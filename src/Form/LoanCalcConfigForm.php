<?php

declare(strict_types=1);

namespace Drupal\loan_calc\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a loan calculator configuration form.
 */
class LoanCalcConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'loan_calc_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [
      'loan_calc.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $defaults = $this->config('loan_calc.settings')->get('loan_calc')
      ?: [];

    $form['loan_amount'] = [
      '#type' => 'number',
      '#title' => $this->t('Loan Amount'),
      '#default_value' => $defaults['loan_amount'] ?? '',
      '#required' => TRUE,
      '#min' => 1000,
    ];

    $form['interest_rate'] = [
      '#type' => 'number',
      '#title' => $this->t('Annual Interest Rate'),
      '#default_value' => $defaults['interest_rate'] ?? '',
      '#required' => TRUE,
      '#step' => 0.01,
      '#min' => 0.00,
    ];

    $form['loan_years'] = [
      '#type' => 'number',
      '#title' => $this->t('Loan Period in Years'),
      '#default_value' => $defaults['loan_years'] ?? '',
      '#required' => TRUE,
      '#min' => 1,
      '#max' => 30,
      '#step' => 1,

    ];

    $form['num_pmt_per_year'] = [
      '#type' => 'number',
      '#title' => $this->t('Number of Payments Per Year'),
      '#default_value' => $defaults['num_pmt_per_year'] ?? '',
      '#required' => TRUE,
      '#min' => 1,
      '#max' => 12,
      '#step' => 1,
    ];

    array_unshift($form, [
      'header' => [
        '#markup' => '<p>' . $this->t('Enter default Loan Calculator values:') . '</p>',
      ],
    ]);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $config = $this->configFactory->getEditable('loan_calc.settings');

    $fields = array_keys(
      $form
    );

    array_walk($fields, function ($field) use ($config, $form_state) {
      $config->set("loan_calc.{$field}", $form_state->getValue($field));
    });

    $config->save();

    $new_config = $this->config('loan_calc.settings')->get('loan_calc');

    $this->logger('loan_calc')->notice(
      'Loan Calculator defaults set to: <br><pre>@values</pre>',
      ['@values' => print_r($new_config, TRUE)]
    );

    parent::submitForm($form, $form_state);
  }

}
