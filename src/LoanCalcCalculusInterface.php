<?php

namespace Drupal\loan_calc;

/**
 * Provides an interface for a loan calculation service.
 */
interface LoanCalcCalculusInterface {

    /**
     * Calculates mortgage loan amortization schedule.
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
    public function calculate($loan_amount, $interest_rate, $loan_years, $num_pmt_per_year, $loan_start, $scheduled_extra_payments = 0);

}
