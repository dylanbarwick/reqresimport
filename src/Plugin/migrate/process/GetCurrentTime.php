<?php

declare(strict_types=1);

namespace Drupal\reqresimport\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Provides a get_current_time plugin.
 *
 * Usage:
 *
 * @code
 * process:
 *   bar:
 *     plugin: get_current_time
 *     source: foo
 * @endcode
 *
 * @MigrateProcessPlugin(id = "get_current_time")
 */
final class GetCurrentTime extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): mixed {
    $value = time();
    return $value;
  }

}
