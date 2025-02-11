<?php

declare(strict_types=1);

namespace Drupal\reqresimport\Service;

/**
 * @todo Add interface description.
 */
interface ReqresUtilitiesInterface {

  /**
    * Provide default settings.
    *
    * @return array
    */
  public function getDefaultConfig(): array;

  /**
   * Sanitise array keys into header labels.
   *
   * @param array $keys
   *  The keys to sanitise into header labels.
   *
   * @return array
   */
  public function sanitiseKeys(array $keys): array;

}
