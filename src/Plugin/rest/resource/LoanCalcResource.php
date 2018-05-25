<?php

namespace Drupal\loan_calc\Plugin\rest\resource;

use Drupal\Core\Config\ImmutableConfig;
use Drupal\loan_calc\LoanCalcCalculus;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @RestResource(
 *   id = "loan_calc_resource",
 *   label = @Translation("Loan Calc rest resource"),
 *   uri_paths = {
 *     "canonical" = "/api/loan-calc",
 *     "https://www.drupal.org/link-relations/create" = "/api/loan-calc"
 *   }
 * )
 */
class LoanCalcResource extends ResourceBase {

  protected $loanCalcCalculus;
  protected $currentRequest;
  protected $config;

  /**
   * LoanCalcResource constructor.
   *
   * @param \Drupal\loan_calc\LoanCalcCalculus $loanCalcCalculus
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    LoanCalcCalculus $loanCalcCalculus,
    Request $currentRequest,
    ImmutableConfig $config
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->loanCalcCalculus = $loanCalcCalculus;
    $this->currentRequest = $currentRequest;
    $this->config = $config;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('loan_calc.calculus'),
      $container->get('request_stack')->getCurrentRequest(),
      $container->get('config.factory')->get('loan_calc.settings')
    );
  }

  /**
   * Responds to entity GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {
    $fields = array_keys(
      $this->config->get('loan_calc')
    );

    foreach ($fields as $field) {
      $params[$field] = $this->currentRequest->get($field) ?? 0;
    }

    if (empty($params)) {
      \Drupal::logger('loan_calc')->error('Loan Calc API error: No params.');
      return (new ResourceResponse(['error' => '1']))->addCacheableDependency(null);
    }

    $values = array_values($params);
    $result = $this->loanCalcCalculus->calculate(...$values);

    if (empty($result['summary'])) {
      \Drupal::logger('loan_calc')->error('Loam Calc API error: No summary.');
      return (new ResourceResponse(['error' => '2']))->addCacheableDependency(null);
    }

    \Drupal::logger('loan_calc')->info(
      'Loan Calc API<br> <b>Request:</b> <br><pre>@request</pre>' .
      '<br><b>Response:</b> <br><pre>@response</pre>', [
        '@request' => print_r($params, TRUE),
        '@response' => print_r($result['summary'], TRUE)
      ]
    );
    return (new ResourceResponse($result['summary']))->addCacheableDependency(null);
  }

}
