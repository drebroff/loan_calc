<?php

namespace Drupal\loan_calc\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * @Block(
 *   id = "loan_calc_block",
 *   admin_label = @Translation("Loan Calculator"),
 * )
 */
class LoanCalcBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\loan_calc\Form\LoanCalcForm');

    return $form;
  }
}
