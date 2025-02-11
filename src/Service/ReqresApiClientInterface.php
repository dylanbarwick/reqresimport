<?php

namespace Drupal\reqresimport\Service;

/**
 * Interface ReqresApiClientInterface.
 *
 * @package Drupal\reqresimport\Service
 */
interface ReqresApiClientInterface {

  /**
   * Retrieve data from the reqres api.
   *
   * @param string $endpoint
   * @param array $query
   *
   * @return array
   */
  public function get(string $endpoint, array $query = []): array;

}