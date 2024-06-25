<?php

declare(strict_types=1);

namespace Drupal\reqresimport\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\user\Entity\User;
use GuzzleHttp\ClientInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Messenger\MessengerTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Fetch JSON data from the `reqres.in` API.
 */
class FetchJson implements FetchJsonInterface {

  use MessengerTrait;

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
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(
    LoggerChannelFactoryInterface $logger_factory, 
    EntityTypeManagerInterface $entity_type_manager, 
    ClientInterface $http_client,
    ConfigFactoryInterface $config_factory, 
    MessengerInterface $messenger) {
    $this->loggerFactory = $logger_factory;
    $this->entityTypeManager = $entity_type_manager;
    $this->httpClient = $http_client;
    $this->configFactory = $config_factory;
    $this->messenger = $messenger;
  }

  /**
   * Fetch JSON data from the `reqres.in` API (now redundant as we have a service for this in ReqresApiClientInterface.php)
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
   * Fetch a single record (also redundant and superceded by fetchSingleRecordApi below).
   * 
   * @param int $id
   *   The ID of the record to fetch.
   * 
   * @return mixed
   *   Either JSON data or boolean FALSE.
   */
  public function fetchSingleRecord(int $id): mixed {
    $url = $this->configFactory->get('reqresimport.settings')->get('default_url');
    // Check validity of $url parameter.
    if ($url && !parse_url($url)) {
      $this->loggerFactory->get('reqresimport')->notice('Duff URL provided.');
      return FALSE;
    }
    // Check if the $id parameter is an integer.
    if (!is_int($id)) {
      $this->loggerFactory->get('reqresimport')->notice('ID parameter is not an integer.');
      return FALSE;
    }
    $client = $this->httpClient;
    $request = $client->request('GET', $url . '/' . $id);
    // Get the response code.
    $status_code = $request->getStatusCode();
    switch ($status_code) {
      case '200':
        // It works.
        $this->loggerFactory->get('reqresimport')->info('Request to %url was successful.', ['%url' => $url]);
        $fetched = json_decode((string) $request->getBody(), TRUE);
        // Put the single retrieved record into an array so we can feed it to applyJsonData()
        $data = $fetched['data'];
        unset($fetched['data']);
        $fetched['data'][] = $data;
        $this->applyJsonData($fetched);
        $response = 'User record, ' . $fetched['data'][0]['email'] . ' updated.';
        // Set message to be displayed.
        $this->messenger()->addMessage($response, 'status');
        break;
      
      default:
        $this->loggerFactory->get('reqresimport')->info('Request to %url was unsuccessful (status code: ' . $status_code . ').', ['%url' => $url]);
        $response = 'No record returned. Invalid ID.';
        // Set message to be displayed.
        $this->messenger()->addMessage($response, 'error');
        break;
    }
    
    // Redirect back to admin view.
    // @todo: Handle this URL better.
    return new RedirectResponse('/reqresimport/imported-users');

  }

  /**
   * Fetch a single record using the reqresimport.client service.
   * 
   * @param int $id
   *   The ID of the record to fetch.
   * 
   * @return mixed
   *   Either JSON data or boolean FALSE.
   */
  public function fetchSingleRecordApi(int $id): mixed {
    $url = $this->configFactory->get('reqresimport.settings')->get('default_url');
    // Check validity of $url parameter.
    if ($url && !parse_url($url)) {
      $this->loggerFactory->get('reqresimport')->notice('Duff URL provided.');
      return FALSE;
    }
    // Check if the $id parameter is an integer.
    if (!is_int($id)) {
      $this->loggerFactory->get('reqresimport')->notice('ID parameter is not an integer.');
      return FALSE;
    }
    // $client = $this->httpClient;
    // Get client from reqresimport.client service.
    $client = \Drupal::service('reqresimport.client');
    $fetched = $client->get($url . '/' . $id);
    if (!empty($fetched) && is_array($fetched) && count($fetched) > 0) {
      $this->loggerFactory->get('reqresimport')->info('Request to %url was successful.', ['%url' => $url]);
      
      // Put the single retrieved record into an array so we can feed it to applyJsonData()
      $data = $fetched['data'];
      unset($fetched['data']);
      $fetched['data'][] = $data;
      $this->applyJsonData($fetched);
      $response = 'User record, ' . $fetched['data'][0]['email'] . ' updated.';
      // Set message to be displayed.
      $this->messenger()->addMessage($response, 'status');
    }
    else {
      $this->loggerFactory->get('reqresimport')->info('Request to %url was unsuccessful', ['%url' => $url]);
      $response = 'No record returned. Possibly invalid ID.';
      // Set message to be displayed.
      $this->messenger()->addMessage($response, 'error');
    }
    
    // Redirect back to admin view.
    // @todo: Handle this URL better.
    return new RedirectResponse('/reqresimport/imported-users');

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
        $uid = (int)$existing_user->id();
        // Check to see if this user has a populated avatar image field.
        $avatar_present = $existing_user->get('field_reqres_avatar_image')->isEmpty() ? FALSE : TRUE;
        // Get the current avatar URI.
        $avatar_current = $existing_user->get('field_reqres_avatar_uri')->value;
        // Compare the current avatar URI with the one in the JSON data OR if the avatar is not present.
        if ($avatar_current !== $user['avatar'] || !$avatar_present) {
          // There is no avatar or the avatar URI has changed.
          $avatar_exists = FALSE;
        }
        else {
          // The avatar URI is the same so we shouldn't try to save the URI as a managed file entity.
          $avatar_exists = TRUE;
        }
      }
      else {
        $this->loggerFactory->get('reqresimport')->notice('Creating user with email %email.', ['%email' => $user['email']]);
        $new_user = $this->entityTypeManager->getStorage('user')->create([
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
        $uid = (int)$new_user;
        // As this is a new user record we can safel assume that there is no existing avatar.
        $avatar_exists = FALSE;
      }
      // Save the avatar as an image if we have a UID, an avatar URI in the json data and this URI does not exist as a file.
      if ($uid && !empty($user['avatar']) && !$avatar_exists) {
        $this->saveAvatarImage($user['avatar'], $uid);
      }
    }

  }

  /**
   * Take the avatar URL and save it as a managed file in field_reqres_avatar_image.
   * 
   * @param string $avatar_url
   *   The URL of the avatar image.
   * @param int $uid
   *   The user ID.
   * 
   * @return void
   */
  protected function saveAvatarImage(string $avatar_url, int $uid): void {
    $directory = 'public://reqres/avatar/' . $uid;
    /** @var \Drupal\Core\File\FileSystemInterface $file_system */
    $file_system = \Drupal::service('file_system');
    $file_repository = \Drupal::service('file.repository');
    $file_system->prepareDirectory($directory, FileSystemInterface:: CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS);
    $data = (string) \Drupal::httpClient()->get($avatar_url)->getBody();
    $file_name = basename($avatar_url);
    $file_repository->writeData($data, $directory . '/' . $file_name, FileSystemInterface::EXISTS_REPLACE);

    $file = File::create([
      'filename' => $file_name,
      'uri' => 'public://reqres/avatar/' . $uid . '/' . $file_name,
      'status' => 1,
      'uid' => $uid,
    ]);
    $file->save();
    $file_id = $file->id();
    $user = User::load($uid);

    //Populate the field_reqres_avatar_image field and subfields.
    $user->set('field_reqres_avatar_image', [
      'target_id' => $file_id,
      'alt' => 'Avatar for ' . $user->get('name')->value,
      'title' => 'Avatar for ' . $user->get('name')->value,
    ])->save();

    // Add file usage.
    /** @var \Drupal\file\FileUsage\DatabaseFileUsageBackend $file_usage */
    $file_usage = \Drupal::service('file.usage');
    $file_usage->add($file, 'reqresimport', 'user', $uid);
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

}
