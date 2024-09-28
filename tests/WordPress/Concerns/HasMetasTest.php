<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Concerns;

use Dbout\WpOrm\Concerns\HasMetas;
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
        $model->setPostTitle('Hello world');
        $model->save();
        $createMeta = $model->setMeta('author', 'Norman FOSTER');

        $meta = $model->getMeta('author');
        $this->assertInstanceOf(AbstractMeta::class, $meta);
        $this->assertEquals($createMeta->getId(), $meta->getId());
        $this->assertEquals($createMeta->getValue(), $meta->getValue());
        $this->assertEquals('author', $meta->getValue());
    }

    /**
     * @return void
     * @covers HasMetas::setMeta
     */
    public function testSetMeta(): void
    {
        $model = new Post();
        $model->setPostTitle('Hello world');
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
        $model->setPostTitle('Hello world');
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
        $model->setPostTitle('Hello world');

        $model->save();
        $model->setMeta('build-by', 'John D.');

        add_post_meta($model->getId(), 'place', 'Lyon, France');
        $this->assertEquals('Lyon, France', $model->getMetaValue('place'));
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
            ];
        };

        $model = new $object();
        $model->setPostTitle('Hello world');
        $model->save();
        $model->setMeta('age', '18');
        $model->setMeta('year', '2024');
        $model->setMeta('is_active', '1');
        $model->setMeta('subscribed', '0');

        $this->assertEquals(18, $model->getMetaValue('age'));
        $this->assertEquals(2024, $model->getMetaValue('year'));
        $this->assertTrue($model->getMetaValue('is_active'));
        $this->assertFalse($model->getMetaValue('subscribed'));
    }

    /**
     * @return void
     * @covers HasMetas::deleteMeta
     */
    public function testDeleteMeta(): void
    {
        $model = new Post();
        $model->setPostTitle('Hello world');
        $model->save();

        $model->setMeta('architect-name', 'Norman F.');

        $this->assertEquals(1, $model->deleteMeta('architect-name'), 'The function must delete only one line.');
        $this->assertFalse($model->hasMeta('architect-name'), 'The meta must no longer exist.');
    }
}
