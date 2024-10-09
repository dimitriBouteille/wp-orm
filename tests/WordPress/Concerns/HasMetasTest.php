<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Concerns;

use Carbon\Carbon;
use Dbout\WpOrm\Concerns\HasMetas;
use Dbout\WpOrm\Enums\YesNo;
use Dbout\WpOrm\Models\Meta\AbstractMeta;
use Dbout\WpOrm\Models\Meta\PostMeta;
use Dbout\WpOrm\Models\Post;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class HasMetasTest extends TestCase
{
    /**
     * @return void
     * @covers HasMetas::getMeta
     */
    public function testGetMeta(): void
    {
        $model = new Post();
        $model->setPostTitle(__FUNCTION__);
        $model->save();
        $createMeta = $model->setMeta('author', 'Norman FOSTER');

        $meta = $model->getMeta('author');
        $this->assertLastQueryEquals($this->getQueryGetMeta($model->getId(), 'author'));
        $this->assertInstanceOf(AbstractMeta::class, $meta);
        $this->assertEquals($createMeta->getId(), $meta->getId());
        $this->assertEquals($createMeta->getValue(), $meta->getValue());
        $this->assertEquals('Norman FOSTER', $meta->getValue());
        $this->assertEquals('author', $meta->getKey());
    }

    /**
     * @return void
     * @covers HasMetas::setMeta
     */
    public function testSetMeta(): void
    {
        $model = new Post();
        $model->setPostTitle(__FUNCTION__);
        $model->save();
        $meta = $model->setMeta('build-by', 'John D.');

        $this->assertEquals('John D.', get_post_meta($model->getId(), 'build-by', true));
        $this->assertInstanceOf(PostMeta::class, $meta);

        $loadedMeta = $model->getMeta('build-by');
        $this->assertEquals($meta->getId(), $loadedMeta->getId());
    }

    /**
     * @return void
     * @covers HasMetas::hasMeta
     */
    public function testHasMeta(): void
    {
        $model = new Post();
        $model->setPostTitle(__FUNCTION__);
        $model->save();

        $model->setMeta('birthday-date', '17/09/1900');
        $this->assertTrue($model->hasMeta('birthday-date'));

        $wpMetaId = add_post_meta($model->getId(), 'birthday-place', 'France');
        $this->assertTrue($model->hasMeta('birthday-place'));
        $this->assertEquals('France', $model->getMetaValue('birthday-place'));
        $this->assertEquals($wpMetaId, $model->getMeta('birthday-place')?->getId());
    }

    /**
     * @return void
     * @covers HasMetas::getMetaValue
     */
    public function testGetMetaValueWithoutCast(): void
    {
        $model = new Post();
        $model->setPostTitle(__FUNCTION__);

        $model->save();
        $model->setMeta('build-by', 'John D.');

        add_post_meta($model->getId(), 'place', 'Lyon, France');
        $this->assertEquals('Lyon, France', $model->getMetaValue('place'));
        $this->assertLastQueryEquals($this->getQueryGetMeta($model->getId(), 'place'));
    }

    /**
     * @return void
     * @covers HasMetas::getMetaValue
     */
    public function testGetMetaValueWithGenericCasts(): void
    {
        $object = new class () extends Post {
            protected array $metaCasts = [
                'age' => 'int',
                'year' => 'integer',
                'is_active' => 'bool',
                'subscribed' => 'boolean',
                'data'  => 'json',
            ];
        };

        $model = new $object();
        $model->setPostTitle(__FUNCTION__);
        $model->save();
        $model->setMeta('age', '18');
        $model->setMeta('year', '2024');
        $model->setMeta('is_active', '1');
        $model->setMeta('subscribed', '0');
        $model->setMeta('data', '{"firstname":"John","lastname":"Doe"}');

        $this->assertEquals(18, $model->getMetaValue('age'));
        $this->assertEquals(2024, $model->getMetaValue('year'));
        $this->assertTrue($model->getMetaValue('is_active'));
        $this->assertFalse($model->getMetaValue('subscribed'));
        $this->assertEquals(['firstname' => 'John', 'lastname' => 'Doe'], $model->getMetaValue('data'));
    }

    /**
     * @return void
     * @covers HasMetas::getMetaValue
     */
    public function testGetMetaValueWithEnumCasts(): void
    {
        $object = new class () extends Post {
            protected array $metaCasts = [
                'active' => YesNo::class,
            ];
        };

        $model = new $object();
        $model->setPostTitle(__FUNCTION__);
        $model->save();
        $model->setMeta('active', 'yes');

        /** @var YesNo $value */
        $value = $model->getMetaValue('active');

        $this->assertInstanceOf(YesNo::class, $value);
        $this->assertEquals('yes', $value->value);
    }

    /**
     * @return void
     * @covers HasMetas::getMetaValue
     */
    public function testGetMetaValueWithDatetimeCasts(): void
    {
        $object = new class () extends Post {
            protected array $metaCasts = [
                'created_at' => 'datetime',
                'uploaded_at' => 'date',
            ];
        };

        $model = new $object();
        $model->setPostTitle(__FUNCTION__);
        $model->save();
        $model->setMeta('created_at', '2022-09-08 07:30:05');
        $model->setMeta('uploaded_at', '2024-10-08 10:25:35');

        /** @var Carbon $date */
        $date = $model->getMetaValue('created_at');
        $this->assertInstanceOf(Carbon::class, $date);
        $this->assertEquals('2022-09-08 07:30:05', $date->format('Y-m-d H:i:s'));

        /** @var Carbon $date */
        $date = $model->getMetaValue('uploaded_at');
        $this->assertInstanceOf(Carbon::class, $date);
        $this->assertEquals('2024-10-08 00:00:00', $date->format('Y-m-d H:i:s'), 'The time must be reset to 00:00:00.');
    }

    /**
     * @return void
     * @covers HasMetas::getMetaValue
     */
    public function testGetMetaValueWithInvalidCasts(): void
    {
        $object = new class () extends Post {
            protected array $metaCasts = [
                'my_meta' => 'boolean_',
            ];
        };

        $model = new $object();
        $model->setPostTitle(__FUNCTION__);
        $model->save();
        $model->setMeta('my_meta', 'yes');

        $this->assertEquals('yes', $model->getMetaValue('my_meta'));
    }

    /**
     * @return void
     * @covers HasMetas::deleteMeta
     */
    public function testDeleteMeta(): void
    {
        $model = new Post();
        $model->setPostTitle(__FUNCTION__);
        $model->save();

        $model->setMeta('architect-name', 'Norman F.');

        $this->assertEquals(1, $model->deleteMeta('architect-name'), 'The function must delete only one line.');
        $this->assertLastQueryEquals(sprintf(
            "delete from `%1\$s` where `%1\$s`.`post_id` = %2\$d and `%1\$s`.`post_id` is not null and `meta_key` = 'architect-name'",
            '#TABLE_PREFIX#postmeta',
            $model->getId()
        ));

        $this->assertFalse($model->hasMeta('architect-name'), 'The meta must no longer exist.');
    }

    /**
     * @return void
     * @covers HasMetas::deleteMeta
     */
    public function testDeleteUndefinedMeta(): void
    {
        $model = new Post();
        $model->setPostTitle(__FUNCTION__);
        $model->save();

        $model->setMeta('architect-name', 'Norman F.');

        $this->assertEquals(0, $model->deleteMeta('fake-meta'));

        $this->assertLastQueryEquals(sprintf(
            "delete from `%1\$s` where `%1\$s`.`post_id` = %2\$d and `%1\$s`.`post_id` is not null and `meta_key` = 'fake-meta'",
            '#TABLE_PREFIX#postmeta',
            $model->getId()
        ));
    }

    /**
     * @param int $postId
     * @param string $metaKey
     * @return string
     */
    private function getQueryGetMeta(int $postId, string $metaKey): string
    {
        return sprintf(
            "select * from `%1\$s` where `%1\$s`.`post_id` = %2\$d and `%1\$s`.`post_id` is not null and `meta_key` = '%3\$s' limit 1",
            '#TABLE_PREFIX#postmeta',
            $postId,
            $metaKey
        );
    }
}
