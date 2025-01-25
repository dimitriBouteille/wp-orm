<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Concerns;

use Carbon\Carbon;
use Dbout\WpOrm\Tests\WordPress\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class PrunableTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        global $wpdb;

        $tableName = $wpdb->prefix . 'sales_payment';
        $sql = "CREATE TABLE $tableName (
            id INT NOT NULL AUTO_INCREMENT,
            method varchar(100) NOT NULL,
            created_at DATE NOT NULL,
            amount float(10,7) NOT NULL,
            PRIMARY KEY  (id)
        );";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /**
     * @return void
     * @covers Prunable::prunable()
     */
    public function testPrunable(): void
    {
        $model = new class () extends Model {
            use Prunable;

            protected $table = 'sales_payment';

            public function prunable()
            {
                return static::where('created_at', '<', Carbon::create(2025, 1, 1));
            }
        };

        $values = [
            [
                'method' => 'adyen',
                'amount' => 100,
                'created_at' => Carbon::create(2024, 05, 10),
            ],
            [
                'method' => 'paypal',
                'amount' => 50.52,
                'created_at' => Carbon::create(2023, 10, 8),
            ],
            [
                'method' => 'apple_pay',
                'amount' => 145.12,
                'created_at' => Carbon::create(2024, 5, 28),
            ],
            [
                'method' => 'amazon_pay',
                'amount' => 199.99,
                'created_at' => Carbon::create(2025, 4, 15),
            ],
            [
                'method' => 'swiss_pay',
                'amount' => 129.50,
                'created_at' => Carbon::create(2025, 3, 5),
            ],
            [
                'method' => 'adyen',
                'amount' => 15.99,
                'created_at' => Carbon::create(2024, 7, 19),
            ],
            [
                'method' => 'ideal',
                'amount' => 69.10,
                'created_at' => Carbon::create(2026, 4, 5),
            ],
        ];

        $model::insert($values);
        $result = (new $model())->pruneAll();

        $this->assertEquals(4, $result);
    }
}
