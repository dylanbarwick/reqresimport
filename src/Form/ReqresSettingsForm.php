<?php

declare(strict_types=1);

namespace Drupal\reqresimport\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure reqresimport settings for this site.
 */
final class ReqresSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'reqresimport_reqres_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['reqresimport.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['default_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default URL'),
      '#default_value' => $this->config('reqresimport.settings')->get('default_url'),
      '#description' => $this->t('Starting with `https://` with no trailing slash.'),
    ];
    $form['default_endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default endpoint'),
      '#default_value' => $this->config('reqresimport.settings')->get('default_endpoint'),
      '#description' => $this->t('The path that specifies an endpoint on the API, start with a forward slash.'),
    ];
    $form['default_parameter'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default parameter'),
      '#default_value' => $this->config('reqresimport.settings')->get('default_parameter'),
      '#description' => $this->t('If a parameter should be passed, specify it here.'),
    ];
    $form['default_parameter_value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default parameter value'),
      '#default_value' => $this->config('reqresimport.settings')->get('default_parameter_value'),
      '#description' => $this->t('The value of the parameter.'),
    ];
    // Integer field for the cron interval.
    $form['cron_interval'] = [
      '#type' => 'textfield',
      '#attributes' => [
        'type' => 'number',
        'min' => 0,
      ],
      '#title' => $this->t('Number of seconds between each cron run'),
      '#default_value' => $this->config('reqresimport.settings')->get('cron_interval'),
      '#description' => $this->t('Set to 0 to disable the cron job.'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('reqresimport.settings')
      ->set('default_url', $form_state->getValue('default_url'))
      ->set('default_endpoint', $form_state->getValue('default_endpoint'))
      ->set('default_parameter', $form_state->getValue('default_parameter'))
      ->set('default_parameter_value', $form_state->getValue('default_parameter_value'))
      ->set('cron_interval', $form_state->getValue('cron_interval'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
