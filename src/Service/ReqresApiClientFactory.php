<?php

namespace Drupal\reqresimport\Service;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Http\ClientFactory;

/**
 * Class ReqresApiClientFactory.
 *
 * @package Drupal\reqresimport\Service
 */
class ReqresApiClientFactory {

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  private $configFactory;

  /**
   * Guzzle client factory.
   *
   * @var \Drupal\Core\Http\ClientFactory
   */
  private $httpClientFactory;

  /**
   * Json serializer.
   *
   * @var \Drupal\Component\Serialization\Json
   */
  private $json;

  /**
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   * @param \Drupal\Core\Http\ClientFactory $http_client_factory
   * @param \Drupal\Component\Serialization\Json $json
   */
  public function __construct(ConfigFactoryInterface $config_factory, ClientFactory $http_client_factory, Json $json) {
    $this->configFactory = $config_factory;
    $this->httpClientFactory = $http_client_factory;
    $this->json = $json;
  }

  /**
   * Create a new fully prepared instance of ReqresApiClient.
   *
   * @return \Drupal\reqresimport\Service\ReqresApiClient
   */
  public function create() {
    $config = $this->configFactory->get('reqresimport.settings');
    $http_client = $this->httpClientFactory->fromOptions([
      'verify' => FALSE,
      'base_uri' => $config->get('default_url'),
      'headers' => [
        'Content-Type' => 'application/json',
      ],
    ]);

    return new ReqresApiClient($http_client, $this->json);
  }

}