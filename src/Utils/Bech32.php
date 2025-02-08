<?php
/**
 * MultiversX Laravel SDK
 *
 * @package   KMCPG\MultiversX
 * @author    KMCPG
 * @copyright Copyright (c) 2024-present KMCPG
 * @license   MIT License
 */

namespace MultiversX\Laravel\Utils;

/**
 * Bech32 encoding/decoding utility class
 * Implements the Bech32 address format for MultiversX blockchain
 */
class Bech32
{
    const CHARSET = 'qpzry9x8gf2tvdw0s3jn54khce6mua7l';
    const CHARSET_REV = [
        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
        15, -1, 10, 17, 21, 20, 26, 30,  7,  5, -1, -1, -1, -1, -1, -1,
        -1, 29, -1, 24, 13, 25,  9,  8, 23, -1, 18, 22, 31, 27, 19, -1,
         1,  0,  3, 16, 11, 28, 12, 14,  6,  4,  2, -1, -1, -1, -1, -1,
        -1, 29, -1, 24, 13, 25,  9,  8, 23, -1, 18, 22, 31, 27, 19, -1,
         1,  0,  3, 16, 11, 28, 12, 14,  6,  4,  2, -1, -1, -1, -1, -1
    ];

    /**
     * Encode bytes to bech32
     *
     * @param string $hrp Human-readable part
     * @param array $data Data bytes to encode
     * @return string Encoded bech32 string
     */
    public static function encode(string $hrp, array $data): string
    {
        $chk = self::createChecksum($hrp, $data);
        $combined = array_merge($data, $chk);
        
        $encoded = $hrp . '1';
        for ($i = 0; $i < count($combined); $i++) {
            $encoded .= self::CHARSET[$combined[$i]];
        }
        
        return $encoded;
    }

    /**
     * Decode a bech32 string
     *
     * @param string $str String to decode
     * @return array|null Array containing hrp and data if valid, null if invalid
     */
    public static function decode(string $str): ?array
    {
        if (strlen($str) < 8 || strlen($str) > 90) {
            return null;
        }
        
        $str = strtolower($str);
        $pos = strrpos($str, '1');
        if ($pos === false) {
            return null;
        }
        
        $hrp = substr($str, 0, $pos);
        $data = [];
        
        for ($i = $pos + 1; $i < strlen($str); $i++) {
            $rev = self::CHARSET_REV[ord($str[$i])];
            if ($rev === -1) {
                return null;
            }
            $data[] = $rev;
        }
        
        if (!self::verifyChecksum($hrp, $data)) {
            return null;
        }
        
        return [
            'hrp' => $hrp,
            'data' => array_slice($data, 0, -6)
        ];
    }

    /**
     * Convert hex address to bech32
     *
     * @param string $hrp Human-readable part
     * @param string $hex_address Hex address to convert
     * @return string Bech32 encoded address
     */
    public static function encode_address(string $hrp, string $hex_address): string
    {
        $address_bytes = array_values(unpack('C*', hex2bin($hex_address)));
        $data = self::convertBits($address_bytes, 8, 5, true);
        return self::encode($hrp, $data);
    }

    /**
     * Convert bech32 address to hex
     *
     * @param string $address Bech32 address to convert
     * @return string|null Hex address if valid, null if invalid
     */
    public static function decode_address(string $address): ?string
    {
        $decoded = self::decode($address);
        if ($decoded === null) {
            return null;
        }
        
        $converted = self::convertBits($decoded['data'], 5, 8, false);
        if ($converted === null) {
            return null;
        }
        
        return bin2hex(pack('C*', ...$converted));
    }

    /**
     * Convert bits from one base to another
     *
     * @param array $data Data to convert
     * @param int $from Source bits per character
     * @param int $to Target bits per character
     * @param bool $pad Whether to pad result
     * @return array|null Converted data if valid, null if invalid
     */
    private static function convertBits(array $data, int $from, int $to, bool $pad): ?array
    {
        $acc = 0;
        $bits = 0;
        $ret = [];
        $maxv = (1 << $to) - 1;
        
        foreach ($data as $value) {
            if ($value < 0 || ($value >> $from) !== 0) {
                return null;
            }
            $acc = ($acc << $from) | $value;
            $bits += $from;
            while ($bits >= $to) {
                $bits -= $to;
                $ret[] = ($acc >> $bits) & $maxv;
            }
        }
        
        if ($pad) {
            if ($bits > 0) {
                $ret[] = ($acc << ($to - $bits)) & $maxv;
            }
        } else if ($bits >= $from || ((($acc << ($to - $bits)) & $maxv) !== 0)) {
            return null;
        }
        
        return $ret;
    }

    /**
     * Create checksum for HRP and data
     *
     * @param string $hrp Human-readable part
     * @param array $data Data bytes
     * @return array Checksum bytes
     */
    private static function createChecksum(string $hrp, array $data): array
    {
        $values = array_merge(self::hrpExpand($hrp), $data);
        $polymod = self::polymod(array_merge($values, [0, 0, 0, 0, 0, 0])) ^ 1;
        $ret = [];
        for ($i = 0; $i < 6; $i++) {
            $ret[$i] = ($polymod >> 5 * (5 - $i)) & 31;
        }
        return $ret;
    }

    /**
     * Verify checksum for HRP and data
     *
     * @param string $hrp Human-readable part
     * @param array $data Data with checksum
     * @return bool True if valid
     */
    private static function verifyChecksum(string $hrp, array $data): bool
    {
        return self::polymod(array_merge(self::hrpExpand($hrp), $data)) === 1;
    }

    /**
     * Expand HRP into values for checksum computation
     *
     * @param string $hrp Human-readable part
     * @return array Expanded values
     */
    private static function hrpExpand(string $hrp): array
    {
        $ret = [];
        $pos = 0;
        
        for ($i = 0; $i < strlen($hrp); $i++) {
            $ret[$pos++] = ord($hrp[$i]) >> 5;
        }
        
        $ret[$pos++] = 0;
        
        for ($i = 0; $i < strlen($hrp); $i++) {
            $ret[$pos++] = ord($hrp[$i]) & 31;
        }
        
        return $ret;
    }

    /**
     * Calculate polymod for checksum verification
     *
     * @param array $values Input values
     * @return int Polymod result
     */
    private static function polymod(array $values): int
    {
        $generator = [0x3b6a57b2, 0x26508e6d, 0x1ea119fa, 0x3d4233dd, 0x2a1462b3];
        $chk = 1;
        
        foreach ($values as $value) {
            $top = $chk >> 25;
            $chk = ($chk & 0x1ffffff) << 5 ^ $value;
            for ($i = 0; $i < 5; $i++) {
                if (($top >> $i) & 1) {
                    $chk ^= $generator[$i];
                }
            }
        }
        
        return $chk;
    }
}
