<?php

declare(strict_types=1);

namespace Drupal\reqresimport\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a reqresimport form.
 */
final class FetchJsonForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'reqresimport_fetch_json';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $query_params = \Drupal::request()->query->all();
    $config = $this->config('reqresimport.settings');
    if (empty($query_params['vars_from_fetchjson_form'])) {
      $vars = [
        'url' => $config->get('default_url'),
        'parameter' => $config->get('default_parameter'),
        'parameter_value' => $config->get('default_parameter_value'),
      ];
    }
    else {
      $vars = $query_params['vars_from_fetchjson_form'];
    }

    $form['url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Url'),
      '#required' => TRUE,
      '#default_value' => $vars['url'],
    ];

    $form['parameter'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Parameter'),
      '#required' => FALSE,
      '#default_value' => $vars['parameter'],
    ];

    $form['parameter_value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Parameter value'),
      '#required' => FALSE,
      '#default_value' => $vars['parameter_value'],
    ];

    $form['preview_option'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Preview the results before importing.'),
      '#default_value' => TRUE,
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Send'),
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $vars_to_send['url'] = $form_state->getValue('url');
    $vars_to_send['parameter'] = $form_state->getValue('parameter');
    $vars_to_send['parameter_value'] = $form_state->getValue('parameter_value');
    $vars_to_send['preview_option'] = $form_state->getValue('preview_option');
    $form_state->setRedirect('reqresimport.fetch_json', [ 'vars_from_fetchjson_form' => $vars_to_send ]);
  }

}
