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
 * Account Service for MultiversX blockchain
 * Handles account-related operations and queries
 */
class AccountService extends ApiClient
{
    /**
     * Get account details
     *
     * @param string $address Account address
     * @return array Account information
     */
    public function getAccount(string $address): array
    {
        return $this->get("/accounts/{$address}");
    }

    /**
     * Get account balance
     *
     * @param string $address Account address
     * @return string Account balance in atomic units
     */
    public function getBalance(string $address): string
    {
        $account = $this->getAccount($address);
        return $account['balance'] ?? '0';
    }

    /**
     * Get account nonce
     *
     * @param string $address Account address
     * @return int Account nonce
     */
    public function getNonce(string $address): int
    {
        $account = $this->getAccount($address);
        return (int) ($account['nonce'] ?? 0);
    }

    /**
     * Get account transactions
     *
     * @param string $address Account address
     * @param array $options Optional transaction filters
     * @return array List of transactions
     */
    public function getTransactions(string $address, array $options = []): array
    {
        return $this->get("/accounts/{$address}/transactions", $options);
    }

    /**
     * Get account ESDT tokens
     *
     * @param string $address Account address
     * @return array List of ESDT tokens
     */
    public function getESDTTokens(string $address): array
    {
        return $this->get("/accounts/{$address}/esdt");
    }

    /**
     * Get account ESDT balance
     *
     * @param string $address Account address
     * @param string $tokenIdentifier Token identifier
     * @return array ESDT balance
     */
    public function getESDTBalance(string $address, string $tokenIdentifier): array
    {
        return $this->get("/accounts/{$address}/esdt/{$tokenIdentifier}");
    }

    /**
     * Get account NFTs
     *
     * @param string $address Account address
     * @param array $options Optional NFT filters
     * @return array List of NFTs
     */
    public function getNFTs(string $address, array $options = []): array
    {
        return $this->get("/accounts/{$address}/nfts", $options);
    }

    /**
     * Get account stake
     *
     * @param string $address Account address
     * @return array Staking information
     */
    public function getStake(string $address): array
    {
        return $this->get("/accounts/{$address}/stake");
    }
}
