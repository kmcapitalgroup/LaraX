<?php

namespace Tests\Unit\Services;

use MultiversX\Laravel\Services\WalletService;
use PHPUnit\Framework\TestCase;
use MultiversX\Laravel\Http\ApiClient;
use GuzzleHttp\Client;
use ParagonIE\Sodium\Compat;

class WalletServiceTest extends TestCase
{
    protected WalletService $walletService;
    protected $config;
    protected $mockMnemonic;
    protected $testMnemonic;
    protected $testAddress = 'erd1test';

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

        $this->testMnemonic = 'test test test test test test test test test test test test test test test test test test test test test test test test';

        // Create WalletService instance with mocked methods
        $this->walletService = $this->getMockBuilder(WalletService::class)
            ->setConstructorArgs([$this->config])
            ->onlyMethods(['get', 'post', 'request', 'getAddressFromMnemonic', 'validateMnemonic'])
            ->getMock();
    }

    public function testCreateWallet()
    {
        // Mock data
        $expectedResponse = [
            'mnemonic' => $this->testMnemonic,
            'address' => $this->testAddress
        ];

        // Mock getAddressFromMnemonic method
        $this->walletService
            ->expects($this->once())
            ->method('getAddressFromMnemonic')
            ->with($expectedResponse['mnemonic'])
            ->willReturn($expectedResponse['address']);

        // Call the method
        $result = $this->walletService->createWallet();

        // Assert the result
        $this->assertArrayHasKey('mnemonic', $result);
        $this->assertArrayHasKey('address', $result);
        $this->assertEquals($expectedResponse['address'], $result['address']);
        $this->assertEquals($expectedResponse['mnemonic'], $result['mnemonic']);
    }

    public function testImportFromMnemonic()
    {
        // Test data
        $expectedResponse = [
            'mnemonic' => $this->testMnemonic,
            'address' => $this->testAddress
        ];

        // Mock validateMnemonic method
        $this->walletService
            ->expects($this->once())
            ->method('validateMnemonic')
            ->with($this->testMnemonic)
            ->willReturn(true);

        // Mock getAddressFromMnemonic method
        $this->walletService
            ->expects($this->once())
            ->method('getAddressFromMnemonic')
            ->with($this->testMnemonic)
            ->willReturn($expectedResponse['address']);

        // Call the method
        $result = $this->walletService->importFromMnemonic($this->testMnemonic);

        // Assert the result
        $this->assertEquals($expectedResponse, $result);
    }

    public function testImportFromMnemonicWithInvalidMnemonic()
    {
        // Test data
        $mnemonic = 'invalid mnemonic phrase';

        // Mock validateMnemonic method
        $this->walletService
            ->expects($this->once())
            ->method('validateMnemonic')
            ->with($mnemonic)
            ->willReturn(false);

        // Expect exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid mnemonic phrase');

        // Call the method
        $this->walletService->importFromMnemonic($mnemonic);
    }

    public function testCreateKeystore()
    {
        // Test data
        $password = 'testPassword';

        // Mock validateMnemonic method
        $this->walletService
            ->expects($this->once())
            ->method('validateMnemonic')
            ->with($this->testMnemonic)
            ->willReturn(true);

        // Mock getAddressFromMnemonic method
        $this->walletService
            ->expects($this->once())
            ->method('getAddressFromMnemonic')
            ->with($this->testMnemonic)
            ->willReturn($this->testAddress);

        // Call the method
        $result = $this->walletService->createKeystore($this->testMnemonic, $password);

        // Assert the result structure
        $this->assertArrayHasKey('keystore', $result);
        $this->assertArrayHasKey('address', $result);
        $this->assertEquals($this->testAddress, $result['address']);
        $this->assertArrayHasKey('version', $result['keystore']);
        $this->assertArrayHasKey('id', $result['keystore']);
        $this->assertArrayHasKey('crypto', $result['keystore']);
    }

    public function testCreateKeystoreWithInvalidMnemonic()
    {
        // Test data
        $mnemonic = 'invalid mnemonic phrase';
        $password = 'testPassword';

        // Mock validateMnemonic method
        $this->walletService
            ->expects($this->once())
            ->method('validateMnemonic')
            ->with($mnemonic)
            ->willReturn(false);

        // Expect exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid mnemonic phrase');

        // Call the method
        $this->walletService->createKeystore($mnemonic, $password);
    }
}
