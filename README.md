# Loan Calculator module for Drupal (a.k.a. loan_calc)

[UAB Baltic Amadeus](https://www.drupal.org/baltic-amadeus) challenge for wannabe drupalists.
## Description (in Lithuanian)

* Sukurti skaičiuoklę pagal pateiktą [xls failą](resources/loan_amortization_schedule.xls);
* Pradinės reikšmės turi būti konfigūruojamos;
* Laukelių pavadinimai taip pat turi būti konfigūruojami/verčiami (EN/LT);
* Ateityje, pridėjus naują kalbą svetainėje, turi atsirasti ir visi nustatymų
laukai šiai kalbai;
* Pradiniai nustatymai turi atkeliauti kartu su nauju moduliu;
* Skaičiuoklė turi būti pasiekiama ir kaip puslapis, ir kaip blokas;
* Skaičiavimai turi būti atliekami serverio lygmenyje (po “Form submit”), ne JS;
* Vartotojo skaičiavimai turi būti loginami;
* Skaičiavimams turi būti panaudotas Drupal servisas;
* Įvesties laukeliai turi būti validuojami;
* Papildomai tūri būti sukurtas WEB servisas (REST), kuriam perdavus
“Enter Values” reikšmes, būtų gražinamos “Loan Summary” reikšmės;
* Drupal versija: 8.5;
* Dizainas: Drupal bartik tema arba bootstrap.

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
