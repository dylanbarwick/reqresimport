<?php

namespace Drupal\reqresimport\Service;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Messenger\Messenger;
use GuzzleHttp\Client;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Messenger\MessengerTrait;

/**
 * Class ReqresApiClient
 *
 * @package Drupal\reqresimport\Service
 */
class ReqresApiClient implements ReqresApiClientInterface {

  use MessengerTrait;

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
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(Client $http_client, Json $json, MessengerInterface $messenger) {
    $this->httpClient = $http_client;
    $this->json = $json;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritDoc}
   */
  public function get(string $endpoint = NULL, array $query = []): array {
    if (empty($endpoint)) {
      return [];
    }

    // Wrap the httpClient->get in a try catch to prevent errors
    // from bubbling up to the user.
    try {
      $response = $this->httpClient->get($endpoint, [
        'query' => $query,
      ]);

      // Switch statement to deal with different status codes.
      switch ($response->getStatusCode()) {
        case 200:
          return $this->json::decode($response->getBody()->getContents());
          break;
        case 404:
          $this->messenger()->addError('The endpoint was not found.');
          break;
        case 500:
          $this->messenger()->addError('The server encountered an error.');
          break;
        default:
          $this->messenger()->addError('An error occurred.');
          break;
      }
    }
    catch (\Exception $e) {
      $this->messenger()->addError('An error occurred: <pre>' . $e->getMessage() . '</pre>');
      return [];
    }

    return [];
  }

}