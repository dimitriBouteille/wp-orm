<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Orm;

use Dbout\WpOrm\Orm\AbstractModel;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class AbstractModelWithCustomTableTest extends TestCase
{
    private const TABLE_NAME = 'custom_table';
    private static AbstractModel $model;

    /**
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        global $wpdb;

        $tableName = $wpdb->prefix . self::TABLE_NAME;
        $sql = "CREATE TABLE $tableName (
            id INT NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            url varchar(55) DEFAULT '' NOT NULL,
            metadata JSON,
            PRIMARY KEY  (id)
        );";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        self::$model = new class () extends AbstractModel {
            protected $primaryKey = 'id';
            public $timestamps = false;

            protected $table = 'custom_table';

            protected $casts = [
                'metadata' => 'json',
            ];
        };
    }

    /**
     * @return void
     * @covers AbstractModel::save
     */
    public function testSave(): void
    {
        $metaData = ['birthday-date' => '01-06-1935', 'address' => ['city' => 'London', 'zipcode' => 'London']];

        /** @var AbstractModel $object */
        $object = new self::$model();
        $object->setAttribute('name', 'Norman FOSTER');
        $object->setAttribute('url', 'norman-forster');
        $object->setAttribute('metadata', $metaData);
        $this->assertTrue($object->save());

        $this->assertEquals('Norman FOSTER', $object->getAttribute('name'));
        $this->assertEquals('norman-forster', $object->getAttribute('url'));
        $this->assertEquals($metaData, $object->getAttribute('metadata'));
    }

    /**
     * @return void
     * @covers AbstractModel::find
     */
    public function testFind(): void
    {
        /** @var AbstractModel $object */
        $object = new self::$model([
            'name' => 'Zaha Hadid',
            'url' => 'zaha-hadid',
            'metadata' => ['birthday-date' => '31-10-1950', 'address' => ['city' => 'Bagdad']],
        ]);

        $this->assertTrue($object->save());

        $newObject = self::$model::find($object->getId());
        $this->assertInstanceOf(AbstractModel::class, $newObject);
    }

    /**
     * @return void
     * @covers AbstractModel::all
     */
    public function testAll(): void
    {

    }

    /**
     * @return void
     * @covers AbstractModel::query
     */
    public function testWhereWithJsonColumn(): void
    {

    }

    /**
     * @return void
     * @covers AbstractModel::delete
     */
    public function testDelete(): void
    {
        /** @var AbstractModel $object */
        $object = new self::$model([
            'name' => 'Frank Gehry',
            'url' => 'frank-gehry',
            'metadata' => ['birthday-date' => '28-02-1929', 'address' => ['city' => 'Toronto']],
        ]);

        $this->assertTrue($object->save());
        $id = $object->getId();
        $this->assertTrue($object->delete());

        $newObject = self::$model::find($id);
        $this->assertNull($newObject);
    }
}
