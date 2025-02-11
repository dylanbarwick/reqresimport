<?php

declare(strict_types=1);

namespace Drupal\reqresimport\Service;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use GuzzleHttp\ClientInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Messenger\MessengerTrait;

/**
 * Fetch JSON data from the `reqres.in` API.
 */
class ReqresUtilities implements ReqresUtilitiesInterface {

  use MessengerTrait;

  /**
   * The logger channel factory - $logger_factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * The HTTP client - $http_client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * The configuration factory - $config_factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The ReqresApiClient service - $reqres_client.
   *
   * @var \Drupal\reqresimport\Service\ReqresApiClientInterface
   */
  protected $reqresClient;

  /**
   * Constructs a ReqresFetchJson object.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger channel factory.
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client.
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The configuration factory.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\reqresimport\Service\ReqresApiClientInterface $reqres_client
   *   The ReqresApiClient service.
   */
  public function __construct(
    LoggerChannelFactoryInterface $logger_factory,
    ClientInterface $http_client,
    ConfigFactoryInterface $config_factory,
    MessengerInterface $messenger,
    ReqresApiClientInterface $reqres_client) {
    $this->loggerFactory = $logger_factory;
    $this->httpClient = $http_client;
    $this->configFactory = $config_factory;
    $this->messenger = $messenger;
    $this->reqresClient = $reqres_client;
  }

  /**
   * Provide default settings.
   *
   * @return array
   */
  public function getDefaultConfig(): array {
    $config = $this->configFactory->get('reqresimport.settings');
    return [
      'default_url' => $config->get('default_url'),
      'default_endpoint' => $config->get('default_endpoint'),
      'default_parameter' => $config->get('default_parameter'),
      'default_parameter_value' => $config->get('default_parameter_value'),
    ];
  }

  /**
   * Sanitise array keys into header labels.
   *
   * @param array $keys
   *  The keys to sanitise into header labels.
   *
   * @return array
   */
  public function sanitiseKeys(array $keys): array {
    $sanitised_keys = [];
    foreach ($keys as $key) {
      if ($key === 'id') {
        $sanitised_keys[$key] = 'ID';
      }
      else {
        $sanitised_keys[$key] = ucwords(str_replace('_', ' ', $key));
      }

    }
    return $sanitised_keys;
  }

}
