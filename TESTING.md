# WordPress Tests - Local Setup Guide

This guide explains how to run WordPress tests locally using Docker Compose.

## Prerequisites

- Docker and Docker Compose installed on your machine
- PHP 8.3+ installed locally
- Composer installed

## Initial Setup

### 1. Install dependencies

```bash
composer install
```

### 2. Configure environment (Optional)

Copy the `.env.example` file to `.env` if you want to customize the configuration:

```bash
cp .env.example .env
```

You can modify the following values in the `.env` file:

```bash
# MySQL root password (default: root)
MYSQL_ROOT_PASSWORD=root

# Test database name (default: wordpress_test)
MYSQL_DATABASE=wordpress_test
```

## Running Tests

### Quick method with automated script

```bash
# Run tests without coverage
./run-wp-tests.sh

# Run tests with coverage
./run-wp-tests.sh --coverage
```

The `run-wp-tests.sh` script automatically:
1. Starts the MySQL container
2. Creates/resets the test database
3. Installs PHPUnit 9 (required for WordPress)
4. Runs the tests

## Reinstalling the Test Environment

If you encounter issues, you can completely reinstall the environment:

```bash
# 1. Clean Docker
docker compose down -v

# 2. Reinstall dependencies
composer install

# 3. Rerun tests
./run-wp-tests.sh
```

## Unit Tests (without WordPress)

To run only unit tests (which don't require WordPress):

```bash
composer run test:unit
```

## Test Architecture

- **Unit tests**: `tests/Unit/` - Isolated tests without WordPress dependencies
- **WordPress tests**: `tests/WordPress/` - Integration tests with WordPress
- **PHPUnit configuration**:
  - `phpunit.xml` : Unit tests (PHPUnit 12)
  - `phpunit-wp.xml` : WordPress tests (PHPUnit 9)

## How It Works

The WordPress test environment is managed entirely through Composer:

- **`roots/wordpress`**: Installs WordPress core in `web/wordpress/`
- **`wp-phpunit/wp-phpunit`**: Provides the WordPress test suite (WP_UnitTestCase, factories, etc.)
- **`tests/WordPress/wp-tests-config.php`**: Database configuration, reads from environment variables

Each test runs inside a database transaction that is rolled back after the test completes, ensuring full isolation between tests.

## Important Notes

- WordPress tests require **PHPUnit 9** only (WordPress limitation)
- The MySQL container uses port **3307** to avoid conflicts with local MySQL installations
- The `run-wp-tests.sh` script automatically installs PHPUnit 9
- The database is dropped and recreated on each test run to ensure a clean state
