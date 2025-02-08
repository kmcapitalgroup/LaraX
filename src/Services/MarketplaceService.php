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
 * Marketplace Service for MultiversX blockchain
 * Handles custom marketplace operations and interactions
 */
class MarketplaceService extends ApiClient
{
    const TOKEN_TYPE_NFT = 'NonFungibleESDT';
    const TOKEN_TYPE_SFT = 'SemiFungibleESDT';
    const TOKEN_TYPE_META = 'MetaESDT';

    /**
     * Deploy a new marketplace contract
     *
     * @param string $ownerAddress Owner address
     * @param int $marketplaceFee Marketplace fee (default: 250)
     * @param array $options Optional deployment options
     * @return array Deployment response
     */
    public function deployMarketplace(
        string $ownerAddress,
        int $marketplaceFee = 250,
        array $options = []
    ): array {
        $args = [
            'owner' => $ownerAddress,
            'marketplace_fee' => $marketplaceFee,
            'accepted_tokens' => [
                self::TOKEN_TYPE_NFT,
                self::TOKEN_TYPE_SFT,
                self::TOKEN_TYPE_META
            ],
            ...$options
        ];

        return $this->smartContract()->deploy('marketplace', $args);
    }

    /**
     * List a token for sale (NFT, SFT or Meta ESDT)
     */
    public function listToken(
        string $marketplaceAddress,
        string $collection,
        string $nonce,
        string $price,
        ?int $quantity = 1,
        ?string $paymentToken = null,
        array $options = []
    ): array {
        $args = [
            'collection' => $collection,
            'nonce' => $nonce,
            'price' => $price,
            'quantity' => $quantity,
            ...$options
        ];

        if ($paymentToken) {
            $args['payment_token'] = $paymentToken;
        }

        return $this->post("/marketplace/{$marketplaceAddress}/list", $args);
    }

    /**
     * Buy a token (NFT, SFT or Meta ESDT)
     */
    public function buyToken(
        string $marketplaceAddress,
        string $collection,
        string $nonce,
        string $price,
        ?int $quantity = 1,
        ?string $paymentToken = null
    ): array {
        $args = [
            'collection' => $collection,
            'nonce' => $nonce,
            'price' => $price,
            'quantity' => $quantity
        ];

        if ($paymentToken) {
            $args['payment_token'] = $paymentToken;
        }

        return $this->post("/marketplace/{$marketplaceAddress}/buy", $args);
    }

    /**
     * Make an offer for a token
     */
    public function makeOffer(
        string $marketplaceAddress,
        string $collection,
        string $nonce,
        string $price,
        int $deadline,
        ?int $quantity = 1,
        ?string $paymentToken = null
    ): array {
        $args = [
            'collection' => $collection,
            'nonce' => $nonce,
            'price' => $price,
            'deadline' => $deadline,
            'quantity' => $quantity
        ];

        if ($paymentToken) {
            $args['payment_token'] = $paymentToken;
        }

        return $this->post("/marketplace/{$marketplaceAddress}/offer", $args);
    }

    /**
     * Configure the token types accepted for a collection
     */
    public function setCollectionTokenTypes(
        string $marketplaceAddress,
        string $collection,
        array $tokenTypes
    ): array {
        return $this->post("/marketplace/{$marketplaceAddress}/collections/{$collection}/token-types", [
            'token_types' => $tokenTypes
        ]);
    }

    /**
     * Check if a token is listed
     */
    public function isTokenListed(
        string $marketplaceAddress,
        string $collection,
        string $nonce,
        ?int $quantity = null
    ): array {
        $args = [
            'collection' => $collection,
            'nonce' => $nonce
        ];

        if ($quantity !== null) {
            $args['quantity'] = $quantity;
        }

        return $this->get("/marketplace/{$marketplaceAddress}/status", $args);
    }

    /**
     * Get the metadata of a token
     */
    public function getTokenMetadata(
        string $marketplaceAddress,
        string $collection,
        string $nonce
    ): array {
        return $this->get("/marketplace/{$marketplaceAddress}/token/{$collection}/{$nonce}");
    }

    /**
     * Update the metadata of a dynamic token
     */
    public function updateDynamicTokenMetadata(
        string $marketplaceAddress,
        string $collection,
        string $nonce,
        array $metadata
    ): array {
        return $this->post("/marketplace/{$marketplaceAddress}/token/{$collection}/{$nonce}/metadata", [
            'metadata' => $metadata
        ]);
    }

    /**
     * Get the transaction history for a token
     */
    public function getTokenHistory(
        string $marketplaceAddress,
        string $collection,
        string $nonce,
        array $filters = [],
        int $page = 1,
        int $itemsPerPage = 20
    ): array {
        return $this->get("/marketplace/{$marketplaceAddress}/token/{$collection}/{$nonce}/history", [
            'filters' => $filters,
            'page' => $page,
            'items' => $itemsPerPage
        ]);
    }

    /**
     * Configure the marketplace fee
     */
    public function setMarketplaceFee(
        string $marketplaceAddress,
        int $fee
    ): array {
        return $this->post("/marketplace/{$marketplaceAddress}/fee", [
            'fee' => $fee
        ]);
    }

    /**
     * Add a collection to the marketplace
     */
    public function addCollection(
        string $marketplaceAddress,
        string $collection,
        array $options = []
    ): array {
        return $this->post("/marketplace/{$marketplaceAddress}/collections", [
            'collection' => $collection,
            ...$options
        ]);
    }

    /**
     * Cancel a listing
     */
    public function cancelListing(
        string $marketplaceAddress,
        string $collection,
        string $nonce
    ): array {
        return $this->post("/marketplace/{$marketplaceAddress}/cancel", [
            'collection' => $collection,
            'nonce' => $nonce
        ]);
    }

    /**
     * Accept an offer
     */
    public function acceptOffer(
        string $marketplaceAddress,
        string $collection,
        string $nonce,
        string $offerId
    ): array {
        return $this->post("/marketplace/{$marketplaceAddress}/accept-offer", [
            'collection' => $collection,
            'nonce' => $nonce,
            'offer_id' => $offerId
        ]);
    }

    /**
     * Configure the royalties for a collection
     */
    public function setRoyalties(
        string $marketplaceAddress,
        string $collection,
        int $royalties,
        string $beneficiary
    ): array {
        return $this->post("/marketplace/{$marketplaceAddress}/royalties", [
            'collection' => $collection,
            'royalties' => $royalties,
            'beneficiary' => $beneficiary
        ]);
    }

    /**
     * Get the active listings
     */
    public function getListings(
        string $marketplaceAddress,
        array $filters = [],
        int $page = 1,
        int $itemsPerPage = 20
    ): array {
        return $this->get("/marketplace/{$marketplaceAddress}/listings", [
            'filters' => $filters,
            'page' => $page,
            'items' => $itemsPerPage
        ]);
    }

    /**
     * Get the active offers
     */
    public function getOffers(
        string $marketplaceAddress,
        array $filters = [],
        int $page = 1,
        int $itemsPerPage = 20
    ): array {
        return $this->get("/marketplace/{$marketplaceAddress}/offers", [
            'filters' => $filters,
            'page' => $page,
            'items' => $itemsPerPage
        ]);
    }

    /**
     * Get the marketplace statistics
     */
    public function getStats(string $marketplaceAddress): array
    {
        return $this->get("/marketplace/{$marketplaceAddress}/stats");
    }

    /**
     * Get the transaction history
     */
    public function getHistory(
        string $marketplaceAddress,
        array $filters = [],
        int $page = 1,
        int $itemsPerPage = 20
    ): array {
        return $this->get("/marketplace/{$marketplaceAddress}/history", [
            'filters' => $filters,
            'page' => $page,
            'items' => $itemsPerPage
        ]);
    }

    /**
     * Configure the accepted payment tokens
     */
    public function setAcceptedPaymentTokens(
        string $marketplaceAddress,
        array $tokens
    ): array {
        return $this->post("/marketplace/{$marketplaceAddress}/payment-tokens", [
            'tokens' => $tokens
        ]);
    }

    /**
     * Add administrators to the marketplace
     */
    public function addAdmins(
        string $marketplaceAddress,
        array $addresses
    ): array {
        return $this->post("/marketplace/{$marketplaceAddress}/admins", [
            'addresses' => $addresses
        ]);
    }

    /**
     * Pause or unpause the marketplace
     */
    public function togglePause(string $marketplaceAddress): array
    {
        return $this->post("/marketplace/{$marketplaceAddress}/toggle-pause");
    }
}
