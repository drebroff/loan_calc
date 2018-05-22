<?php

namespace Drupal\loan_calc;

class LoanCalcCalculus {

  /**
   * Calculates mortgage loan amortization schedule.
   *
   * @param $loan_amount
   *   Loan Amount
   * @param $interest_rate
   *   Annual Interest Rate
   * @param $loan_years
   *   Loan Period in Years
   * @param $num_pmt_per_year
   *   Number of Payments Per Year
   * @param $loan_start
   *   Start Date of Loan
   * @param int $scheduled_extra_payments [optional]
   *   Optional Extra Payments
   *
   * @return array
   *   Loan Amortization Schedule
   */
  public function calculate($loan_amount, $interest_rate, $loan_years, $num_pmt_per_year, $loan_start, $scheduled_extra_payments = 0) {
    $rate_per_pmt = ($interest_rate / 100) / $num_pmt_per_year;

    $scheduled_num_of_pmt = $loan_years * $num_pmt_per_year;
    $scheduled_monthly_payment =
      ($loan_amount * pow(1 + $rate_per_pmt, $scheduled_num_of_pmt) * $rate_per_pmt) /
      (pow(1 + $rate_per_pmt, $scheduled_num_of_pmt) - 1)
    ;

    $sched_pay = $scheduled_monthly_payment;
    $beg_bal = $loan_amount;
    $pay_num = 1;
    $cum_int = 0;
    $total_early_pmt = 0;
    $payments = [];

    while ($beg_bal > 0) {
      // Payment Date
      $shift = $pay_num * 12 / $num_pmt_per_year;
      $pay_date = date('Y-m-d', strtotime("+{$shift} months", strtotime($loan_start)));

      // Extra Payment
      if ($sched_pay + $scheduled_extra_payments < $beg_bal) {
        $extra_pay = $scheduled_extra_payments;
      }
      elseif ($beg_bal - $sched_pay > 0) {
        $extra_pay = $beg_bal - $sched_pay;
      }
      else {
        $extra_pay = 0;
      }

      // Total Payment
      if ($sched_pay + $extra_pay < $beg_bal) {
        $total_pay = $sched_pay + $extra_pay;
      }
      else {
        $total_pay = $beg_bal;
      }

      $total_early_pmt += $extra_pay;
      $int = $beg_bal * $rate_per_pmt;
      $princ = $total_pay - $int;
      $cum_int += $int;

      // Ending Balance
      if ($sched_pay + $extra_pay < $beg_bal) {
        $end_bal = $beg_bal - $princ;
      }
      else {
        $end_bal = 0;
      }

      $payments[$pay_num] = compact(
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
      $pay_num++;
      $beg_bal = $end_bal;
    }

    $actual_num_of_pmt = count($payments);
    $total_int = $cum_int;

    $summary = compact(
      'scheduled_monthly_payment',
      'scheduled_num_of_pmt',
      'actual_num_of_pmt',
      'total_early_pmt',
      'total_int'
    );

    return compact(
      'summary',
      'payments'
    );
  }

}
