<?php

declare(strict_types=1);

namespace Drupal\loan_calc\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\loan_calc\Form\LoanCalcForm;
use Drupal\loan_calc\LoanCalcCalculusInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller routines for Loan calculator routes.
 */
class LoanCalcController extends ControllerBase {

  /**
   * Loan calculus service.
   *
   * @var \Drupal\loan_calc\LoanCalcCalculusInterface
   */
  protected LoanCalcCalculusInterface $loanCalcCalculus;

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
  public static function create(ContainerInterface $container): self {
    return new static(
      $container->get('loan_calc.calculus')
    );
  }

  /**
   * Action to display Loan Calculator page.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return array
   *   Action response as render array.
   */
  public function page(Request $request): array {
    $state = $request->getSession()->get('loan_calc')
      ?: $this->config('loan_calc.settings')->get('loan_calc')
      ?: [];

    $values = $this->loanCalcCalculus->extractArguments($state);

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
      foreach ($this->loanCalcCalculus->scheduledPaymentInfo(...$values) as $payment) {
        $table['#rows'][] = $payment;
      }
    }

    return [
      'header' => [
        '#markup' => '<p>' . $this->t('Enter loan values') . '</p>',
      ],
      'form' => $this->formBuilder()->getForm(LoanCalcForm::class),
      'table' => $table,
    ];
  }

}
