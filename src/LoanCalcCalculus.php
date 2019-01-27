<?php

namespace Drupal\loan_calc;

/**
 * Provides a loan calculation service implementation.
 */
class LoanCalcCalculus implements LoanCalcCalculusInterface {

  /**
   * {@inheritdoc}
   */
  public function loanSummary($loan_amount, $interest_rate, $loan_years, $num_pmt_per_year, $loan_start, $scheduled_extra_payments = 0) {
    $scheduled_monthly_payment = 0;
    $scheduled_num_of_pmt = $loan_years * $num_pmt_per_year;
    $actual_num_of_pmt = 0;
    $total_int = 0;
    $total_early_pmt = 0;

    foreach ($this->scheduledPaymentInfo($loan_amount, $interest_rate, $loan_years, $num_pmt_per_year, $loan_start, $scheduled_extra_payments) as $payment) {
      $scheduled_monthly_payment = $payment['sched_pay'] ?? 0;
      $actual_num_of_pmt++;
      $total_int = $payment['cum_int'] ?? 0;
      $total_early_pmt += $payment['extra_pay'] ?? 0;
    }

    $summary = compact(
      'scheduled_monthly_payment',
      'scheduled_num_of_pmt',
      'actual_num_of_pmt',
      'total_early_pmt',
      'total_int'
    );

    array_walk($summary, function (&$value) {
      $value = round($value, 2);
    });

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function scheduledPaymentInfo($loan_amount, $interest_rate, $loan_years, $num_pmt_per_year, $loan_start, $scheduled_extra_payments = 0) {
    $scheduled_num_of_pmt = $loan_years * $num_pmt_per_year;
    $sched_pay = $this->paymentAmount($loan_amount, $interest_rate, $num_pmt_per_year, $scheduled_num_of_pmt);

    $beg_bal = (int) $loan_amount;
    $pay_num = 1;
    $cum_int = 0;
    $total_early_pmt = 0;

    while ($beg_bal > 0) {
      // Payment Date.
      $shift = $pay_num * 12 / $num_pmt_per_year;
      $pay_date = date('Y-m-d', strtotime("+{$shift} months", strtotime($loan_start)));

      // Extra Payment.
      if ($sched_pay + $scheduled_extra_payments < $beg_bal) {
        $extra_pay = (int) $scheduled_extra_payments;
      }
      elseif ($beg_bal - $sched_pay > 0) {
        $extra_pay = $beg_bal - $sched_pay;
      }
      else {
        $extra_pay = 0;
      }

      // Total Payment.
      if ($sched_pay + $extra_pay < $beg_bal) {
        $total_pay = $sched_pay + $extra_pay;
      }
      else {
        $total_pay = $beg_bal;
      }

      $total_early_pmt += $extra_pay;
      $rate_per_pmt = ($interest_rate / 100) / $num_pmt_per_year;
      $int = $beg_bal * $rate_per_pmt;
      $princ = $total_pay - $int;
      $cum_int += $int;

      // Ending Balance.
      if ($sched_pay + $extra_pay < $beg_bal) {
        $end_bal = $beg_bal - $princ;
      }
      else {
        $end_bal = 0;
      }

      $payment = compact(
        'pay_num',
        'pay_date',
        'beg_bal',
        'sched_pay',
        'extra_pay',
        'total_pay',
        'princ',
        'int',
        'end_bal',
        'cum_int'
      );

      array_walk($payment, function (&$val, $idx) {
        if ($idx != 'pay_num' && $idx != 'pay_date') {
          $val = round($val, 2);
        }
      });

      $pay_num++;
      $beg_bal = $end_bal;

      yield $payment;
    }
  }

  /**
   * Calculates a periodic payment amount by financial formula.
   *
   * @param $loan_amount
   *   Loan Amount.
   * @param $interest_rate
   *   Annual Interest Rate.
   * @param $num_pmt_per_year
   *   Number of Payments Per Year.
   * @param $scheduled_num_of_pmt
   *   Scheduled Number of Payments.
   *
   * @return float|int
   */
  protected function paymentAmount($loan_amount, $interest_rate, $num_pmt_per_year, $scheduled_num_of_pmt) {
    $rate_per_pmt = ($interest_rate / 100) / $num_pmt_per_year;

    $scheduled_monthly_payment =
      ($loan_amount * pow(1 + $rate_per_pmt, $scheduled_num_of_pmt) * $rate_per_pmt) /
      (pow(1 + $rate_per_pmt, $scheduled_num_of_pmt) - 1);

    return $scheduled_monthly_payment;
  }

}
