<?php

declare(strict_types=1);

namespace Drupal\reqresimport\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for reqresimport routes.
 */
class FetchJsonController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function __invoke($vars_from_fetchjson_form = NULL): array {
    $reqres_getter = \Drupal::service('reqresimport.client');
    $json_utils = \Drupal::service('reqresimport.jsonutilities');
    $default_config = $json_utils->getDefaultConfig();
    $query_params = \Drupal::request()->query->all();
    if (empty($query_params['vars_from_fetchjson_form'])) {
      $vars_from_fetchjson_form = [
        'url' => $default_config['default_url'],
        'parameter' => $default_config['default_parameter'],
        'parameter_value' => $default_config['default_parameter_value'],
        'preview_option' => '1',
      ];
    }
    else {
      $vars_from_fetchjson_form = $query_params['vars_from_fetchjson_form'];
    }

    $url = $vars_from_fetchjson_form['url'];
    $params = [
      $vars_from_fetchjson_form['parameter'] => $vars_from_fetchjson_form['parameter_value'],
    ];
    $fetched_json = $reqres_getter->get($url, $params);

    // Apply data only if $fetched_json is TRUE and not empty.
    if ($fetched_json && !empty($fetched_json)) {
      // If the preview_option is `0` then we will create/update the user records.
      if ($vars_from_fetchjson_form['preview_option'] === '0') {
        $json_utils->applyJsonData($fetched_json);
      }
    }

    // Header for the results table.
    $header = [
      'id' => [
        'data' => $this->t('ID'),
      ],
      'email' => [
        'data' => $this->t('Email'),
      ],
      'first_name' => [
        'data' => $this->t('First name'),
      ],
      'last_name' => [
        'data' => $this->t('Last name'),
      ],
      'avatar' => [
        'data' => $this->t('Avatar URI'),
      ],
    ];

    // Prepare the rows for the results table.
    $rows = [];
    foreach ($fetched_json['data'] as $data) {
      $rows[] = [
        'id' => $data['id'],
        'email' => $data['email'],
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'avatar' => $data['avatar'],
      ];
    }


    // Initialise the $build render array.
    $build = [];
    // Build the form that will allow a user to preview the results before importing.
    $render_form = \Drupal::formBuilder()->getForm('Drupal\reqresimport\Form\FetchJsonForm');
    $render_service = \Drupal::service('renderer');
    $build['fetch_json_form_block'] = [
      '#markup' => $render_service->renderPlain($render_form),
      '#weight' => 10,
      '#cache' => [
        'max-age' => 0,
      ],
    ];

    // The table that will display the results.
    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#weight' => 20,
      '#cache' => [
        'max-age' => 0,
      ],
    ];

    return $build;
  }

}
