<?php

declare(strict_types=1);

namespace Drupal\loan_calc\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Provides a loan calculator form.
 */
class LoanCalcForm extends FormBase {

  use LoanCalcFormTrait;

  /**
   * The session.
   *
   * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
   */
  protected SessionInterface $session;

  /**
   * LoanCalcForm constructor.
   *
   * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
   *   The session.
   */
  public function __construct(SessionInterface $session) {
    $this->session = $session;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    $form = new static(
      $container->get('session')
    );
    $form->setStringTranslation($container->get('string_translation'));
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'loan_calc_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $defaults = $this->session->get('loan_calc')
      ?: $this->config('loan_calc.settings')->get('loan_calc')
      ?: [];

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
        '::resetFormHandler',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $fields = array_keys(
      $this->getFormDefinition()
    );

    $values = [];

    foreach ($fields as $field) {
      $values[$field] = $form_state->getValue($field);
    }

    if (!empty($values)) {
      $this->session->set('loan_calc', $values);
      $this->logger('loan_calc')->info(
        'Loan Calculator values entered: <br><pre>@values</pre>',
        ['@values' => print_r($values, TRUE)]
      );
    }

    $form_state->setRedirect('loan_calc.page');
  }

  /**
   * Reset form values to defaults.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function resetFormHandler(array &$form, FormStateInterface $form_state): void {
    $this->session->remove('loan_calc');
    $this->logger('loan_calc')
      ->notice('Loan Calculator reset to defaults.');
  }

}
