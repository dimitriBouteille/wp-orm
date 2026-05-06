<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */
/**
 * Stub for static analysis only — wp-phpunit is loaded at runtime via the
 * test bootstrap, not through Composer's autoloader, so PHPStan can't see
 * the real class chain (WP_UnitTestCase → WP_UnitTestCase_Base →
 * PHPUnit_Adapter_TestCase → Polyfill_TestCase). This minimal declaration
 * is enough for PHPStan to resolve parent:: calls in test sub-classes.
 *
 * @see vendor/wp-phpunit/wp-phpunit/includes/abstract-testcase.php
 */
abstract class WP_UnitTestCase extends \PHPUnit\Framework\TestCase
{
}
