_# Loan Calculator module for Drupal (a.k.a. loan_calc)

[UAB Baltic Amadeus](https://www.drupal.org/baltic-amadeus) challenge for wannabe drupalists.
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

Module supports both Drupal 8 and Drupal 9.\
PHP 7.4 or higher is required.\
Docker and Docker Compose suggested trying the module.

## Usage

Clone repository and run: ```docker-compose up -d```\
Open in the browser:
[http://localhost:9000](http://localhost:9000)



REST API usage example:
```
http://localhost:9000/api/loan-calc?_format=json
    &loan_amount=50000
    &interest_rate=3
    &loan_years=10
    &num_pmt_per_year=12
    &loan_start=2014-01-01
    &scheduled_extra_payments=100
```

[![Build Status](https://www.travis-ci.org/phpistai/loan_calc.svg?branch=master)](https://www.travis-ci.org/phpistai/loan_calc)
[![Drupal](https://img.shields.io/badge/Drupal-9-%2353B0EB "Supports Symfony 3.x")](https://drupal.org/9)
![PHP from Travis config](https://img.shields.io/travis/php-v/phpistai/loan_calc)
[![License: Unlicense](https://img.shields.io/badge/license-Unlicense-blue.svg)](http://unlicense.org/)
_
