<?php

namespace Drupal\loan_calc\Form;

/**
 * Trait LoanCalcFormTrait.
 */
trait LoanCalcFormTrait {

  /**
   * Gets a form definition.
   *
   * @param array|null $defaults
   *   (optional) The form defaults.
   *
   * @return array
   *   An associative array containing the structure of the form.
   */
  private function getFormDefinition($defaults = NULL) {
    $form = [];

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

    $form['loan_start'] = [
      '#type' => 'date',
      '#title' => $this->t('Start Date of Loan'),
      '#default_value' => $defaults['loan_start'] ?? '',
      '#required' => TRUE,
    ];

    $form['scheduled_extra_payments'] = [
      '#type' => 'number',
      '#title' => $this->t('Optional Extra Payments'),
      '#default_value' => $defaults['scheduled_extra_payments'] ?? '',
      '#min' => 0,
    ];

    return $form;
  }

}
