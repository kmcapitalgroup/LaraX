# MultiversX Laravel SDK

Complete Laravel SDK for the MultiversX ecosystem with support for xExchange, Dynamic NFTs, and XOXNO.

## Installation

```bash
composer require kmcpg/sdklaramvx
```

## Features

### 1. Wallet Management
- **Creation and Import**
  - Create new wallets with mnemonic phrase (24 words)
  - Import via mnemonic phrase
  - Keystore management (creation, encryption, decryption)
  - BIP39 support for mnemonic phrases
  - BIP44 support for address derivation

- **Connection Methods**
  - xPortal (mobile app)
  - Web Wallet (browser wallet)
  - Ledger (hardware wallet)
  - Session management and logout

- **Security**
  - XChaCha20-Poly1305 encryption
  - Argon2id key derivation
  - bech32 address support
  - Cryptographic address validation

```php
// Create a new wallet
$wallet = MultiversX::wallet()->createWallet();
echo $wallet['mnemonic']; // Save this securely
echo $wallet['address'];  // Wallet address

// Import from mnemonic
$imported = MultiversX::wallet()->importFromMnemonic(
    'word1 word2 word3 ... word24'
);

// Create keystore
$keystore = MultiversX::wallet()->createKeystore(
    'word1 word2 word3 ... word24',
    'strongPassword123'
);

// Connect with xPortal
$xportal = MultiversX::wallet()->initXPortalLogin();
$status = MultiversX::wallet()->checkXPortalLogin($xportal['token']);

// Connect with Web Wallet
$webWallet = MultiversX::wallet()->initWebWalletLogin();
$status = MultiversX::wallet()->checkWebWalletLogin($webWallet['token']);

// Connect with Ledger
$ledger = MultiversX::wallet()->initLedgerLogin();
$status = MultiversX::wallet()->checkLedgerLogin($ledger['token']);

// Logout
MultiversX::wallet()->logout();
```

### 2. Basic Operations
- **Account Management**
  - Balance checking
  - Transaction history
  - Token management (ESDT)
  - Nonces and signatures

- **Transactions**
  - Creation and sending
  - Multi-signature
  - Status tracking
  - Fee estimation

- **Smart Contracts**
  - Deployment
  - Function calls
  - Read queries
  - ABI management

```php
// Check balance
$balance = MultiversX::account()->getBalance('erd1...');
$tokens = MultiversX::account()->getESDTTokens('erd1...');

// Send transaction
$transaction = [
    'nonce' => 1,
    'value' => '1000000000000000000', // 1 EGLD
    'receiver' => 'erd1...',
    'sender' => 'erd1...',
    'gasPrice' => 1000000000,
    'gasLimit' => 50000,
    'data' => base64_encode('hello'),
    'chainID' => '1',
    'version' => 1
];
$hash = MultiversX::transaction()->send($transaction);

// Deploy smart contract
$contract = [
    'code' => '0x...',
    'arguments' => ['arg1', 'arg2'],
    'gasLimit' => 500000
];
$result = MultiversX::smartContract()->deploy($contract);
```

### 3. xExchange (DEX)
- **Basic Operations**
  - Wrap/unwrap EGLD
  - Token swaps
  - Add/remove liquidity
  - Price and exchange rates

- **Farming and Staking**
  - LP token deposits
  - Reward harvesting
  - APR/APY checking
  - Token locking

```php
// Wrap EGLD
$wrapTx = MultiversX::xExchange()->wrapEgld('1000000000000000000');

// Swap tokens
$quote = MultiversX::xExchange()->getSwapQuote(
    'WEGLD-bd4d79',
    'MEX-455c57',
    '1000000000000000000'
);
$swap = MultiversX::xExchange()->swap(
    'WEGLD-bd4d79',
    'MEX-455c57',
    '1000000000000000000',
    $quote['minAmountOut']
);

// Add liquidity
$addLiquidity = MultiversX::xExchange()->addLiquidity(
    'WEGLD-bd4d79',
    'MEX-455c57',
    '1000000000000000000',
    '1000000000000000000',
    '990000000000000000',
    '990000000000000000'
);

// Farm staking
$stakeFarm = MultiversX::xExchange()->stakeFarm(
    'erd1qqqqqqqqqqqqqpgqeel2kumf0r8ffyhth7pqdujjat9nx0862jpsg2pqaq',
    'LPTOK-abcdef',
    '1000000000000000000'
);
```

### 4. NFTs and Tokens
- **Standard NFTs**
  - Collection creation
  - NFT minting
  - Transfers and burning
  - Royalty management

- **Dynamic NFTs**
  - Dynamic collection creation
  - Attribute updates
  - URI modifications
  - Metadata evolution

- **SFTs (Semi-Fungible Tokens)**
  - Creation and management
  - Multiple quantity support
  - Partial transfers
  - Custom metadata

- **Meta ESDT**
  - Meta ESDT token creation
  - Property management
  - Unlock scheduling
  - Attribute updates

```php
// Create NFT collection
$collection = MultiversX::nft()->createCollection(
    'MyCollection',
    'MYCOL',
    ['canFreeze' => true, 'canWipe' => true]
);

// Create dynamic NFT
$nft = MultiversX::dynamicNft()->createDynamicNft(
    'MYCOL',
    'My NFT',
    ['level' => 1, 'power' => 100],
    ['https://api.mysite.com/nft/1.json'],
    500 // 5% royalties
);

// Update NFT attributes
MultiversX::dynamicNft()->updateAttributes(
    'MYCOL',
    '1',
    ['level' => 2, 'power' => 150]
);

// Create SFT
$sft = MultiversX::nft()->createSft(
    'MYCOL',
    'My SFT',
    1000, // quantity
    ['rarity' => 'rare']
);

// Create Meta ESDT
$metaEsdt = MultiversX::nft()->createMetaEsdt(
    'MYCOL',
    'My Meta Token',
    10000, // quantity
    ['unlockEpoch' => 100]
);
```

### 5. XOXNO Marketplace
- **Listings and Sales**
  - Listing creation
  - Price management
  - Offer system
  - Direct sales

- **Collections**
  - Real-time statistics
  - Floor price
  - Recent sales
  - Verified collections

- **Search and Filters**
  - Attribute search
  - Price filters
  - Sorting and pagination
  - Transaction history

```php
// List NFT for sale
$listing = MultiversX::xoxno()->listNft(
    'MYCOL-abcdef',
    '1',
    '1000000000000000000', // 1 EGLD
    'WEGLD-bd4d79' // Optional, EGLD by default
);

// Buy NFT
$purchase = MultiversX::xoxno()->buyNft(
    'MYCOL-abcdef',
    '1',
    '1000000000000000000'
);

// Make offer
$offer = MultiversX::xoxno()->makeOffer(
    'MYCOL-abcdef',
    '1',
    '900000000000000000', // 0.9 EGLD
    time() + 86400 // Expires in 24h
);

// Get collection stats
$stats = MultiversX::xoxno()->getCollectionStats('MYCOL-abcdef');
$floor = MultiversX::xoxno()->getCollectionFloor('MYCOL-abcdef');
$recentSales = MultiversX::xoxno()->getRecentSales('MYCOL-abcdef', 5);

// Search NFTs
$searchResults = MultiversX::xoxno()->searchNfts([
    'collection' => 'MYCOL-abcdef',
    'traits' => [
        'background' => 'blue',
        'rarity' => 'legendary'
    ],
    'minPrice' => '100000000000000000',
    'maxPrice' => '2000000000000000000'
]);
```

### 6. Custom Marketplace
- **Configuration**
  - Custom deployment
  - Fee management
  - Accepted token configuration
  - Administration and moderation

- **Multi-Token Support**
  - Standard NFTs
  - Dynamic NFTs
  - SFTs
  - Meta ESDT

- **Advanced Features**
  - Offer system
  - Royalty management
  - Detailed statistics
  - Complete history

```php
// Deploy custom marketplace
$marketplace = MultiversX::marketplace()->deployMarketplace(
    'erd1...', // Owner address
    250        // 2.5% fee
);

// Configure accepted tokens
MultiversX::marketplace()->setAcceptedTokens(
    $marketplace['address'],
    [
        MarketplaceService::TOKEN_TYPE_NFT,
        MarketplaceService::TOKEN_TYPE_SFT,
        MarketplaceService::TOKEN_TYPE_META
    ]
);

// List token
$listing = MultiversX::marketplace()->listToken(
    $marketplace['address'],
    'MYCOL-abcdef',
    '1',
    '1000000000000000000', // 1 EGLD
    1,                     // Quantity (1 for NFT, >1 for SFT/Meta)
    'WEGLD-bd4d79'
);

// Buy token
$purchase = MultiversX::marketplace()->buyToken(
    $marketplace['address'],
    'MYCOL-abcdef',
    '1',
    '1000000000000000000',
    5                     // Buy 5 tokens (for SFT/Meta)
);

// Update dynamic token metadata
MultiversX::marketplace()->updateDynamicTokenMetadata(
    $marketplace['address'],
    'MYCOL-abcdef',
    '1',
    [
        'name' => 'Updated Name',
        'attributes' => [
            'level' => '2',
            'power' => '150'
        ]
    ]
);
```

### 7. Network and Utilities
- **Network Information**
  - Configuration
  - Economic statistics
  - Network status
  - Validator list

- **Utilities**
  - Unit conversion
  - Address validation
  - Hashing and signatures
  - bech32 encoding/decoding

```php
// Get network info
$config = MultiversX::network()->getNetworkConfig();
$economics = MultiversX::network()->getNetworkEconomics();
$status = MultiversX::network()->getNetworkStatus();

// Validate address
$isValid = MultiversX::wallet()->validateAddress('erd1...');

// Convert units
$egld = MultiversX::utils()->convertToEGLD('1000000000000000000');
$wei = MultiversX::utils()->convertToWei('1.5');

// Hash data
$hash = MultiversX::utils()->keccak256('data');

// Encode/decode bech32
$encoded = MultiversX::utils()->encodeBech32('erd1', $publicKey);
$decoded = MultiversX::utils()->decodeBech32($address);
```

### 8. Security and Compliance
- **Data Protection**
  - Private key encryption
  - Secure storage
  - Input validation
  - Attack protection

- **Standards**
  - BIP39 compliance
  - BIP44 compliance
  - bech32 format
  - MultiversX ESDT standards

```php
// Secure key generation
$entropy = MultiversX::security()->generateSecureEntropy();
$mnemonic = MultiversX::security()->generateMnemonic($entropy);

// Validate mnemonic
$isValid = MultiversX::security()->validateMnemonic($mnemonic);

// Encrypt sensitive data
$encrypted = MultiversX::security()->encryptData(
    $sensitiveData,
    $password,
    $salt
);

// Validate transaction input
$isValid = MultiversX::security()->validateTransactionInput([
    'value' => '1000000000000000000',
    'receiver' => 'erd1...',
    'data' => 'base64encoded'
]);
```

## License

MIT License

## Contributing

Please read our [Contributing Guide](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.
