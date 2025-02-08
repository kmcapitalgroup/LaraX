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

/**
 * Network Service for MultiversX blockchain
 * Handles network-related operations and information
 */
class NetworkService extends ApiClient
{
    /**
     * Get network configuration
     *
     * @return array Network configuration data
     */
    public function getNetworkConfig(): array
    {
        return $this->get('/network/config');
    }

    /**
     * Get network economics data
     *
     * @return array Network economics information
     */
    public function getNetworkEconomics(): array
    {
        return $this->get('/network/economics');
    }

    /**
     * Get shard status
     *
     * @param int $shardId Shard identifier
     * @return array Shard status information
     */
    public function getShardStatus(int $shardId): array
    {
        return $this->get("/network/status/{$shardId}");
    }

    /**
     * Get latest block nonce
     *
     * @return int Latest block nonce
     */
    public function getLatestBlockNonce(): int
    {
        $response = $this->get('/network/status/4294967295');
        return (int) ($response['status']['nonce'] ?? 0);
    }

    /**
     * Get stats
     *
     * @return array Stats data
     */
    public function getStats(): array
    {
        return $this->get('/stats');
    }

    /**
     * Get constants
     *
     * @return array Constants data
     */
    public function getConstants(): array
    {
        return $this->get('/constants');
    }
}
