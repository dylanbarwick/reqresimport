<?php

namespace Drupal\Tests\reqresimport\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\reqresimport\Service\FetchJson;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use GuzzleHttp\ClientInterface;
use Drupal\user\Entity\User;

/**
 * Main test class for the json fetching service.
 */
class FetchJsonTest extends UnitTestCase {
    
  /**
  * {@inheritdoc}
  */
  protected function setUp(): void {
    parent::setUp();
  }

  /**
   * Provides data for testFetchJsonData().
   */
  public function fetchJsonData() {
    $return = [
      'data_good' => [
        // Input
        [
          'url' => 'https://reqres.in/api/users',
          'params' => [
            'page' => 1,
          ],
        ],
        // Expected
        ['array' => 1]
      ],
      'data_bad_no_url' => [
        // Input
        [
          'url' => NULL,
          'params' => [
            'page' => 1,
          ],
        ],
        // Expected
        'not an array',
      ],
      'data_bad_bad_url' => [
        // Input
        [
          'url' => 'not a URL',
          'params' => [
            'page' => 1,
          ],
        ],
        // Expected
        'not an array',
      ],
      'data_bad_no_params' => [
        // Input
        [
          'url' => 'https://reqres.in/api/users',
          'params' => [],
        ],
        // Expected
        'not an array',
      ],
      'data_bad_no_url_or_params' => [
        // Input
        [
          'url' => NULL,
          'params' => [],
        ],
        // Expected
        'not an array',
      ],
    ];
    return $return;
  }

  /**
   * Provides test data for testGetDefaultConfig
   */
  public function getDefaultConfigData() {
    $return = [
      'good_result' => [
        // Expected
        [
          'default_url' => 'some_string',
          'default_parameter' => 'some_string',
          'default_parameter_value' => 'some_string',
        ]
      ],
      'bad_result' => [
        // Expected
        [
          'default_url' => '',
          'default_parameter' => '',
          'default_parameter_value' => '',
        ]
      ],
    ];
    return $return;
  }

  /**
   * Provides test data for the userExists method.
   */
  public function userExistsData() {
    $admin_account = User::load($this->rootUser->id());
    $return = [
      'user_exists' => [
        // Input
        'email' => $admin_account->getEmail(),
        // Expected
        $admin_account,
      ],
      'user_does_not_exist' => [
        // Input
        'email' => 'not_an_email@example.com',
        // Expected
        FALSE,
      ],
    ];
    return $return;
  }


  /**
   * Feeds values to the fetchJsonData method and tests if an array is returned.
   *
   * @param array $input
   *   The input value.
   * @param mixed $expected
   *   The expected output.
   *
   * @dataProvider fetchJsonData
   * */
  public function notestFetchJsonData(array $input, $expected) {
    $loggerService = $this->createMock(LoggerChannelFactoryInterface::class);
    $entityTypeManagerService = $this->createMock(EntityTypeManagerInterface::class);
    $httpClientService = $this->createMock(ClientInterface::class);
    $configFactoryService = $this->createMock(ConfigFactoryInterface::class);
    $fetchJsonService = new FetchJson($loggerService, $entityTypeManagerService, $httpClientService, $configFactoryService);

    $result = $fetchJsonService->fetchJsonData($input['url'], $input['params']);
    // Label all feedback.
    fwrite(STDOUT, "\n======================\ntestFetchJsonData \n======================\n");
    fwrite(STDOUT, "input:    " . print_r($input, TRUE) . " \nexpected: " . print_r($expected, TRUE) . " \nresult:   " . print_r($result, TRUE) . " \n");

    $this->assertIsArray($result);
  }

  /**
   * Feeds values to the getDefaultConfig method and tests if an array is returned.
   *
   * @param mixed $expected
   *   The expected output.
   *
   * @dataProvider getDefaultConfigData
   * */
  public function notestGetDefaultConfig($expected) {
    $loggerService = $this->createMock(LoggerChannelFactoryInterface::class);
    $entityTypeManagerService = $this->createMock(EntityTypeManagerInterface::class);
    $httpClientService = $this->createMock(ClientInterface::class);
    $configFactoryService = $this->createMock(ConfigFactoryInterface::class);
    $fetchJsonService = new FetchJson($loggerService, $entityTypeManagerService, $httpClientService, $configFactoryService);

    $result = $fetchJsonService->getDefaultConfig();
    // Label all feedback.
    fwrite(STDOUT, "\n======================\ntestGetDefaultConfig \n======================\n");
    fwrite(STDOUT, "expected: $expected \nresult:   $result \n");

    $this->assertIsString($result['default_url']);
    $this->assertIsString($result['default_parameter']);
    $this->assertIsString($result['default_parameter_value']);
  }

  /**
   * Feeds values to the userExists method and tests if a user object is returned.
   * 
   * @param string $input
   *  The email address to check.
   * @param mixed $expected
   *  The expected output.
   * 
   * @dataProvider userExistsData
   */
  public function notestUserExists($input, $expected) {
    $loggerService = $this->createMock(LoggerChannelFactoryInterface::class);
    $entityTypeManagerService = $this->createMock(EntityTypeManagerInterface::class);
    $httpClientService = $this->createMock(ClientInterface::class);
    $configFactoryService = $this->createMock(ConfigFactoryInterface::class);
    // $reflectedFetchJson = new \ReflectionClass(FetchJson::class);
    // $reflectedFetchJson->getMethod('userExists')->setAccessible(TRUE);
    $fetchJsonService = new FetchJson($loggerService, $entityTypeManagerService, $httpClientService, $configFactoryService);

    $result = $fetchJsonService->userExists($input);
    // Label all feedback.
    fwrite(STDOUT, "\n======================\ntestUserExists \n======================\n");
    fwrite(STDOUT, "input:    $input \nexpected: $expected \nresult:   $result \n");

    $this->assertEquals($expected, $result);
  }

}