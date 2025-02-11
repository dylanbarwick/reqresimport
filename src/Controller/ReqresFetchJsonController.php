<?php

declare(strict_types=1);

namespace Drupal\reqresimport\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for reqresimport routes.
 */
class ReqresFetchJsonController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function __invoke($vars_from_fetchjson_form = NULL): array {
    $reqres_getter = \Drupal::service('reqresimport.client');
    $json_utils = \Drupal::service('reqresimport.utilities');
    $default_config = $json_utils->getDefaultConfig();
    $query_params = \Drupal::request()->query->all();
    if (empty($query_params['vars_from_fetchjson_form'])) {
      $vars_from_fetchjson_form = [
        'url' => $default_config['default_url'],
        'endpoint' => $default_config['default_endpoint'],
        'parameter' => $default_config['default_parameter'],
        'parameter_value' => $default_config['default_parameter_value'],
      ];
    }
    else {
      $vars_from_fetchjson_form = $query_params['vars_from_fetchjson_form'];
    }

    $url = $vars_from_fetchjson_form['url'] . $vars_from_fetchjson_form['endpoint'];
    $params = [
      $vars_from_fetchjson_form['parameter'] => $vars_from_fetchjson_form['parameter_value'],
    ];
    $fetched_json = $reqres_getter->get($url, $params);

    $header_labels = [];
    if (!empty($fetched_json['data'])) {
      $array_keys = array_keys($fetched_json['data'][0]);
      $header_labels = $json_utils->sanitiseKeys($array_keys);
    }

    $header = [];
    foreach ($header_labels as $label) {
      $header[$label] = [
        'data' => $label,
      ];
    }

    // Prepare the rows for the results table.
    $rows = [];
    if (isset($fetched_json['data']) && count($fetched_json['data']) > 0) {
      foreach ($fetched_json['data'] as $data) {
        $rows[] = $data;
      }
    }


    // Initialise the $build render array.
    $build = [];
    // Build the form that will allow a user to preview the results before importing.
    $render_form = \Drupal::formBuilder()->getForm('Drupal\reqresimport\Form\ReqresFetchJsonForm');
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
      '#attributes' => [
        'class' => [
          'reqres-table',
        ],
      ],
      '#cache' => [
        'max-age' => 0,
      ],
      '#attached' => [
        'library' => [
          'reqresimport/reqres-styles',
        ],
      ],
    ];

    return $build;
  }

}
