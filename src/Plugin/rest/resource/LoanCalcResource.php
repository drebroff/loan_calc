<?php

namespace Drupal\loan_calc\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;

/**
 * @RestResource(
 *   id = "loan_calc_resource",
 *   label = @Translation("Custom rest resource"),
 *   uri_paths = {
 *     "canonical" = "/api/loan-calc",
 *     "https://www.drupal.org/link-relations/create" = "/api/loan-calc"
 *   }
 * )
 */
class LoanCalcResource extends ResourceBase {

    /**
     * Responds to entity GET requests.
     *
     * @return \Drupal\rest\ResourceResponse
     */
    public function get() {
        return new ResourceResponse("Implement REST State GET!");
    }

}
