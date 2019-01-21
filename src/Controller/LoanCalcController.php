<?php

namespace Drupal\loan_calc\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\loan_calc\LoanCalcCalculusInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller routines for Loan calculator routes.
 */
class LoanCalcController extends ControllerBase {

  protected $loanCalcCalculus;

  /**
   * LoanCalcController constructor.
   *
   * @param \Drupal\loan_calc\LoanCalcCalculusInterface $loanCalcCalculus
   *   Loan calculus service.
   */
  public function __construct(LoanCalcCalculusInterface $loanCalcCalculus) {
    $this->loanCalcCalculus = $loanCalcCalculus;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('loan_calc.calculus')
    );
  }

  /**
   * Action to display Loan Calculator page.
   */
  public function page() {
    $state = $this->state()->get('loan_calc')
      ?? $this->config('loan_calc.settings')->get('loan_calc')
      ?? [];

    $values = array_values($state);

    $table = [
      '#type' => 'table',
      '#header' => [
        $this->t('Pmt No.'),
        $this->t('Payment Date'),
        $this->t('Beginning Balance'),
        $this->t('Scheduled Payment'),
        $this->t('Extra Payment'),
        $this->t('Total Payment'),
        $this->t('Principal'),
        $this->t('Interest'),
        $this->t('Ending Balance'),
        $this->t('Cumulative Interest'),
      ],
      '#empty' => $this->t('No values submitted yet.'),
    ];

    if (!empty($values)) {
      $result = $this->loanCalcCalculus->calculate(...$values);
      $table['#rows'] = $result['payments'] ?? [];
    }

    return [
      'header' => [
        '#markup' => '<p>' . $this->t('Enter loan values') . '</p>',
      ],
      'form' => $this->formBuilder()->getForm('Drupal\loan_calc\Form\LoanCalcForm'),
      'table' => $table,
    ];
  }

}
