<?php

declare(strict_types=1);

namespace Drupal\baltic_calc\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\baltic_calc\Form\BalticCalcForm;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller routines for Loan calculator routes.
 */
class BalticCalcController extends ControllerBase {

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
    $state = $request->getSession()->get('baltic_calc')
      ?: $this->config('baltic_calc.settings')->get('baltic_calc')
      ?: [];

    return [
      'form' => $this->formBuilder()->getForm(BalticCalcForm::class),
    ];
  }

}
