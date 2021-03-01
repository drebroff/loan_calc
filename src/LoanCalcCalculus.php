<?php

declare(strict_types=1);

namespace Drupal\loan_calc;

/**
 * Provides a loan calculation service implementation.
 */
class LoanCalcCalculus implements LoanCalcCalculusInterface {

  /**
   * {@inheritdoc}
   */
  public function extractArguments(array $input): array {
    $method = new \ReflectionMethod(__CLASS__, 'loanSummary');
    $arguments = [];

    foreach ($method->getParameters() as $argument) {
      $value = $input[$argument->getName()] ?? 0;
      $type = $argument->getType()->getName();
      settype($value, $type);
      $arguments[] = $value;
    }

    return $arguments;
  }

  /**
   * {@inheritdoc}
   */
  public function loanSummary(
    int $loan_amount,
    float $interest_rate,
    int $loan_years,
    int $num_pmt_per_year
    ): array {
    $scheduled_monthly_payment = $this->scheduledMonthlyPayment($loan_amount, $interest_rate, $loan_years, $num_pmt_per_year);
    $scheduled_num_of_pmt = $loan_years * $num_pmt_per_year;

    $summary = compact(
      'scheduled_monthly_payment',
      'scheduled_num_of_pmt',
    );

    // phpcs:ignore DrupalPractice.CodeAnalysis.VariableAnalysis.UndefinedVariable
    return array_map(fn($value) => round($value, 2), $summary);
  }

  /**
   * Calculates a periodic payment amount by financial formula.
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
   * @return float
   *   Payment amount.
   */
  protected function scheduledMonthlyPayment(
    int $loan_amount,
    float $interest_rate,
    int $loan_years,
    int $num_pmt_per_year): float {
    return (($loan_amount * ($interest_rate / 100) * $loan_years + $loan_amount) / ($loan_years * $num_pmt_per_year));

  }

  /**
   * Preparation for payment table.
   */
  public function scheduledPaymentInfo(
    int $loan_amount,
    float $interest_rate,
    int $loan_years,
    int $num_pmt_per_year): \Generator {
    // @todo Implement scheduledPaymentInfo() method.
  }

}
