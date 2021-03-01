<?php

declare(strict_types=1);

namespace Drupal\baltic_calc\Form;

use GuzzleHttp\ClientInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a loan calculator form.
 */
class BalticCalcForm extends FormBase {

  /**
   * Guzzle\Client instance.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * BalticCalcForm constructor.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   Guzzle rest client.
   */
  public function __construct(
    ClientInterface $http_client
  ) {
    $this->httpClient = $http_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    $form = new static(
      $container->get('http_client')
    );
    $form->setStringTranslation($container->get('string_translation'));
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'baltic_calc_form';
  }

  /**
   * {@inheritdoc}
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $defaults = $this->config('baltic_calc.settings')->get('baltic_calc')
      ?: [];

    $form['#tree'] = TRUE;

    // Get loan API calculation from AJAX request.
    $calculation = $form_state->get('response');
    // We have to ensure that there is at least one name field.
    // @todo create user error handler. When something wrong with AJAX/API.
    if ($calculation === NULL) {
      $calculation = self::calculationApiRequest(
        $defaults['loan_amount'],
        $defaults['interest_rate'],
        $defaults['loan_years'],
        $defaults['num_pmt_per_year']
      );

    }

    // Loan summary field set.
    $form['summary'] = [
      '#type' => 'fieldset',
      '#title' => t('Loan Summary'),
      '#prefix' => '<div id="summary-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];

    $form['summary']['scheduled_payment'] = [
      '#type' => 'markup',
      '#markup' => '<div>' .
      $this->t('Scheduled monthly payment:') . ' ' . $calculation['scheduled_monthly_payment'] .
      '</div>',
    ];

    $form['summary']['scheduled_number_payments'] = [
      '#type' => 'markup',
      '#markup' => '<div>' .
      $this->t('Scheduled number of Payments:') . ' ' . $calculation['scheduled_num_of_pmt'] .
      '</div>',
    ];
    // }
    $form['values'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Enter Values'),
      '#prefix' => '<div id="values-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];

    $form['values']['loan_amount'] = [
      '#type' => 'range',
      '#title' => $this->t('Loan Amount'),
      '#default_value' => $defaults['loan_amount'] ?? '',
      '#required' => TRUE,
      '#min' => 1000,
      '#max' => 60000,
      '#step' => 1000,
      '#description' => '<output id="num">' . $defaults['loan_amount'] ?? '' . '</output>',
      '#attributes' => [
        'oninput' => 'num.value = this.value',
      ],
    ];

    $form['values']['interest_rate'] = [
      '#type' => 'number',
      '#title' => $this->t('Annual Interest Rate'),
      '#default_value' => $defaults['interest_rate'] ?? '',
      '#required' => TRUE,
      '#step' => 0.01,
      '#min' => 0.00,
    ];

    $form['values']['loan_years'] = [
      '#type' => 'number',
      '#title' => $this->t('Loan Period in Years'),
      '#default_value' => $defaults['loan_years'] ?? '',
      '#required' => TRUE,
      '#min' => 1,
      '#max' => 30,
      '#step' => 1,

    ];

    $form['values']['num_pmt_per_year'] = [
      '#type' => 'number',
      '#title' => $this->t('Number of Payments Per Year'),
      '#default_value' => $defaults['num_pmt_per_year'] ?? '',
      '#required' => TRUE,
      '#min' => 1,
      '#max' => 12,
      '#step' => 1,
    ];
    $form['values']['actions'] = [
      '#type' => 'actions',
    ];
    $form['values']['actions']['calculate'] = [
      '#type' => 'submit',
      '#value' => $this->t('Calculate'),
      '#submit' => ['::loanSummaryCalculation'],
      '#ajax' => [
        'callback' => '::loanSummaryCallback',
        'wrapper' => 'summary-fieldset-wrapper',
      ],
    ];
    $form['lender_name'] = [
      '#title' => $this->t('Lender name'),
      '#type' => 'textfield',
      '#default_value' => 'John Walker',
    ];

    return $form;
  }

  /**
   * Callback for ajax enabled button.
   *
   * Selects and returns the fieldset with API calculation in it.
   *
   * @param array $form
   *   Form construct.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state.
   *
   * @return mixed
   *   Wrapper.
   */
  public function loanSummaryCallback(array &$form, FormStateInterface $form_state) {
    return $form['summary'];
  }

  /**
   * Submit handler for the "Calculate" button.
   *
   * Does loan calculation API request and rebuilds form.
   *
   * @param array $form
   *   Form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function loanSummaryCalculation(array &$form, FormStateInterface $form_state) {
    $loan_amount = $form_state->getValue(['values', 'loan_amount']);
    $interest_rate = $form_state->getValue(['values', 'interest_rate']);
    $loan_years = $form_state->getValue(['values', 'loan_years']);
    $num_pmt_per_year = $form_state->getValue(['values', 'num_pmt_per_year']);

    // Basic form validation.
    if ($interest_rate > 10) {
      $this->messenger()->addWarning($this->t('Interest rate might be too high.'));
    }

    $response = self::calculationAPIRequest($loan_amount, $interest_rate, $loan_years, $num_pmt_per_year);
    $form_state->set('response', $response);


    // Since our buildForm() method relies on API calculation values so, to
    // generate 'calculation' data, we have to tell the form to rebuild. If we
    // don't do this, the form builder will not call buildForm().
    $form_state->setRebuild();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    // @todo calculator form submit here.
  }

  /**
   * Makes a simple request to loan calculation API.
   *
   * @param mixed $loan_amount
   *   Loan amount.
   * @param mixed $interest_rate
   *   Interest rate in integer.
   * @param mixed $loan_years
   *   Loan years in integer.
   * @param mixed $num_pmt_per_year
   *   Number of payments per year. Integer.
   *
   * @return array
   *   API response in array.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   *
   * @todo Probably good to make it as service.
   */
  public function calculationApiRequest($loan_amount, $interest_rate, $loan_years, $num_pmt_per_year): array {
    $host = \Drupal::request()->getSchemeAndHttpHost();

    $request = $this->httpClient->request('GET', $host . '/api/baltic-calc', [
      'query' => [
        '_format' => 'json',
        'loan_amount' => $loan_amount,
        'interest_rate' => $interest_rate,
        'loan_years' => $loan_years,
        'num_pmt_per_year' => $num_pmt_per_year,
      ],
    ]);
    if ($request->getBody()) {
      return json_decode($request->getBody()->read(1024), TRUE);
    }
    return [];
  }

}
