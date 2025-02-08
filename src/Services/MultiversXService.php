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
 * Main MultiversX Service
 * Provides access to all MultiversX blockchain functionalities
 */
class MultiversXService extends ApiClient
{
    protected $config;
    protected $account;
    protected $transaction;
    protected $smartContract;
    protected $network;
    protected $wallet;
    protected $xExchange;
    protected $dynamicNft;
    protected $xoxno;
    protected $marketplace;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get Account Service instance
     *
     * @return AccountService
     */
    public function account(): AccountService
    {
        if (!$this->account) {
            $this->account = new AccountService($this->config);
        }
        return $this->account;
    }

    /**
     * Get Transaction Service instance
     *
     * @return TransactionService
     */
    public function transaction(): TransactionService
    {
        if (!$this->transaction) {
            $this->transaction = new TransactionService($this->config);
        }
        return $this->transaction;
    }

    /**
     * Get Smart Contract Service instance
     *
     * @return SmartContractService
     */
    public function smartContract(): SmartContractService
    {
        if (!$this->smartContract) {
            $this->smartContract = new SmartContractService($this->config);
        }
        return $this->smartContract;
    }

    /**
     * Get Network Service instance
     *
     * @return NetworkService
     */
    public function network(): NetworkService
    {
        if (!$this->network) {
            $this->network = new NetworkService($this->config);
        }
        return $this->network;
    }

    /**
     * Get Wallet Service instance
     *
     * @return WalletService
     */
    public function wallet(): WalletService
    {
        if (!$this->wallet) {
            $this->wallet = new WalletService($this->config);
        }
        return $this->wallet;
    }

    /**
     * Get xExchange Service instance
     *
     * @return XExchangeService
     */
    public function xExchange(): XExchangeService
    {
        if (!$this->xExchange) {
            $this->xExchange = new XExchangeService($this->config);
        }
        return $this->xExchange;
    }

    /**
     * Get Dynamic NFT Service instance
     *
     * @return DynamicNftService
     */
    public function dynamicNft(): DynamicNftService
    {
        if (!$this->dynamicNft) {
            $this->dynamicNft = new DynamicNftService($this->config);
        }
        return $this->dynamicNft;
    }

    /**
     * Get XOXNO Marketplace Service instance
     *
     * @return XoxnoService
     */
    public function xoxno(): XoxnoService
    {
        if (!$this->xoxno) {
            $this->xoxno = new XoxnoService($this->config);
        }
        return $this->xoxno;
    }

    /**
     * Get Custom Marketplace Service instance
     *
     * @return MarketplaceService
     */
    public function marketplace(): MarketplaceService
    {
        if (!$this->marketplace) {
            $this->marketplace = new MarketplaceService($this->config);
        }
        return $this->marketplace;
    }
}
