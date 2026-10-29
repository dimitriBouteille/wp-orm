<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Orm\Schemas;

use Dbout\WpOrm\Orm\Database;
use Dbout\WpOrm\Tests\WordPress\Support\CreatesCustomTable;
use Dbout\WpOrm\Tests\WordPress\TestCase;
use Illuminate\Database\Schema\Blueprint;

/**
 * Tables created with the schema builder must use the charset / collation configured
 * on $wpdb, otherwise comparing their string columns with core tables fails.
 */
class SchemaCollationTest extends TestCase
{
    use CreatesCustomTable;

    private const TABLE_NAME = 'collation_check';

    /**
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::createCustomTable(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->string('slug');
        });
    }

    /**
     * @return void
     * @covers Database::getConfig
     * @covers \Illuminate\Database\Schema\Grammars\MySqlGrammar::compileCreate
     */
    public function testCreatedTableUsesWordPressCollation(): void
    {
        global $wpdb;
        if ($wpdb->collate === '') {
            $this->markTestSkipped('$wpdb->collate is empty, the server default collation is used.');
        }

        $collation = $wpdb->get_var($wpdb->prepare(
            'SELECT TABLE_COLLATION FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = %s',
            self::getTable(self::TABLE_NAME)
        ));

        $this->assertSame($wpdb->collate, $collation);
    }

    /**
     * @return void
     * @covers Database::getConfig
     * @covers \Illuminate\Database\Schema\Grammars\MySqlGrammar::compileCreate
     */
    public function testStringColumnCanBeComparedWithCoreTableColumn(): void
    {
        $postId = self::factory()->post->create(['post_name' => 'collation-check']);
        Database::getInstance()->table(self::TABLE_NAME)->insert(['slug' => 'collation-check']);

        $ids = Database::getInstance()
            ->table(self::TABLE_NAME)
            ->join('posts', 'posts.post_name', '=', self::TABLE_NAME . '.slug')
            ->pluck('posts.ID')
            ->all();

        $this->assertEquals([$postId], $ids);
    }
}
