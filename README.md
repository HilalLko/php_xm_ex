## API documentation available at
```bash
http://127.0.0.1:4545/api/documentation
```

## Setup and Installation

### 1. Clone the Repository

Start by cloning the repository to your local machine:

```bash
git clone https://github.com/HilalLko/php_xm_ex.git 
cd php_xm_ex
```

### 2. Install PHP Dependencies

Before we bring the Docker containers up, install the PHP dependencies:

```bash
composer install
```

### Env Setup
Make sure to add `RAPIDAPI_KEY` from your Rapid api account without this token app may not work.

### Bonus #Docker. Run with Docker using Laravel Sail
Laravel Sail is a light-weight command-line interface for interacting with Laravel's default Docker environment. The recommended way to run the project is using Sail.
Please review the `docker-compose.yml` to check the ports of the application and change them according to your
requirements.

## Current Ports

- Application:`http://127.0.0.1:4545/`
- MailPit server: `http://localhost:8025`
- Redis: `http://localhost:6389`
- MySQL: `3310`

To start the Docker containers for the project, run:

```bash
./vendor/bin/sail up
```

The first time you run the Sail `up` command, Sail's application containers will be built on your machine. This could take several minutes.

Once the containers are started, you can access the project in your web browser at: http://localhost:4545.

### Other Useful Sail Commands
To stop the containers, you can simply press `Ctrl + C` or run:

```bash
./vendor/bin/sail artisan migrate
./vendor/bin/sail down
```

### This application is using Laravel Queue to Fetch Company Details from finance API and then send required data to provided Email
To make queu work, please change `QUEUE_CONNECTION` from `database` to `redis`
# With docker run below command to make queue work
```bash
./vendor/bin/sail artisan queue:work
```

## Email Testing with Mailpit

This project leverages Mailpit as its email service for testing. Mailpit offers a straightforward approach to intercept and display emails for development purposes, ensuring that no emails are unintentionally sent to real users.

### Accessing Mailpit

Once the project is up and running, you can access the Mailpit web interface to view any emails sent by the application:

```plaintext
http://localhost:8025/
```
By using Mailpit, we can easily inspect the emails, verify their content, and ensure that our application's email features are functioning as expected without any side effects.

## Testing

This project uses [PHPUnit](https://phpunit.de) for testing.

### Running Tests

To run the suite of tests for the application, execute the following command:

```bash
./vendor/bin/phpunit
```
This command will run all tests and display a summary of passed and failed tests, if any.
