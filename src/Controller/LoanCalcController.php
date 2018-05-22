<?php

namespace Drupal\loan_calc\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\loan_calc\LoanCalcCalculus;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoanCalcController extends ControllerBase {

  protected $loanCalcCalculus;

  /**
   * LoanCalcController constructor.
   *
   * @param \Drupal\loan_calc\LoanCalcCalculus $loanCalcCalculus
   */
  public function __construct(LoanCalcCalculus $loanCalcCalculus) {
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
    $values = array_values(
      \Drupal::state()->get('loan_calc')
    );

    return [
      'header' => [
        '#markup' => '<p>' . t('Enter loan values') . '</p>',
      ],
      'form' => \Drupal::formBuilder()->getForm('Drupal\loan_calc\Form\LoanCalcForm'),
      'result' => [
        '#markup' => '<pre>' . print_r($this->loanCalcCalculus->calculate(...$values), TRUE) . '</pre>',
      ],
    ];
  }

}
