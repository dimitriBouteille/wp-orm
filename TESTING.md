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

## Writing assertions

Tests should assert on **observable behavior** (returned models, attribute values, row counts), not on the SQL string Eloquent emits. Tying tests to a specific generated SQL string makes them fragile across Eloquent grammar changes without catching real regressions.

`TestCase` exposes a single SQL-introspection helper, `assertLastQueryContains(string $needle)`, intended for the rare cases where the SQL shape is itself part of the contract:

- **Custom grammar overrides** — e.g. the `WordPressGrammar::wrapJsonSelector` idiom (`json_unquote(json_extract(...))`) must be pinned because it *is* what the class promises to produce.
- **Security regression tests** — pinning that a value reaches the SQL via a binding rather than as a literal.

For everything else, prefer fixture-based assertions:

```php
// ❌ Couples the test to grammar formatting
$this->assertLastQueryContains("where `post_type` = 'product'");

// ✅ Asserts the actual filtering contract
$productId = self::factory()->post->create(['post_type' => 'product']);
self::factory()->post->create(['post_type' => 'page']);

$results = Post::query()->tap(new IsPostTypeTap('product'))->get();
$this->assertCount(1, $results);
$this->assertEquals($productId, $results->first()->getId());
```

## Test groups

A handful of cross-cutting `@group` annotations are in place so subsets of
the suite can be targeted in isolation:

- `@group security` — regression tests pinning hardening (SQL injection
  rejection in `joinToMeta` / `addMetaTo*`, bound parameter usage).
- `@group multisite` — tests that require the suite to be booted with
  `WP_MULTISITE=1`. Also auto-skipped when not in multisite mode via the
  `RunsInMultisite` trait.

Run a single group locally:

```bash
vendor/bin/phpunit -c phpunit-wp.xml --group security
```

## Multisite

Multisite is currently **not supported** at the ORM level (see README and
the v6 milestone). The test suite still has scaffolding so that
multisite-only tests can be written and so the package is exercised
against a multisite WordPress install in CI.

To run the suite in multisite mode locally:

```bash
WP_MULTISITE=1 ./run-wp-tests.sh
```

A test class that should run only in multisite mode adds the
`RunsInMultisite` trait — single-site runs auto-skip:

```php
use Dbout\WpOrm\Tests\WordPress\Support\RunsInMultisite;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class MyMultisiteTest extends TestCase
{
    use RunsInMultisite;

    public function testInsideASubsite(): void
    {
        $value = $this->inBlog($subsiteId, fn () => get_option('blogname'));
        $this->assertSame('subsite', $value);
    }
}
```

The dedicated `wp-test-multisite` CI job runs the full WP test suite on
WordPress latest with `WP_MULTISITE=1`.

## Important Notes

- WordPress tests require **PHPUnit 9** only (WordPress limitation)
- The MySQL container uses port **3307** to avoid conflicts with local MySQL installations
- The `run-wp-tests.sh` script automatically installs PHPUnit 9
- The database is dropped and recreated on each test run to ensure a clean state
