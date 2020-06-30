<?php

declare(strict_types=1);

namespace Drupal\loan_calc\Plugin\rest\resource;

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

  /**
   * Loan calculation service.
   *
   * @var \Drupal\loan_calc\LoanCalcCalculusInterface
   */
  protected LoanCalcCalculusInterface $loanCalcCalculus;

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
   */
  public function __construct(
    array $configuration,
    string $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    LoanCalcCalculusInterface $loanCalcCalculus
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->loanCalcCalculus = $loanCalcCalculus;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('loan_calc'),
      $container->get('loan_calc.calculus')
    );
  }

  /**
   * Responds to entity GET requests.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The resource response.
   */
  public function get(Request $request): ResourceResponse {
    $params = $request->query->all();

    if (empty($params)) {
      $this->logger->error('Loan Calc API error: No params.');

      return (new ResourceResponse(['error' => '1']))
        ->addCacheableDependency(NULL);
    }

    $values = $this->loanCalcCalculus->extractArguments($params);
    $summary = $this->loanCalcCalculus->loanSummary(...$values);

    if (empty($summary)) {
      $this->logger->error('Loan Calc API error: No summary.');

      return (new ResourceResponse(['error' => '2']))
        ->addCacheableDependency(NULL);
    }

    $this->logger->info('Loan Calc API<br> <b>Request:</b> <br><pre>@request</pre>' .
        '<br><b>Response:</b> <br><pre>@response</pre>', [
          '@request' => print_r($params, TRUE),
          '@response' => print_r($summary, TRUE),
        ]
    );

    $response = new ResourceResponse($summary);
    $response->getCacheableMetadata()
      ->addCacheContexts(['url.query_args']);

    return $response;
  }

}
