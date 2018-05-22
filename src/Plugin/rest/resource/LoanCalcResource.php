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
 *   label = @Translation("Custom rest resource"),
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
      $params[] = $this->currentRequest->get($field) ?? 0;
    }

    if (empty($params)) {
      return new ResourceResponse(['error' => '1']);
    }

    $result = $this->loanCalcCalculus->calculate(...$params);

    if (empty($result['summary'])) {
      return new ResourceResponse(['error' => '2']);
    }

    $summary = array_map(function($value) {
      return round($value, 2);
    }, $result['summary']);

    return (new ResourceResponse($summary))->addCacheableDependency(null);
  }

}
