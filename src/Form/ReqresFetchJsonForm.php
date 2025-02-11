<?php

declare(strict_types=1);

namespace Drupal\reqresimport\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a reqresimport form.
 */
final class ReqresFetchJsonForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'reqresimport_fetch_json_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $request = $this->getRequest();
    $query_params = $request->query->all();
    $config = $this->config('reqresimport.settings');
    if (empty($query_params['vars_from_fetchjson_form'])) {
      $vars = [
        'url' => $config->get('default_url'),
        'endpoint' => $config->get('default_endpoint'),
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

    $form['endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Endpoint'),
      '#required' => TRUE,
      '#default_value' => $vars['endpoint'],
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
    $vars_to_send['endpoint'] = $form_state->getValue('endpoint');
    $vars_to_send['parameter'] = $form_state->getValue('parameter');
    $vars_to_send['parameter_value'] = $form_state->getValue('parameter_value');
    $form_state->setRedirect('reqresimport.fetch_json_test', [ 'vars_from_fetchjson_form' => $vars_to_send ]);
  }

}
