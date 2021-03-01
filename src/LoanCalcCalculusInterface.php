<?php

declare(strict_types=1);

namespace Drupal\loan_calc;

/**
 * Provides an interface for a loan calculation service.
 */
interface LoanCalcCalculusInterface {

  /**
   * Extracts formula argument values from input data.
   *
   * @param array $input
   *   Input data as associative array.
   *
   * @return array
   *   Numeric array of argument values in specific order and types.
   */
  public function extractArguments(array $input): array;

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
   *
   * @return array
   *   Loan Amortization Schedule
   */
  public function loanSummary(int $loan_amount, float $interest_rate, int $loan_years, int $num_pmt_per_year): array;

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
   *
   * @return \Generator
   *   Payment information.
   */
  public function scheduledPaymentInfo(
    int $loan_amount,
    float $interest_rate,
    int $loan_years,
    int $num_pmt_per_year): \Generator;

}
