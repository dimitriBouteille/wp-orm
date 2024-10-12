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
     */
    public function setUp(): void
    {
        self::$model::truncate();
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
     * @covers AbstractModel::query
     */
    public function testWhereWithComplexJsonColumn(): void
    {
        $seArtists = [
            [
                'name' => 'Avicii',
                'url' => 'avicii',
                'metadata' => ['birthday-date' => '08-09-1989', 'address' => ['city' => 'Stockholm', 'country' => 'SE']],
            ],
            [
                'name' => 'Basshunter',
                'url' => 'basshunter',
                'metadata' => ['birthday-date' => '22-12-1984', 'address' => ['city' => 'Halmstad', 'country' => 'SE']],
            ],
            [
                'name' => 'Måns Zelmerlöw',
                'url' => 'mans-zelmerlow',
                'metadata' => ['birthday-date' => '11-06-1986', 'address' => ['city' => 'Lund', 'country' => 'SE']],
            ],
        ];

        $seIds = [];
        foreach ($seArtists as $artist) {
            $model = new self::$model($artist);
            $model->save();
            $seIds[] = $model->getId();
        }

        $frArtists = [
            [
                'name' => 'Madeon',
                'url' => 'madeon',
                'metadata' => ['birthday-date' => '30-05-1994', 'address' => ['city' => 'Nantes', 'country' => 'FR']],
            ],
            [
                'name' => 'Kavinsky',
                'url' => 'kavinsky',
                'metadata' => ['birthday-date' => '31-07-1975', 'address' => ['city' => 'Seine-Saint-Denis', 'country' => 'FR']],
            ],
        ];

        foreach ($frArtists as $artist) {
            $model = new self::$model($artist);
            $model->save();
        }

        $selectedIds = self::$model::query()->where('metadata->address.country', 'SE')->get()->pluck('id')->toArray();
        $this->assertLastQueryEquals("select * from `#TABLE_PREFIX#custom_table` where json_unquote(json_extract(`metadata`, '$.address.country')) = 'SE'");
        $this->assertEquals($seIds, $selectedIds);
    }

    /**
     * @return void
     * @covers AbstractModel::query
     */
    public function testWhereWithSimpleJsonColum(): void
    {
        $edmArtists = [
            [
                'name' => 'Martin Garrix',
                'url' => 'martin-garrix',
                'metadata' => ['type' => 'edm'],
            ],
            [
                'name' => 'Marshmello',
                'url' => 'marshmello',
                'metadata' => ['type' => 'edm'],
            ],
            [
                'name' => 'Calvin Harris',
                'url' => 'calvin-harris',
                'metadata' => ['type' => 'edm'],
            ],
        ];

        $edmIds = [];
        foreach ($edmArtists as $artist) {
            $model = new self::$model($artist);
            $model->save();
            $edmIds[] = $model->getId();
        }

        $popArtists = [
            [
                'name' => 'Coldplay',
                'url' => 'coldplay',
                'metadata' => ['type' => 'pop'],
            ],
        ];

        foreach ($popArtists as $artist) {
            $model = new self::$model($artist);
            $model->save();
        }

        $selectedIds = self::$model::query()->where('metadata->type', 'edm')->get()->pluck('id')->toArray();
        $this->assertLastQueryEquals("select * from `#TABLE_PREFIX#custom_table` where json_unquote(json_extract(`metadata`, '$.type')) = 'edm'");
        $this->assertEquals($edmIds, $selectedIds);
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

    /**
     * @return void
     * @covers AbstractModel::truncate
     */
    public function testTruncate(): void
    {
        $data = [
            [
                'name' => 'Martin Garrix',
                'url' => 'martin-garrix',
                'metadata' => ['type' => 'edm'],
            ],
            [
                'name' => 'Marshmello',
                'url' => 'marshmello',
                'metadata' => ['type' => 'edm'],
            ],
            [
                'name' => 'Calvin Harris',
                'url' => 'calvin-harris',
                'metadata' => ['type' => 'edm'],
            ],
        ];

        foreach ($data as $item) {
            /** @var AbstractModel $e */
            $e = new self::$model($item);
            $e->save();
        }

        $this->assertEquals(3, self::$model::all()->count());

        self::$model::truncate();
        $this->assertEquals(0, self::$model::all()->count());
    }
}
