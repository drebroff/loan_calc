<?php

namespace Drupal\loan_calc\Plugin\rest\resource;

use Drupal\Core\Config\ImmutableConfig;
use Drupal\loan_calc\LoanCalcCalculusInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a loan calculator REST resource.
 *
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
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\loan_calc\LoanCalcCalculusInterface $loanCalcCalculus
   *   Loan calculation service.
   * @param \Symfony\Component\HttpFoundation\Request $currentRequest
   *   The request.
   * @param \Drupal\Core\Config\ImmutableConfig $config
   *   The configuration.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    LoanCalcCalculusInterface $loanCalcCalculus,
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
      $container->get('logger.factory')->get('loan_calc'),
      $container->get('loan_calc.calculus'),
      $container->get('request_stack')->getCurrentRequest(),
      $container->get('config.factory')->get('loan_calc.settings')
    );
  }

  /**
   * Responds to entity GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The resource response.
   */
  public function get() {
    $fields = array_keys(
      $this->config->get('loan_calc')
    );

    foreach ($fields as $field) {
      $params[$field] = $this->currentRequest->get($field) ?? 0;
    }

    if (empty($params)) {
      $this->logger->error('Loan Calc API error: No params.');

      return (new ResourceResponse(['error' => '1']))
        ->addCacheableDependency(NULL);
    }

    $values = array_values($params);
    $result = $this->loanCalcCalculus->calculate(...$values);

    if (empty($result['summary'])) {
      $this->logger->error('Loam Calc API error: No summary.');

      return (new ResourceResponse(['error' => '2']))
        ->addCacheableDependency(NULL);
    }

    $this->logger->info('Loan Calc API<br> <b>Request:</b> <br><pre>@request</pre>' .
        '<br><b>Response:</b> <br><pre>@response</pre>', [
          '@request' => print_r($params, TRUE),
          '@response' => print_r($result['summary'], TRUE),
        ]
    );

    return (new ResourceResponse($result['summary']))
      ->addCacheableDependency(NULL);
  }

}
