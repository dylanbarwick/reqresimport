<?php

declare(strict_types=1);

namespace Drupal\reqresimport\Service;

/**
 * @todo Add interface description.
 */
interface FetchJsonInterface {

  /**
   * @todo Add method description.
   */
  public function fetchJsonData($url, array $options): mixed;

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
