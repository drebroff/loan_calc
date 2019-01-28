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
      [209.09, [1000, 1.5, 1, 5]],
      [104.17, [1000, 1.5, 2, 10]],
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

}
