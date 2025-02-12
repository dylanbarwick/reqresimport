<?php

namespace Drupal\Tests\reqresimport\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\reqresimport\Service\ReqresUtilities;
use Drupal\Core\Logger\LoggerChannelFactory;
use GuzzleHttp\ClientInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\reqresimport\Service\ReqresApiClientInterface;

/**
 * Tests the ReqresUtilities class.
 *
 * @group reqresimport
 */
class ReqresUtilitiesTest extends UnitTestCase {

  protected $reqresUtilities;

  /**
   * Standard setUp function.
   */
  public function setUp():void {
    parent::setUp();
  }
  public function testSanitiseKeys() {
    $logger_factory = $this->createMock(LoggerChannelFactory::class);
    $http_client = $this->createMock(ClientInterface::class);
    $config_factory = $this->createMock(ConfigFactoryInterface::class);
    $messenger = $this->createMock(MessengerInterface::class);
    $reqres_client = $this->createMock(ReqresApiClientInterface::class);
    // $logger_factory = new LoggerChannelFactory();
    // $http_client = new ClientInterface();
    // $config_factory = new ConfigFactoryInterface();
    // $messenger = new MessengerInterface();
    // $reqres_client = new ReqresApiClientInterface();

    // machine-name keys.
    $array_keys = [
      "id",
      "email",
      "first_name",
      "last_name",
      "avatar",
    ];
    $expected = [
      "ID",
      "Email",
      "First Name",
      "Last Name",
      "Avatar"
    ];

    $reqresUtilities = new ReqresUtilities($logger_factory, $http_client, $config_factory, $messenger, $reqres_client);
    $header_labels = array_values($reqresUtilities->sanitiseKeys($array_keys));

    // Assert that the header labels are as expected.
    $this->assertEquals($expected, $header_labels);

  }

}
