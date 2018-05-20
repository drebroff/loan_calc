<?php

namespace Drupal\loan_calc\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class LoanCalcConfigForm extends ConfigFormBase {

  use LoanCalcFormTrait;

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'loan_calc_config_form';
  }

  /**
   * {@inheritdoc}.
   */
  protected function getEditableConfigNames() {
    return [
      'loan_calc.settings'
    ];
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('loan_calc.settings');

    $form = $this->getFormDefinition(
      $config->get('loan_calc')
    );

    array_unshift($form, [
      'header' => [
        '#markup' => '<p>' . t('Enter default Loan Calculator values:') . '</p>',
      ],
    ]);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->configFactory->getEditable('loan_calc.settings');

    $fields = array_keys(
      $this->getFormDefinition()
    );

    array_walk($fields, function($field) use ($config, $form_state) {
      $config->set("loan_calc.{$field}", $form_state->getValue($field));
    });

    $config->save();

    parent::submitForm($form, $form_state);
  }

}
