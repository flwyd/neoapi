# Neoapi - The Next Generation Ranger Clubhouse Backend
(An experiment in Laravel)

## Prerequisites

You will need the following things properly installed on your computer.
(See the neohouse repository README file for instructions on how to bring up the frontend)

* [Git](https://git-scm.com/)
* [PHP >= 7.1.3](https://php.net)

## Installation

* `git clone <repository-url>` this repository
* `cd neoapi`
* `composer install`
* Copy .env.clubhouse to .env and set the configuration appropriately
* `php artisan migrate` (needed to have database auditing)

## Running / Development

* `php -S localhost:8000 -t public`
* See the neohouse README for instructions on how to start the frontend
