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
 * Dynamic NFT Service for MultiversX blockchain
 * Handles dynamic NFT operations and updates
 */
class DynamicNftService extends ApiClient
{
    /**
     * Create a new dynamic NFT collection
     *
     * @param string $name Collection name
     * @param string $ticker Collection ticker
     * @param array $properties Collection properties
     * @return array Collection creation response
     */
    public function createCollection(
        string $name,
        string $ticker,
        array $properties = []
    ): array {
        return $this->post('/collections/nft', [
            'name' => $name,
            'ticker' => $ticker,
            'canFreeze' => true,
            'canWipe' => true,
            'canPause' => true,
            'canTransferNFTCreateRole' => true,
            'canChangeOwner' => true,
            'canUpgrade' => true,
            'canAddSpecialRoles' => true,
            ...$properties
        ]);
    }

    /**
     * Create a new dynamic NFT
     *
     * @param string $collection Collection identifier
     * @param string $name NFT name
     * @param array $attributes NFT attributes
     * @param string $baseUri NFT base URI
     * @param array $uris NFT URIs
     * @param int $royalties NFT royalties
     * @param int $supply NFT supply
     * @return array NFT creation response
     */
    public function createDynamicNft(
        string $collection,
        string $name,
        array $attributes,
        string $baseUri,
        array $uris = [],
        int $royalties = 0,
        int $supply = 1
    ): array {
        return $this->post('/nft/create', [
            'collection' => $collection,
            'name' => $name,
            'attributes' => json_encode($attributes),
            'baseUri' => $baseUri,
            'uris' => $uris,
            'royalties' => $royalties,
            'supply' => $supply,
        ]);
    }

    /**
     * Update dynamic NFT attributes
     *
     * @param string $collection Collection identifier
     * @param string $nonce Token nonce
     * @param array $attributes New attributes
     * @return array Update response
     */
    public function updateAttributes(
        string $collection,
        string $nonce,
        array $attributes
    ): array {
        return $this->post('/nft/update', [
            'collection' => $collection,
            'nonce' => $nonce,
            'attributes' => json_encode($attributes),
        ]);
    }

    /**
     * Update dynamic NFT URIs
     *
     * @param string $collection Collection identifier
     * @param string $nonce Token nonce
     * @param array $uris New URIs
     * @return array Update response
     */
    public function updateUris(
        string $collection,
        string $nonce,
        array $uris
    ): array {
        return $this->post('/nft/update', [
            'collection' => $collection,
            'nonce' => $nonce,
            'uris' => $uris,
        ]);
    }

    /**
     * Add URI to dynamic NFT
     *
     * @param string $collection Collection identifier
     * @param string $nonce Token nonce
     * @param string $uri New URI
     * @return array Update response
     */
    public function addUri(
        string $collection,
        string $nonce,
        string $uri
    ): array {
        return $this->post('/nft/update', [
            'collection' => $collection,
            'nonce' => $nonce,
            'uris' => ['add' => [$uri]],
        ]);
    }

    /**
     * Remove URI from dynamic NFT
     *
     * @param string $collection Collection identifier
     * @param string $nonce Token nonce
     * @param string $uri URI to remove
     * @return array Update response
     */
    public function removeUri(
        string $collection,
        string $nonce,
        string $uri
    ): array {
        return $this->post('/nft/update', [
            'collection' => $collection,
            'nonce' => $nonce,
            'uris' => ['remove' => [$uri]],
        ]);
    }

    /**
     * Get NFT metadata
     *
     * @param string $collection Collection identifier
     * @param string $nonce Token nonce
     * @return array NFT metadata
     */
    public function getNftMetadata(
        string $collection,
        string $nonce
    ): array {
        return $this->get("/nft/{$collection}-{$nonce}");
    }

    /**
     * Get collection metadata
     *
     * @param string $collection Collection identifier
     * @return array Collection metadata
     */
    public function getCollectionMetadata(string $collection): array
    {
        return $this->get("/collections/{$collection}");
    }

    /**
     * Add special role to address
     *
     * @param string $collection Collection identifier
     * @param string $address Address to add role
     * @param string $role Role to add
     * @return array Update response
     */
    public function addSpecialRole(
        string $collection,
        string $address,
        string $role
    ): array {
        return $this->post('/collections/nft/setSpecialRole', [
            'collection' => $collection,
            'address' => $address,
            'role' => $role,
        ]);
    }

    /**
     * Remove special role from address
     *
     * @param string $collection Collection identifier
     * @param string $address Address to remove role
     * @param string $role Role to remove
     * @return array Update response
     */
    public function removeSpecialRole(
        string $collection,
        string $address,
        string $role
    ): array {
        return $this->post('/collections/nft/unsetSpecialRole', [
            'collection' => $collection,
            'address' => $address,
            'role' => $role,
        ]);
    }

    /**
     * Transfer NFT create role
     *
     * @param string $collection Collection identifier
     * @param string $address Address to transfer role
     * @return array Update response
     */
    public function transferCreateRole(
        string $collection,
        string $address
    ): array {
        return $this->post('/collections/nft/transferNFTCreateRole', [
            'collection' => $collection,
            'address' => $address,
        ]);
    }

    /**
     * Stop NFT create
     *
     * @param string $collection Collection identifier
     * @return array Update response
     */
    public function stopCreate(string $collection): array
    {
        return $this->post('/collections/nft/stopCreate', [
            'collection' => $collection,
        ]);
    }
}
