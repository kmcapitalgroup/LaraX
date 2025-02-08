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

        // Create WalletService instance with mocked methods
        $this->walletService = $this->getMockBuilder(WalletService::class)
            ->setConstructorArgs([$this->config])
            ->onlyMethods(['get', 'post', 'request', 'getAddressFromMnemonic', 'validateMnemonic', 'generatePrivateKey'])
            ->getMock();
    }

    public function testCreateWallet()
    {
        // Mock data
        $expectedResponse = [
            'mnemonic' => 'test mnemonic',
            'address' => 'erd1test'
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
    }

    public function testImportFromMnemonic()
    {
        // Test data
        $mnemonic = 'test mnemonic phrase';
        $expectedResponse = [
            'mnemonic' => $mnemonic,
            'address' => 'erd1test'
        ];

        // Mock validateMnemonic method
        $this->walletService
            ->expects($this->once())
            ->method('validateMnemonic')
            ->with($mnemonic)
            ->willReturn(true);

        // Mock getAddressFromMnemonic method
        $this->walletService
            ->expects($this->once())
            ->method('getAddressFromMnemonic')
            ->with($mnemonic)
            ->willReturn($expectedResponse['address']);

        // Call the method
        $result = $this->walletService->importFromMnemonic($mnemonic);

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
        $mnemonic = 'test mnemonic phrase';
        $password = 'testPassword';
        $expectedResponse = [
            'keystore' => [
                'version' => 4,
                'id' => 'test-uuid',
                'address' => 'erd1test'
            ],
            'address' => 'erd1test'
        ];

        // Mock validateMnemonic method
        $this->walletService
            ->expects($this->once())
            ->method('validateMnemonic')
            ->with($mnemonic)
            ->willReturn(true);

        // Mock generatePrivateKey method
        $this->walletService
            ->expects($this->once())
            ->method('generatePrivateKey')
            ->with($mnemonic)
            ->willReturn('testPrivateKey');

        // Call the method
        $result = $this->walletService->createKeystore($mnemonic, $password);

        // Assert the result structure
        $this->assertArrayHasKey('keystore', $result);
        $this->assertArrayHasKey('address', $result);
        $this->assertArrayHasKey('version', $result['keystore']);
        $this->assertArrayHasKey('id', $result['keystore']);
        $this->assertArrayHasKey('address', $result['keystore']);
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
