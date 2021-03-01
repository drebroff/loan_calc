# Loan Calculator module for Drupal 8 for Baltic Amodeus

## The task:

1. Create a simple calculator by the given excel file [xls failą](resources/Calculator.xlsx);
2. Default values should be configurable through the administration page.
3. All the texts should be translatable.
4. (OPTIONAL) If a new language is added, settings fields for that language should appear.
5. All initial settings should come together with a new module.
6. Calculator should be available as a page and as a block (At least one of these).
7. Calculations should be done after the AJAX form submit (Not JS).
8. Additional REST service should be created. After the form submits, "Enter Values" should be sent, and "Loan Summary" calculated values returned.
9. User usage of the calculator should be logged.
10. Input fields should have validation.
11. (OPTIONAL) The "Loan Amount" field for the user should be a slider done with JS.
12. Drupal version: 8.9
13. Final result should be a single module .zip file.

## Requirements

* drupal 8.9+
* PHP 7.4
* Bartic theme set as default (optional)

## Usage

1. Install batlic_calc module
2. With baltic calc page at http://example.com/baltic-calc

REST API usage example:
```
http://example.com/api/baltic-calc?_format=json
    &loan_amount=39999
    &interest_rate=2
    &loan_years=8
    &num_pmt_per_year=12
```

[![Drupal](https://img.shields.io/badge/Drupal-9-%2353B0EB "Supports Symfony 3.x")](https://drupal.org/9)
