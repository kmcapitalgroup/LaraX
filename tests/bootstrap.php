<?php

namespace ParagonIE\Sodium {
    class Compat {
        const CRYPTO_PWHASH_ALG_DEFAULT = 2;
        const CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE = 4;
        const CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE = 33554432;

        public static function crypto_pwhash($length, $password, $salt, $opslimit, $memlimit, $alg) {
            return str_repeat('0', $length);
        }

        public static function crypto_aead_xchacha20poly1305_ietf_encrypt($message, $additional_data, $nonce, $key) {
            return str_repeat('0', strlen($message) + 16); // Add 16 bytes for the authentication tag
        }
    }
}

namespace {
    // Mock Web3\HDWallet\Mnemonic class
    if (!class_exists('Web3\HDWallet\Mnemonic')) {
        class Mnemonic {
            public function generate($words = 24) {
                return 'test test test test test test test test test test test test test test test test test test test test test test test test';
            }
        }
        class_alias('Mnemonic', 'Web3\HDWallet\Mnemonic');
    }

    // Mock sodium functions if they don't exist
    if (!function_exists('random_bytes')) {
        function random_bytes($length) {
            return str_repeat('0', $length);
        }
    }

    if (!function_exists('sodium_crypto_pwhash')) {
        function sodium_crypto_pwhash($length, $password, $salt, $opslimit, $memlimit, $alg) {
            return ParagonIE\Sodium\Compat::crypto_pwhash($length, $password, $salt, $opslimit, $memlimit, $alg);
        }
    }

    if (!function_exists('sodium_crypto_aead_xchacha20poly1305_ietf_encrypt')) {
        function sodium_crypto_aead_xchacha20poly1305_ietf_encrypt($message, $additional_data, $nonce, $key) {
            return ParagonIE\Sodium\Compat::crypto_aead_xchacha20poly1305_ietf_encrypt($message, $additional_data, $nonce, $key);
        }
    }

    // Mock kornrunner\Keccak if it doesn't exist
    if (!class_exists('kornrunner\Keccak')) {
        class Keccak {
            public static function hash($message, $bits) {
                return str_repeat('0', $bits / 8);
            }
        }
        class_alias('Keccak', 'kornrunner\Keccak');
    }

    // Mock Elliptic\EC if it doesn't exist
    if (!class_exists('Elliptic\EC')) {
        class EC {
            public function __construct($curve) {}
            public function keyFromPrivate($key) {
                return new ECKeyPair();
            }
        }
        class ECKeyPair {
            public function getPublic($compact = false, $enc = 'hex') {
                return str_repeat('0', 64);
            }
        }
        class_alias('EC', 'Elliptic\EC');
    }
}
