<?php

declare(strict_types=1);

namespace Drupal\loan_calc\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\loan_calc\Form\LoanCalcForm;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller routines for Loan calculator routes.
 */
class LoanCalcController extends ControllerBase {

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

    return [
      'form' => $this->formBuilder()->getForm(LoanCalcForm::class),
    ];
  }

}
