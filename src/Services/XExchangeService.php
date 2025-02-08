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
 * xExchange Service for MultiversX blockchain
 * Handles DEX operations and liquidity management
 */
class XExchangeService extends ApiClient
{
    protected $wrapperAddress = 'erd1qqqqqqqqqqqqqpgqp699jngundfqw07d8jzkepucvpzrvhzz2gls3icpev';
    protected $routerAddress = 'erd1qqqqqqqqqqqqqpgqeel2kumf0r8ffyhth7pqdujjat9nx0862jpsg2pqaq';

    /**
     * Wrap EGLD to WEGLD
     *
     * @param string $amount Amount to wrap
     * @return array Wrap response
     */
    public function wrapEgld(string $amount): array
    {
        return $this->post('/wrap-egld', [
            'amount' => $amount
        ]);
    }

    /**
     * Unwrap WEGLD to EGLD
     *
     * @param string $amount Amount to unwrap
     * @return array Unwrap response
     */
    public function unwrapEgld(string $amount): array
    {
        return $this->post('/unwrap-egld', [
            'amount' => $amount
        ]);
    }

    /**
     * Get pool information
     *
     * @param string $firstToken First token identifier
     * @param string $secondToken Second token identifier
     * @return array Pool information
     */
    public function getPool(string $firstToken, string $secondToken): array
    {
        return $this->get("/pool/{$firstToken}/{$secondToken}");
    }

    /**
     * Get swap quote
     *
     * @param string $tokenIn Input token identifier
     * @param string $tokenOut Output token identifier
     * @param string $amountIn Amount to swap
     * @return array Swap quote
     */
    public function getSwapQuote(
        string $tokenIn,
        string $tokenOut,
        string $amountIn
    ): array {
        return $this->get('/quote', [
            'tokenIn' => $tokenIn,
            'tokenOut' => $tokenOut,
            'amountIn' => $amountIn,
        ]);
    }

    /**
     * Execute swap
     *
     * @param string $tokenIn Input token identifier
     * @param string $tokenOut Output token identifier
     * @param string $amountIn Amount to swap
     * @param string $minAmountOut Minimum output amount
     * @param int $deadline Deadline for swap execution
     * @return array Swap response
     */
    public function swap(
        string $tokenIn,
        string $tokenOut,
        string $amountIn,
        string $minAmountOut,
        int $deadline = null
    ): array {
        if ($deadline === null) {
            $deadline = time() + 3600; // 1 hour from now
        }

        return $this->post('/transaction', [
            'function' => 'swapTokensFixedInput',
            'args' => [
                $tokenIn,
                $tokenOut,
                $amountIn,
                $minAmountOut,
                $deadline
            ],
            'contract' => $this->routerAddress,
        ]);
    }

    /**
     * Add liquidity to a pool
     *
     * @param string $firstToken First token identifier
     * @param string $secondToken Second token identifier
     * @param string $firstAmount First token amount
     * @param string $secondAmount Second token amount
     * @param string $minFirstAmount Minimum first token amount
     * @param string $minSecondAmount Minimum second token amount
     * @param int $deadline Deadline for liquidity addition
     * @return array Liquidity addition response
     */
    public function addLiquidity(
        string $firstToken,
        string $secondToken,
        string $firstAmount,
        string $secondAmount,
        string $minFirstAmount,
        string $minSecondAmount,
        int $deadline = null
    ): array {
        if ($deadline === null) {
            $deadline = time() + 3600;
        }

        return $this->post('/transaction', [
            'function' => 'addLiquidity',
            'args' => [
                $firstToken,
                $secondToken,
                $firstAmount,
                $secondAmount,
                $minFirstAmount,
                $minSecondAmount,
                $deadline
            ],
            'contract' => $this->routerAddress,
        ]);
    }

    /**
     * Remove liquidity from a pool
     *
     * @param string $firstToken First token identifier
     * @param string $secondToken Second token identifier
     * @param string $liquidityAmount Liquidity amount to remove
     * @param string $minFirstAmount Minimum first token amount
     * @param string $minSecondAmount Minimum second token amount
     * @param int $deadline Deadline for liquidity removal
     * @return array Liquidity removal response
     */
    public function removeLiquidity(
        string $firstToken,
        string $secondToken,
        string $liquidityAmount,
        string $minFirstAmount,
        string $minSecondAmount,
        int $deadline = null
    ): array {
        if ($deadline === null) {
            $deadline = time() + 3600;
        }

        return $this->post('/transaction', [
            'function' => 'removeLiquidity',
            'args' => [
                $firstToken,
                $secondToken,
                $liquidityAmount,
                $minFirstAmount,
                $minSecondAmount,
                $deadline
            ],
            'contract' => $this->routerAddress,
        ]);
    }

    /**
     * Get farm information
     *
     * @param string $farmAddress Farm address
     * @return array Farm information
     */
    public function getFarm(string $farmAddress): array
    {
        return $this->get("/farm/{$farmAddress}");
    }

    /**
     * Stake LP tokens in a farm
     *
     * @param string $farmAddress Farm address
     * @param string $lpToken LP token identifier
     * @param string $amount Amount to stake
     * @return array Stake response
     */
    public function stakeFarm(
        string $farmAddress,
        string $lpToken,
        string $amount
    ): array {
        return $this->post('/transaction', [
            'function' => 'enterFarm',
            'args' => [$lpToken, $amount],
            'contract' => $farmAddress,
        ]);
    }

    /**
     * Unstake LP tokens from a farm
     *
     * @param string $farmAddress Farm address
     * @param string $lpToken LP token identifier
     * @param string $amount Amount to unstake
     * @return array Unstake response
     */
    public function unstakeFarm(
        string $farmAddress,
        string $lpToken,
        string $amount
    ): array {
        return $this->post('/transaction', [
            'function' => 'exitFarm',
            'args' => [$lpToken, $amount],
            'contract' => $farmAddress,
        ]);
    }

    /**
     * Claim farm rewards
     *
     * @param string $farmAddress Farm address
     * @return array Claim response
     */
    public function claimFarmRewards(string $farmAddress): array
    {
        return $this->post('/transaction', [
            'function' => 'claimRewards',
            'contract' => $farmAddress,
        ]);
    }
}
