<?php

declare(strict_types=1);

namespace Drupal\reqresimport\Service;

/**
 * @todo Add interface description.
 */
interface FetchJsonInterface {

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
  public function fetchJsonData($url, array $params = []): mixed;

  /**
   * Fetch a single record.
   * 
   * @param int $id
   *   The ID of the record to fetch.
   * 
   * @return mixed
   *   Either JSON data or boolean FALSE.
   */
  public function fetchSingleRecord(int $id): mixed;

  /**
   * Process the retrieved JSON data.
   * 
   * @param array $data
   * 
   * @return void
   */
  public function applyJsonData(array $data): void;
  
/**
  * Provide default settings.
  * 
  * @return array
  */
 public function getDefaultConfig(): array;

}
