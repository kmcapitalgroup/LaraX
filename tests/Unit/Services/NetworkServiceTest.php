<?php

namespace Tests\Unit\Services;

use MultiversX\Laravel\Services\NetworkService;
use PHPUnit\Framework\TestCase;
use MultiversX\Laravel\Http\ApiClient;
use GuzzleHttp\Client;

class NetworkServiceTest extends TestCase
{
    protected NetworkService $networkService;
    protected $config;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mock configuration
        $this->config = [
            'network' => 'testnet',
            'networks' => [
                'testnet' => [
                    'api_url' => 'https://testnet-api.multiversx.com'
                ]
            ]
        ];

        // Create NetworkService instance with mocked methods
        $this->networkService = $this->getMockBuilder(NetworkService::class)
            ->setConstructorArgs([$this->config])
            ->onlyMethods(['get', 'post', 'request'])
            ->getMock();
    }

    public function testGetNetworkConfig()
    {
        // Mock expected response
        $expectedResponse = [
            'chainId' => 'T',
            'gasPerDataByte' => 1500,
            'minGasLimit' => 50000,
            'minGasPrice' => 1000000000
        ];

        // Set up mock expectations
        $this->networkService
            ->expects($this->once())
            ->method('get')
            ->with('/network/config')
            ->willReturn($expectedResponse);

        // Call the method
        $result = $this->networkService->getNetworkConfig();

        // Assert the result
        $this->assertEquals($expectedResponse, $result);
    }

    public function testGetNetworkEconomics()
    {
        // Mock expected response
        $expectedResponse = [
            'totalSupply' => '20000000000000000000000000',
            'circulatingSupply' => '19000000000000000000000000',
            'staked' => '10000000000000000000000000',
            'price' => '100.00'
        ];

        // Set up mock expectations
        $this->networkService
            ->expects($this->once())
            ->method('get')
            ->with('/network/economics')
            ->willReturn($expectedResponse);

        // Call the method
        $result = $this->networkService->getNetworkEconomics();

        // Assert the result
        $this->assertEquals($expectedResponse, $result);
    }

    public function testGetShardStatus()
    {
        // Test data
        $shardId = 0;

        // Mock expected response
        $expectedResponse = [
            'currentRound' => 100,
            'currentNonce' => 1000,
            'crossCheckBlockHeight' => 95
        ];

        // Set up mock expectations
        $this->networkService
            ->expects($this->once())
            ->method('get')
            ->with("/network/status/{$shardId}")
            ->willReturn($expectedResponse);

        // Call the method
        $result = $this->networkService->getShardStatus($shardId);

        // Assert the result
        $this->assertEquals($expectedResponse, $result);
    }

    public function testGetLatestBlockNonce()
    {
        // Mock expected response
        $expectedResponse = [
            'status' => [
                'nonce' => 1000
            ]
        ];

        // Set up mock expectations
        $this->networkService
            ->expects($this->once())
            ->method('get')
            ->with('/network/status/4294967295')
            ->willReturn($expectedResponse);

        // Call the method
        $result = $this->networkService->getLatestBlockNonce();

        // Assert the result
        $this->assertEquals(1000, $result);
    }

    public function testGetStats()
    {
        // Mock expected response
        $expectedResponse = [
            'shards' => 3,
            'blocks' => 1000000,
            'accounts' => 500000,
            'transactions' => 2000000
        ];

        // Set up mock expectations
        $this->networkService
            ->expects($this->once())
            ->method('get')
            ->with('/stats')
            ->willReturn($expectedResponse);

        // Call the method
        $result = $this->networkService->getStats();

        // Assert the result
        $this->assertEquals($expectedResponse, $result);
    }

    public function testGetConstants()
    {
        // Mock expected response
        $expectedResponse = [
            'chainId' => 'T',
            'roundDuration' => 6000,
            'epochDuration' => 14400,
            'roundsPerEpoch' => 2400
        ];

        // Set up mock expectations
        $this->networkService
            ->expects($this->once())
            ->method('get')
            ->with('/constants')
            ->willReturn($expectedResponse);

        // Call the method
        $result = $this->networkService->getConstants();

        // Assert the result
        $this->assertEquals($expectedResponse, $result);
    }
}
