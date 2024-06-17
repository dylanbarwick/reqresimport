<?php

declare(strict_types=1);

namespace Drupal\reqresimport\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a fetch json form block.
 *
 * @Block(
 *   id = "reqresimport_fetch_json_form",
 *   admin_label = @Translation("Fetch Json form"),
 *   category = @Translation("Custom"),
 * )
 */
final class FetchJsonFormBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $build = [];
    $form = \Drupal::formBuilder()->getForm('Drupal\reqresimport\Form\FetchJsonForm');
    $render_service = \Drupal::service('renderer');
    $build['fetch_json_form']['#markup'] = $render_service->renderPlain($form);

    return $build;
  }

}
