<?php

declare(strict_types=1);

namespace Drupal\reqresimport\Service;

/**
 * @todo Add interface description.
 */
interface JsonUtilitiesInterface {

/**
 * Fetch a single record using the reqresimport.client service.
 * 
 * @param int $id
 *   The ID of the record to fetch.
 * 
 * @return mixed
 *   Either JSON data or boolean FALSE.
 */
public function fetchSingleRecordApi(int $id): mixed;

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
