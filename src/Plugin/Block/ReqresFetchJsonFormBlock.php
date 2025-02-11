<?php

declare(strict_types=1);

namespace Drupal\reqresimport\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a fetch json form block.
 *
 * @Block(
 *   id = "reqresimport_fetch_json_form_block",
 *   admin_label = @Translation("Fetch reqres Json form"),
 *   category = @Translation("Custom"),
 * )
 */
final class ReqresFetchJsonFormBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $build = [];
    $form = \Drupal::formBuilder()->getForm('Drupal\reqresimport\Form\ReqresFetchJsonForm');
    $render_service = \Drupal::service('renderer');
    $build['reqres_fetch_json_form']['#markup'] = $render_service->renderPlain($form);

    return $build;
  }

}
