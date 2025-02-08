<?php

// Define required constants for sodium
if (!defined('ParagonIE\Sodium\Compat::CRYPTO_PWHASH_ALG_DEFAULT')) {
    define('ParagonIE\Sodium\Compat::CRYPTO_PWHASH_ALG_DEFAULT', 2);
}
if (!defined('ParagonIE\Sodium\Compat::CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE')) {
    define('ParagonIE\Sodium\Compat::CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE', 4);
}
if (!defined('ParagonIE\Sodium\Compat::CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE')) {
    define('ParagonIE\Sodium\Compat::CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE', 33554432);
}

// Mock Web3\HDWallet\Mnemonic class
class_alias(\PHPUnit\Framework\TestCase::class, 'Web3\HDWallet\Mnemonic');

// Mock sodium functions if they don't exist
if (!function_exists('random_bytes')) {
    function random_bytes($length) {
        return str_repeat('0', $length);
    }
}

if (!function_exists('sodium_crypto_pwhash')) {
    function sodium_crypto_pwhash($length, $password, $salt, $opslimit, $memlimit, $alg) {
        return str_repeat('0', $length);
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
