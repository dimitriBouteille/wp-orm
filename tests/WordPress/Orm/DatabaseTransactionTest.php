<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Orm;

use Dbout\WpOrm\Orm\AbstractModel;
use Dbout\WpOrm\Orm\Database;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class DatabaseTransactionTest extends TestCase
{
    private string $tableName = '';
    private AbstractModel $model;
    private Database $db;

    /**
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        global $wpdb;

        $tableName = $wpdb->prefix . 'document';
        $sql = "CREATE TABLE $tableName (
            id INT NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            url varchar(55) DEFAULT '' NOT NULL,
            PRIMARY KEY  (id)
        );";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        define('SAVEQUERIES', true);
    }

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->model = new class () extends AbstractModel {
            protected $primaryKey = 'id';
            public $timestamps = false;
            protected $table = 'document';
        };

        global $wpdb;
        $this->tableName = $wpdb->prefix . 'document';
        $this->model::truncate();
        $this->db = new Database();
    }

    /**
     * @throws \Throwable
     * @return void
     * @covers Database::transaction
     */
    public function testTransactionSuccess(): void
    {
        $this->db->transaction(function () {
            $query = sprintf('INSERT INTO %s (name, URL) VALUES(? ?);', $this->tableName);
            $this->db->insert($query, ['Invoice #15', 'invoice-15']);
            $this->db->insert($query, ['Invoice #16', 'invoice-16']);
        });

        global $wpdb;
        var_dump($wpdb->queries);
    }
}
