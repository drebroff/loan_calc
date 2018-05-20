<?php

namespace Drupal\loan_calc\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class LoanCalcForm extends FormBase {

  use LoanCalcFormTrait;

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'loan_calc_form';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $defaults = \Drupal::state()->get('loan_calc') ??
      $this->config('loan_calc.settings')->get('loan_calc');

    $form = $this->getFormDefinition($defaults);

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Calculate'),
      '#button_type' => 'primary',
    ];
    $form['actions']['reset'] = [
      '#type' => 'submit',
      '#value' => $this->t('Reset'),
      '#submit' => [
        '::resetFormHandler'
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::state()->set('loan_calc', $form_state->getValues());
    $form_state->setRedirect('loan_calc.page');
  }

  /**
   * Reset form values to defaults
   */
  public function resetFormHandler(array &$form, FormStateInterface $form_state) {
    \Drupal::state()->delete('loan_calc');
  }
}
