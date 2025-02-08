<?php
/**
 * MultiversX Laravel SDK
 *
 * @package   MultiversX\Laravel
 * @author    MultiversX
 * @copyright Copyright (c) 2024-present MultiversX
 * @license   MIT License
 */

namespace MultiversX\Laravel\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use Exception;

/**
 * Base API Client for MultiversX
 * Handles HTTP requests to the MultiversX API
 */
class ApiClient
{
    protected $client;
    protected $config;
    protected $baseUrl;

    /**
     * Initialize API client
     *
     * @param array $config Configuration options
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->baseUrl = $this->getApiUrl();
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
    }

    protected function getApiUrl(): string
    {
        $network = $this->config['network'];
        return $this->config['networks'][$network]['api_url'];
    }

    /**
     * Make request
     *
     * @param string $method Request method
     * @param string $endpoint API endpoint
     * @param array $options Request options
     * @return array Response data
     * @throws Exception If request fails
     */
    protected function request(string $method, string $endpoint, array $options = []): array
    {
        try {
            $response = $this->client->request($method, $endpoint, $options);
            $contents = json_decode($response->getBody()->getContents(), true);
            
            if (isset($contents['error']) && !empty($contents['error'])) {
                throw new \Exception($contents['error']);
            }

            return $contents['data'] ?? $contents;
        } catch (GuzzleException $e) {
            throw new \Exception("API Request failed: " . $e->getMessage());
        }
    }

    /**
     * Make GET request
     *
     * @param string $endpoint API endpoint
     * @param array $query Query parameters
     * @return array Response data
     * @throws Exception If request fails
     */
    protected function get(string $endpoint, array $query = []): array
    {
        return $this->request('GET', $endpoint, ['query' => $query]);
    }

    /**
     * Make POST request
     *
     * @param string $endpoint API endpoint
     * @param array $data Request body data
     * @return array Response data
     * @throws Exception If request fails
     */
    protected function post(string $endpoint, array $data = []): array
    {
        return $this->request('POST', $endpoint, ['json' => $data]);
    }
}
