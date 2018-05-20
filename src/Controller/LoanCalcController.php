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
    return [
      'header' => [
        '#markup' => '<p>' . t('Enter loan values') . '</p>',
      ],
      'form' => \Drupal::formBuilder()->getForm('Drupal\loan_calc\Form\LoanCalcForm'),
      'summary' => [
        '#markup' => $this->loanCalcCalculus->summary(),
      ],
      'payments' => [
        '#markup' => $this->loanCalcCalculus->payments()
      ],
    ];
  }

}
