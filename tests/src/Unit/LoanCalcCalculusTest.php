<?php

namespace Drupal\Tests\loan_calc\Unit;

use Drupal\loan_calc\LoanCalcCalculus;
use Drupal\Tests\UnitTestCase;

/**
 * Tests a loan calculation service.
 *
 * @coversDefaultClass \Drupal\loan_calc\LoanCalcCalculus
 *
 * @group loan_calc
 */
class LoanCalcCalculusTest extends UnitTestCase {

  /**
   * Data provider for testPaymentAmount().
   *
   * @return array
   *   Array of test data:
   *   - Expected assertion result.
   *   - Arguments passed to method being tested.
   */
  public function provideTestPaymentAmount() {
    return [
      [0, [0, 1, 1, 1]],
      [1010, [1000, 1, 1, 1]],
      [1015, [1000, 1.5, 1, 1]],
      [209.09, [1000, 1.5, 5, 1]],
      [104.17, [1000, 1.5, 5, 2]],
    ];
  }

  /**
   * Tests the paymentAmount method.
   *
   * @param float $expected
   *   Expected testing result.
   * @param array $args
   *   Arguments for testing method.
   *
   * @throws \ReflectionException
   *
   * @covers ::paymentAmount
   *
   * @dataProvider provideTestPaymentAmount
   */
  public function testPaymentAmount($expected, array $args) {
    $service = new LoanCalcCalculus();
    $ref_method = new \ReflectionMethod($service, 'paymentAmount');
    $ref_method->setAccessible(TRUE);
    $actual = round($ref_method->invokeArgs($service, $args), 2);
    $this->assertEquals($expected, $actual);
  }

  /**
   * Data provider for testScheduledPaymentInfo().
   *
   * @return array
   *   Array of test data:
   *   - Expected assertion result.
   *   - Arguments passed to method being tested.
   */
  public function provideTestScheduledPaymentInfo() {
    return [
      [
        [
          [
            'pay_num' => 1,
            'pay_date' => '2014-07-01',
            'beg_bal' => 1000,
            'sched_pay' => 254.71,
            'extra_pay' => 300,
            'total_pay' => 554.71,
            'princ' => 547.21,
            'int' => 7.5,
            'end_bal' => 452.79,
            'cum_int' => 7.5,
          ],
          [
            'pay_num' => 2,
            'pay_date' => '2015-01-01',
            'beg_bal' => 452.79,
            'sched_pay' => 254.71,
            'extra_pay' => 198.09,
            'total_pay' => 452.79,
            'princ' => 449.4,
            'int' => 3.4,
            'end_bal' => 0,
            'cum_int' => 10.9,
          ],
        ],
        [1000, 1.5, 2, 2, '2014-01-01', 300],
      ],
    ];
  }

  /**
   * Tests the scheduledPaymentInfo method.
   *
   * @param array $expected
   *   Expected information of each scheduled payment.
   * @param array $args
   *   Arguments for testing method.
   *
   * @covers ::scheduledPaymentInfo
   *
   * @dataProvider provideTestScheduledPaymentInfo
   */
  public function testScheduledPaymentInfo(array $expected, array $args) {
    $service = new LoanCalcCalculus();
    foreach ($service->scheduledPaymentInfo(...$args) as $key => $actual) {
      $this->assertArrayEquals($expected[$key], $actual);
    }
  }

}
