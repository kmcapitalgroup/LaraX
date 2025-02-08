<?php
/**
 * MultiversX Laravel SDK
 *
 * @package   KMCPG\MultiversX
 * @author    KMCPG
 * @copyright Copyright (c) 2024-present KMCPG
 * @license   MIT License
 */

namespace MultiversX\Laravel\Services;

use MultiversX\Laravel\Http\ApiClient;
use MultiversX\Laravel\Utils\Bech32;
use Exception;
use Web3\HDWallet\Mnemonic;
use Web3\HDWallet\BIP44;
use ParagonIE\Sodium\Compat;
use Elliptic\EC;
use kornrunner\Keccak;

/**
 * Wallet Service for MultiversX blockchain
 * Handles wallet creation, import, and management with secure cryptographic operations
 */
class WalletService extends ApiClient
{
    const CIPHER_ALGORITHM = 'aes-128-ctr';
    const KDF_ALGORITHM = 'scrypt';
    const DERIVATION_PATH = "m/44'/508'/0'/0/0"; // MultiversX derivation path
    
    protected $ec;
    
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->ec = new EC('secp256k1');
    }

    /**
     * Create a new wallet with a mnemonic phrase
     *
     * @return array Array containing mnemonic and address
     */
    public function createWallet(): array
    {
        $mnemonic = new Mnemonic();
        $words = $mnemonic->generate(24); // 24 words for enhanced security
        
        return [
            'mnemonic' => $words,
            'address' => $this->getAddressFromMnemonic($words)
        ];
    }

    /**
     * Import a wallet using a mnemonic phrase
     *
     * @param string $mnemonic The mnemonic phrase to import
     * @return array Array containing mnemonic and address
     * @throws Exception If mnemonic is invalid
     */
    public function importFromMnemonic(string $mnemonic): array
    {
        if (!$this->validateMnemonic($mnemonic)) {
            throw new Exception('Invalid mnemonic phrase');
        }

        return [
            'mnemonic' => $mnemonic,
            'address' => $this->getAddressFromMnemonic($mnemonic)
        ];
    }

    /**
     * Create a keystore from a mnemonic phrase
     *
     * @param string $mnemonic The mnemonic phrase to encrypt
     * @param string $password Password to encrypt the keystore
     * @return array Array containing keystore and address
     * @throws Exception If mnemonic is invalid
     */
    public function createKeystore(string $mnemonic, string $password): array
    {
        if (!$this->validateMnemonic($mnemonic)) {
            throw new Exception('Invalid mnemonic phrase');
        }

        $salt = random_bytes(32);
        $iv = random_bytes(16);
        
        // Key derivation using Sodium (libsodium)
        $key = Compat::crypto_pwhash(
            32,
            $password,
            $salt,
            Compat::CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
            Compat::CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE,
            Compat::CRYPTO_PWHASH_ALG_DEFAULT
        );
        
        // Encrypt mnemonic phrase using Sodium
        $encrypted = Compat::crypto_aead_xchacha20poly1305_ietf_encrypt(
            $mnemonic,
            '',
            $iv,
            $key
        );
        
        $keystore = [
            'version' => 4,
            'id' => $this->generateUuid(),
            'kind' => 'mnemonic',
            'crypto' => [
                'ciphertext' => bin2hex($encrypted),
                'cipherparams' => [
                    'iv' => bin2hex($iv)
                ],
                'cipher' => 'xchacha20poly1305',
                'kdf' => 'argon2id',
                'kdfparams' => [
                    'salt' => bin2hex($salt),
                    'opslimit' => Compat::CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
                    'memlimit' => Compat::CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE,
                    'algorithm' => Compat::CRYPTO_PWHASH_ALG_DEFAULT
                ]
            ]
        ];

        return [
            'keystore' => $keystore,
            'address' => $this->getAddressFromMnemonic($mnemonic)
        ];
    }

    /**
     * Decrypt a keystore
     *
     * @param array $keystore The keystore to decrypt
     * @param string $password Password to decrypt the keystore
     * @return array Array containing mnemonic and address
     * @throws Exception If password is invalid or keystore is corrupted
     */
    public function decryptKeystore(array $keystore, string $password): array
    {
        if ($keystore['version'] !== 4) {
            throw new Exception('Unsupported keystore version');
        }

        $crypto = $keystore['crypto'];
        $salt = hex2bin($crypto['kdfparams']['salt']);
        
        // Key derivation using Sodium
        $key = Compat::crypto_pwhash(
            32,
            $password,
            $salt,
            $crypto['kdfparams']['opslimit'],
            $crypto['kdfparams']['memlimit'],
            $crypto['kdfparams']['algorithm']
        );

        // Decryption using Sodium
        $iv = hex2bin($crypto['cipherparams']['iv']);
        $ciphertext = hex2bin($crypto['ciphertext']);
        
        try {
            $mnemonic = Compat::crypto_aead_xchacha20poly1305_ietf_decrypt(
                $ciphertext,
                '',
                $iv,
                $key
            );
        } catch (Exception $e) {
            throw new Exception('Invalid password');
        }

        if (!$this->validateMnemonic($mnemonic)) {
            throw new Exception('Corrupted keystore: invalid mnemonic');
        }

        return [
            'mnemonic' => $mnemonic,
            'address' => $this->getAddressFromMnemonic($mnemonic)
        ];
    }

    /**
     * Validate a MultiversX address
     *
     * @param string $address The address to validate
     * @return bool True if address is valid
     */
    public function validateAddress(string $address): bool
    {
        if (strpos($address, 'erd1') !== 0) {
            return false;
        }

        try {
            $decoded = Bech32::decode_address($address);
            return $decoded !== null && strlen($decoded) === 40;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Validate a mnemonic phrase
     *
     * @param string $mnemonic The mnemonic to validate
     * @return bool True if mnemonic is valid
     */
    protected function validateMnemonic(string $mnemonic): bool
    {
        $mnemonicObj = new Mnemonic();
        return $mnemonicObj->validate($mnemonic);
    }

    /**
     * Get address from mnemonic phrase
     *
     * @param string $mnemonic The mnemonic to derive address from
     * @return string The derived MultiversX address
     */
    protected function getAddressFromMnemonic(string $mnemonic): string
    {
        // Create HD wallet
        $hdWallet = new BIP44($mnemonic);
        
        // Derive MultiversX path
        $keyPair = $hdWallet->derivePath(self::DERIVATION_PATH);
        
        // Generate public key
        $publicKey = $this->ec->keyFromPrivate($keyPair->getPrivateKey())->getPublic();
        
        // Calculate MultiversX address (erd1...)
        $pubKeyBytes = hex2bin($publicKey->encode('hex'));
        $hash = Keccak::hash($pubKeyBytes, 256);
        $addressHex = substr($hash, -40);
        
        // Encode to bech32 with 'erd' prefix
        return Bech32::encode_address('erd', $addressHex);
    }

    /**
     * Generate a UUID v4
     *
     * @return string Generated UUID
     */
    protected function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Generate a token for authentication sessions
     *
     * @return string Generated token
     */
    protected function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Initialize xPortal login
     *
     * @return array Array containing login data
     */
    public function initXPortalLogin(): array
    {
        return $this->post('/auth/xportal/init', [
            'token' => $this->generateToken(),
            'callbackUrl' => config('multiversx.xportal.callback_url')
        ]);
    }

    /**
     * Check xPortal login status
     *
     * @param string $token The token to check
     * @return array Array containing login status
     */
    public function checkXPortalLogin(string $token): array
    {
        return $this->get('/auth/xportal/status', [
            'token' => $token
        ]);
    }

    /**
     * Initialize Web Wallet login
     *
     * @return array Array containing login data
     */
    public function initWebWalletLogin(): array
    {
        return $this->post('/auth/web-wallet/init', [
            'token' => $this->generateToken(),
            'callbackUrl' => config('multiversx.web_wallet.callback_url')
        ]);
    }

    /**
     * Check Web Wallet login status
     *
     * @param string $token The token to check
     * @return array Array containing login status
     */
    public function checkWebWalletLogin(string $token): array
    {
        return $this->get('/auth/web-wallet/status', [
            'token' => $token
        ]);
    }

    /**
     * Initialize Ledger login
     *
     * @return array Array containing login data
     */
    public function initLedgerLogin(): array
    {
        return $this->post('/auth/ledger/init', [
            'token' => $this->generateToken()
        ]);
    }

    /**
     * Check Ledger login status
     *
     * @param string $token The token to check
     * @return array Array containing login status
     */
    public function checkLedgerLogin(string $token): array
    {
        return $this->get('/auth/ledger/status', [
            'token' => $token
        ]);
    }

    /**
     * Logout
     *
     * @return array Array containing logout data
     */
    public function logout(): array
    {
        return $this->post('/auth/logout');
    }
}
