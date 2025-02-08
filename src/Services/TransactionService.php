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
use Exception;

/**
 * Transaction Service for MultiversX blockchain
 * Handles transaction operations and queries
 */
class TransactionService extends ApiClient
{
    /**
     * Send a transaction
     *
     * @param array $transaction Transaction data
     * @return array Transaction response
     */
    public function send(array $transaction): array
    {
        return $this->post('/transactions', $transaction);
    }

    /**
     * Send multiple transactions
     *
     * @param array $transactions Array of transaction data
     * @return array Transactions response
     */
    public function sendMultiple(array $transactions): array
    {
        return $this->post('/transactions/send-multiple', $transactions);
    }

    /**
     * Get transaction status
     *
     * @param string $txHash Transaction hash
     * @return string Transaction status
     */
    public function getTransactionStatus(string $txHash): string
    {
        $response = $this->get("/transactions/{$txHash}/status");
        return $response['status'] ?? 'unknown';
    }

    /**
     * Get transaction details
     *
     * @param string $txHash Transaction hash
     * @return array Transaction details
     */
    public function getTransaction(string $txHash): array
    {
        return $this->get("/transactions/{$txHash}");
    }

    /**
     * Estimate transaction cost
     *
     * @param array $transaction Transaction data
     * @return array Cost estimation
     */
    public function estimateGasCost(array $transaction): array
    {
        return $this->post('/transaction/cost', $transaction);
    }

    /**
     * Get transactions by nonce
     *
     * @param string $sender Sender address
     * @param int $nonce Transaction nonce
     * @return array Transactions response
     */
    public function getTransactionsByNonce(string $sender, int $nonce): array
    {
        return $this->get("/transactions", [
            'sender' => $sender,
            'nonce' => $nonce
        ]);
    }
}
