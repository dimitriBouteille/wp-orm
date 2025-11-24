# WordPress Tests - Local Setup Guide

This guide explains how to run WordPress tests locally using Docker Compose.

## Prerequisites

- Docker and Docker Compose installed on your machine
- PHP 8.2+ installed locally
- Composer installed
- Subversion (svn) to download WordPress test files:
  ```bash
  sudo apt-get install -y subversion  # Ubuntu/Debian
  sudo yum install -y subversion      # CentOS/RHEL
  brew install svn                    # macOS
  ```

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

# WordPress version to test against (default: latest)
# Options: 6.3, 6.4, 6.5, 6.6, 6.7, 6.8, latest
WP_VERSION=latest
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
4. Installs WordPress test environment (if not already present)
5. Runs the tests

**Note:** WordPress and test files are installed in `var/testings/` and will be reused on subsequent runs for faster execution.

## Testing Different WordPress Versions

To test with a specific WordPress version:

```bash
# Using .env file
echo "WP_VERSION=6.7" > .env
./run-wp-tests.sh

# Or directly via command line
WP_VERSION=6.7 ./run-wp-tests.sh
```

## Reinstalling the Test Environment

If you encounter issues, you can completely reinstall the environment:

```bash
# 1. Clean Docker
docker compose down -v

# 2. Remove WordPress files
rm -rf var/testings

# 3. Rerun tests
./run-wp-tests.sh
```

### Tests fail after changing WordPress version

```bash
# Reinstall the test environment
rm -rf var/testings
./run-wp-tests.sh
```

### SVN not found error

Make sure Subversion is installed:

```bash
# Ubuntu/Debian
sudo apt-get install -y subversion

# Check installation
svn --version
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
  - `phpunit.xml` : Unit tests (PHPUnit 11)
  - `phpunit-wp.xml` : WordPress tests (PHPUnit 9)

## Important Notes

- WordPress tests require **PHPUnit 9** only (WordPress limitation)
- The MySQL container uses port **3307** to avoid conflicts with local MySQL installations
- WordPress files are stored in `var/testings/wordpress` (local to project)
- Test suite files are stored in `var/testings/tests-wp` (local to project)
- The `run-wp-tests.sh` script automatically installs PHPUnit 9
- The database is dropped and recreated on each test run to ensure a clean state
- WordPress installation is cached and reused across test runs for better performance

## Performance Tips

- First run will be slower (downloads WordPress and test files)
- Subsequent runs are faster (WordPress files are cached in `var/testings/`)
- To force a clean WordPress installation: `rm -rf var/testings`
- The test database is always reset to ensure consistent test results