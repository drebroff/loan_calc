<?php

declare(strict_types=1);

namespace Drupal\loan_calc;

/**
 * Provides an interface for a loan calculation service.
 */
interface LoanCalcCalculusInterface {

  /**
   * Calculates mortgage loan amortization summary.
   *
   * @param int $loan_amount
   *   Loan Amount.
   * @param float $interest_rate
   *   Annual Interest Rate.
   * @param int $loan_years
   *   Loan Period in Years.
   * @param int $num_pmt_per_year
   *   Number of Payments Per Year.
   * @param string $loan_start
   *   Start Date of Loan.
   * @param int $scheduled_extra_payments
   *   (optional) Optional Extra Payments.
   *
   * @return array
   *   Loan Amortization Schedule
   */
  public function loanSummary(int $loan_amount, float $interest_rate, int $loan_years, int $num_pmt_per_year, string $loan_start, int $scheduled_extra_payments = 0): array;

  /**
   * Generates information for each scheduled payment.
   *
   * @param int $loan_amount
   *   Loan Amount.
   * @param float $interest_rate
   *   Annual Interest Rate.
   * @param int $loan_years
   *   Loan Period in Years.
   * @param int $num_pmt_per_year
   *   Number of Payments Per Year.
   * @param string $loan_start
   *   Start Date of Loan.
   * @param int $scheduled_extra_payments
   *   (optional) Optional Extra Payments.
   *
   * @return \Generator
   *   Payment information.
   */
  public function scheduledPaymentInfo(int $loan_amount, float $interest_rate, int $loan_years, int $num_pmt_per_year, string $loan_start, int $scheduled_extra_payments = 0): \Generator;

}
