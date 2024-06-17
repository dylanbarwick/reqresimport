<?php

declare(strict_types=1);

namespace Drupal\reqresimport\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\user\Entity\User;
use GuzzleHttp\ClientInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Fetch JSON data from the `reqres.in` API.
 */
class FetchJson implements FetchJsonInterface {

  /**
   * The logger channel factory - $logger_factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * The entity manager - $entity_type_manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

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
   * Constructs a FetchJson object.
   * 
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger channel factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity manager.
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client.
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The configuration factory.
   */
  public function __construct(
    LoggerChannelFactoryInterface $logger_factory, 
    EntityTypeManagerInterface $entity_type_manager, 
    ClientInterface $http_client,
    ConfigFactoryInterface $config_factory) {
    $this->loggerFactory = $logger_factory;
    $this->entityTypeManager = $entity_type_manager;
    $this->httpClient = $http_client;
    $this->configFactory = $config_factory;
  }

  /**
   * Fetch JSON data from the `reqres.in` API.
   * 
   * @param string $url
   *   The URL to fetch JSON data from.
   * @param array $options
   *   An array of options to pass to the HTTP client.
   * 
   * @return mixed
   *  The JSON data.
   */
  public function fetchJsonData($url, array $params = []): mixed {
    // Check validity of $url parameter.
    if ($url && !parse_url($url)) {
      $this->loggerFactory->get('reqresimport')->notice('Duff URL provided.');
      return FALSE;
    }
    // Check if the params array is empty.
    if (empty($params)) {
      $this->loggerFactory->get('reqresimport')->info('No parameters provided.');
      return FALSE;
    }
    $client = $this->httpClient;
    $request = $client->request('GET', $url, $params);
    // Get the response code.
    $status_code = $request->getStatusCode();
    switch ($status_code) {
      case '200':
        // It works.
        $this->loggerFactory->get('reqresimport')->info('Request to %url was successful.', ['%url' => $url]);
        $response = json_decode((string) $request->getBody(), TRUE);
        break;
      
      default:
        $this->loggerFactory->get('reqresimport')->info('Request to %url was unsuccessful (status code: ' . $status_code . ').', ['%url' => $url]);
        $response = FALSE;
        break;
    }
    
    return $response;
  }

  /**
   * Process the retrieved JSON data.
   * 
   * @param array $fetched_json
   *  The JSON data to process.
   * 
   * @return void
   */
  public function applyJsonData($fetched_json): void {
    // Get the data.
    $users = $fetched_json['data'];
    // Process the data.
    foreach ($users as $user) {
      // Check if the user exists by checking email.
      $existing_user = $this->userExists($user['email']);
      if ($existing_user) {
        $this->loggerFactory->get('reqresimport')->notice('User with email %email already exists but I will update it anyway.', ['%email' => $user['email']]);
        if ($existing_user instanceof User) {
          $existing_user
            ->set('name', strtolower($user['first_name'] . $user['last_name']))
            ->set('field_reqres_id', $user['id'])
            ->set('field_reqres_first_name', $user['first_name'])
            ->set('field_reqres_last_name', $user['last_name'])
            ->set('field_reqres_avatar_uri', $user['avatar'])
            ->save();
        }
      }
      else {
        $this->loggerFactory->get('reqresimport')->notice('Creating user with email %email.', ['%email' => $user['email']]);
        $this->entityTypeManager->getStorage('user')->create([
          'name' => strtolower($user['first_name'] . $user['last_name']),
          'mail' => $user['email'],
          'init' => $user['email'],
          'field_reqres_id' => $user['id'],
          'field_reqres_first_name' => $user['first_name'],
          'field_reqres_last_name' => $user['last_name'],
          'field_reqres_avatar_uri' => $user['avatar'],
          'status' => 1,
          'roles' => ['authenticated'],
          'pass' => strtolower($user['first_name'] . $user['last_name']) . $user['id'],
        ])->save();
      }
    }

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
      'default_parameter' => $config->get('default_parameter'),
      'default_parameter_value' => $config->get('default_parameter_value'),
    ];
  }

  /**
   * Helper function to check if a user exists.
   * 
   * @param string $email
   *  The email address to check.
   * 
   * @return mixed
   */
  protected function userExists(string $email): mixed {
    $users = $this->entityTypeManager->getStorage('user')->loadByProperties(['mail' => $email]);
    if (empty($users)) {
      return FALSE;
    }
    else {
      return reset($users);
    }
  }

  /**
   * Build and return a valid URL to fetch reqres data based on default config.
   * 
   * @return array
   */
  public function getReqresUrl(): array {
    $config = $this->getDefaultConfig();
    $url = $config['default_url'];
    $parameter = $config['default_parameter'];
    $parameter_value = $config['default_parameter_value'];
    $full_url[] = $url . '?' . $parameter . '=' . $parameter_value;
    $this->loggerFactory->get('reqresimport - getReqresUrl')->notice('Getting the default URL: %url', ['%url' => $full_url]);
    return $full_url;
  }

}
