<?php

namespace Drupal\reqresimport\Service;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * Service for fetching table content.
 */
class TableContentServiceBase {

  public function __construct(
    private readonly MessengerInterface $messenger,
    private readonly AccountProxyInterface $currentUser,
    private readonly PagerManagerInterface $pagerManager,
  ) {}

  /**
   * Generates the table content.
   *
   * @param string $block_id
   *   The block ID.
   * @param int $per_page
   *   The number of items per page.
   * @param string $url
   *   The URL to query.
   * @param int $page
   *   The current page number.
   * @param array $field_labels
   *   The field labels.
   *
   * @return array
   *   The render array of the table.
   */
  public function getTableContent($block_id, $items_per_page, $url, int $page = 1, $field_labels = []): array {
    $reqres_getter = \Drupal::service('reqresimport.client');
    $json_utils = \Drupal::service('reqresimport.utilities');

    $params = [
      'per_page' => $items_per_page,
      'page' => $page,
    ];
    $fetched_json = $reqres_getter->get($url, $params);
    $total_items = $fetched_json['total'];
    $total_pages = $fetched_json['total_pages'];

    // Set an array to contain the table header labels.
    $header_labels = [];
    // Fetch and prep the sanitised fallback values for the header labels.
    if (!empty($fetched_json['data'])) {
      $array_keys = array_keys($fetched_json['data'][0]);
      $sanitised_labels = $json_utils->sanitiseKeys($array_keys);
    }

    // Loop through the $field_labels array and set the header labels.
    foreach ($field_labels as $field_label_key => $field_label_data) {
      if (!$field_label_data['our_value']) {
        $header_labels[$field_label_data['reqres_label']] = $sanitised_labels[$field_label_data['reqres_label']];
      }
      else {
        $header_labels[$field_label_data['reqres_label']] = $field_label_data['our_value'];
      }
    }
    $header_labels = $this->prepHeaderLabels($field_labels, $sanitised_labels);

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
        $row = [];
        $row['email'] = $data['email'];
        $row['first_name'] = $data['first_name'];
        $row['last_name'] = $data['last_name'];
        $rows[] = $row;
      }
    }

    // The table that will display the results.
    $build['table_content'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#weight' => 20,
      '#attributes' => [
        'class' => [
          'reqres-table',
        ],
        'id' => $block_id . '_table',
      ],
      '#attached' => [
        'library' => [
          'reqresimport/reqres-styles',
        ],
      ],
      '#cache' => [
        'max-age' => 0,
      ],
    ];
    ////////////////////////////////////////////////////

    $pager = $this->pagerManager->getPager(0);
    if (is_null($pager)) {
      $pager = $this->pagerManager->createPager($total_items, $items_per_page, 0);
    }

    $current_page = $page;
    $start = $current_page * $items_per_page;
    $end = min($start + $items_per_page, $total_items);

    $blockManager = \Drupal::service('plugin.manager.block');
    $config = [];
    $block = $blockManager->createInstance('reqresimport_users_block', $config);
    $config = $block->getConfiguration();

    if ($total_pages > 1) {
      $pager_content = [
        '#type' => 'pager',
        '#element' => 0,
        '#weight' => 20,
        '#route_name' => 'reqresimport_users.refresh_table',
        '#parameters' => [
          'per_page' => $items_per_page,
          'page' => $page,
        ],
        '#prefix' => '<div id="pager-wrapper">',
        '#suffix' => '</div>',
      ];
    }

    $build['table_content']['#footer'] = [
      'data' => [
        [
          'colspan' => count($header),
          'data' => $pager_content,
        ]
      ],
    ];

    // Add wrapper around the whole thing
    $build['table_content']['#prefix'] = '<div id="ajax-pager-table-wrapper">';
    $build['table_content']['#suffix'] = '</div>';
    return $build;
  }

  /**
   * Preps the header labels.
   *
   * @param array $field_labels
   *  The field labels.
   * @param array $sanitised_labels
   *  The sanitised labels.
   *
   * @return array
   */
  public function prepHeaderLabels($field_labels, $sanitised_labels): array {
    $header_labels = [];
    // Loop through the $field_labels array and set the header labels.
    foreach ($field_labels as $field_label_key => $field_label_data) {
      if (!$field_label_data['our_value']) {
        $header_labels[$field_label_data['reqres_label']] = $sanitised_labels[$field_label_data['reqres_label']];
      }
      else {
        $header_labels[$field_label_data['reqres_label']] = $field_label_data['our_value'];
      }
    }
    return $header_labels;
  }

}