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
 * XOXNO Marketplace Service
 * Handles interactions with the XOXNO NFT marketplace on MultiversX
 */
class XoxnoService extends ApiClient
{
    protected $marketplaceAddress = 'erd1qqqqqqqqqqqqqpgqrc4pg2xarca9z34njcxeur622qmfjp8w2jps89fxnl';
    
    /**
     * List NFT on XOXNO marketplace
     *
     * @param string $collection Collection identifier
     * @param string $nonce Token nonce
     * @param string $price Listing price
     * @param string|null $paymentToken Optional payment token identifier
     * @return array Listing response data
     */
    public function listNft(string $collection, string $nonce, string $price, ?string $paymentToken = null): array
    {
        $args = [
            'collection' => $collection,
            'nonce' => $nonce,
            'price' => $price
        ];
        
        if ($paymentToken) {
            $args['paymentToken'] = $paymentToken;
        }
        
        return $this->post('/marketplace/list', $args);
    }

    /**
     * Cancel NFT listing on XOXNO
     *
     * @param string $collection Collection identifier
     * @param string $nonce Token nonce
     * @return array Cancellation response data
     */
    public function cancelListing(string $collection, string $nonce): array
    {
        return $this->post('/marketplace/cancel', [
            'collection' => $collection,
            'nonce' => $nonce
        ]);
    }

    /**
     * Buy NFT on XOXNO marketplace
     *
     * @param string $collection Collection identifier
     * @param string $nonce Token nonce
     * @param string $price Listing price
     * @param string|null $paymentToken Optional payment token identifier
     * @return array Buy response data
     */
    public function buyNft(string $collection, string $nonce, string $price, ?string $paymentToken = null): array
    {
        $args = [
            'collection' => $collection,
            'nonce' => $nonce,
            'price' => $price
        ];
        
        if ($paymentToken) {
            $args['paymentToken'] = $paymentToken;
        }
        
        return $this->post('/marketplace/buy', $args);
    }

    /**
     * Make an offer for an NFT
     *
     * @param string $collection Collection identifier
     * @param string $nonce Token nonce
     * @param string $price Offer price
     * @param int $deadline Offer deadline
     * @param string|null $paymentToken Optional payment token identifier
     * @return array Offer response data
     */
    public function makeOffer(string $collection, string $nonce, string $price, int $deadline, ?string $paymentToken = null): array
    {
        $args = [
            'collection' => $collection,
            'nonce' => $nonce,
            'price' => $price,
            'deadline' => $deadline
        ];
        
        if ($paymentToken) {
            $args['paymentToken'] = $paymentToken;
        }
        
        return $this->post('/marketplace/offer', $args);
    }

    /**
     * Cancel an NFT offer
     *
     * @param string $collection Collection identifier
     * @param string $nonce Token nonce
     * @param string $offerId Offer identifier
     * @return array Cancellation response data
     */
    public function cancelOffer(string $collection, string $nonce, string $offerId): array
    {
        return $this->post('/marketplace/cancel-offer', [
            'collection' => $collection,
            'nonce' => $nonce,
            'offerId' => $offerId
        ]);
    }

    /**
     * Accept an NFT offer
     *
     * @param string $collection Collection identifier
     * @param string $nonce Token nonce
     * @param string $offerId Offer identifier
     * @return array Acceptance response data
     */
    public function acceptOffer(string $collection, string $nonce, string $offerId): array
    {
        return $this->post('/marketplace/accept-offer', [
            'collection' => $collection,
            'nonce' => $nonce,
            'offerId' => $offerId
        ]);
    }

    /**
     * Get collection listings
     *
     * @param string $collection Collection identifier
     * @param int $page Page number
     * @param int $itemsPerPage Items per page
     * @return array Listings response data
     */
    public function getCollectionListings(string $collection, int $page = 1, int $itemsPerPage = 20): array
    {
        return $this->get("/marketplace/collection/{$collection}/listings", [
            'page' => $page,
            'items' => $itemsPerPage
        ]);
    }

    /**
     * Get NFT offers
     *
     * @param string $collection Collection identifier
     * @param string $nonce Token nonce
     * @return array Offers response data
     */
    public function getNftOffers(string $collection, string $nonce): array
    {
        return $this->get("/marketplace/nft/{$collection}/{$nonce}/offers");
    }

    /**
     * Get NFT history
     *
     * @param string $collection Collection identifier
     * @param string $nonce Token nonce
     * @return array History response data
     */
    public function getNftHistory(string $collection, string $nonce): array
    {
        return $this->get("/marketplace/nft/{$collection}/{$nonce}/history");
    }

    /**
     * Get collection statistics
     *
     * @param string $collection Collection identifier
     * @return array Statistics response data
     */
    public function getCollectionStats(string $collection): array
    {
        return $this->get("/marketplace/collection/{$collection}/stats");
    }

    /**
     * Get collection floor price
     *
     * @param string $collection Collection identifier
     * @return array Floor price response data
     */
    public function getCollectionFloor(string $collection): array
    {
        return $this->get("/marketplace/collection/{$collection}/floor");
    }

    /**
     * Get recent sales
     *
     * @param string $collection Collection identifier
     * @param int $limit Limit of sales
     * @return array Sales response data
     */
    public function getRecentSales(string $collection, int $limit = 10): array
    {
        return $this->get("/marketplace/collection/{$collection}/sales", [
            'limit' => $limit
        ]);
    }

    /**
     * Check if NFT is listed
     *
     * @param string $collection Collection identifier
     * @param string $nonce Token nonce
     * @return array Status response data
     */
    public function isNftListed(string $collection, string $nonce): array
    {
        return $this->get("/marketplace/nft/{$collection}/{$nonce}/status");
    }

    /**
     * Get verified collections
     *
     * @return array Verified collections response data
     */
    public function getVerifiedCollections(): array
    {
        return $this->get('/marketplace/collections/verified');
    }

    /**
     * Search NFTs
     *
     * @param array $filters Search filters
     * @param int $page Page number
     * @param int $itemsPerPage Items per page
     * @return array Search response data
     */
    public function searchNfts(array $filters, int $page = 1, int $itemsPerPage = 20): array
    {
        return $this->post('/marketplace/search', [
            'filters' => $filters,
            'page' => $page,
            'items' => $itemsPerPage
        ]);
    }

    /**
     * Get collection attributes
     *
     * @param string $collection Collection identifier
     * @return array Attributes response data
     */
    public function getCollectionAttributes(string $collection): array
    {
        return $this->get("/marketplace/collection/{$collection}/attributes");
    }
}
