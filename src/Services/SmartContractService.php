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
 * Smart Contract Service for MultiversX blockchain
 * Handles smart contract operations and interactions
 */
class SmartContractService extends ApiClient
{
    /**
     * Deploy a smart contract
     *
     * @param array $contract Contract deployment data
     * @return array Deployment response
     */
    public function deploy(array $contract): array
    {
        return $this->post('/vm-values/deploy', $contract);
    }

    /**
     * Call a smart contract function
     * 
     * @param string $address Contract address
     * @param array $query Function arguments
     * @return array Function call response
     */
    public function query(string $address, array $query): array
    {
        return $this->post("/vm-values/query/{$address}", $query);
    }

    /**
     * Get smart contract results for a transaction
     *
     * @param string $txHash Transaction hash
     * @return array Contract results
     */
    public function getResults(string $txHash): array
    {
        return $this->get("/vm-values/query/{$txHash}");
    }

    /**
     * Get hex encoded arguments
     *
     * @param array $args Function arguments
     * @return array Hex encoded arguments
     */
    public function getHexEncodedArguments(array $args): array
    {
        return array_map(function ($arg) {
            return bin2hex($arg);
        }, $args);
    }

    /**
     * Compute contract address
     *
     * @param string $owner Owner address
     * @param int $nonce Nonce value
     * @return string Computed contract address
     */
    public function computeAddress(string $owner, int $nonce): string
    {
        $data = [
            'owner' => $owner,
            'nonce' => $nonce
        ];
        $response = $this->post('/address/compute', $data);
        return $response['address'] ?? '';
    }
}
