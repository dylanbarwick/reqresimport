<?php

namespace Drupal\reqresimport\Service;

use Drupal\Component\Serialization\Json;
use GuzzleHttp\Client;

/**
 * Class ReqresApiClient
 *
 * @package Drupal\reqresimport\Service
 */
class ReqresApiClient implements ReqresApiClientInterface {

  /**
   * Prepared instance of http client.
   *
   * @var \GuzzleHttp\Client
   */
  private $httpClient;

  /**
   * Json serializer.
   *
   * @var \Drupal\Component\Serialization\Json
   */
  private $json;

  /**
   * ReqresApiClient constructor.
   *
   * @param \GuzzleHttp\Client $http_client
   * @param \Drupal\Component\Serialization\Json $json
   */
  public function __construct(Client $http_client, Json $json) {
    $this->httpClient = $http_client;
    $this->json = $json;
  }

  /**
   * {@inheritDoc}
   */
  public function get(string $endpoint = NULL, array $query = []): array {
    if (empty($endpoint)) {
      return [];
    }

    $response = $this->httpClient->get($endpoint, [
      'query' => $query,
    ]);

    if ($response->getStatusCode() === 200) {
      return $this->json::decode($response->getBody()->getContents());
    }

    return [];
  }

}