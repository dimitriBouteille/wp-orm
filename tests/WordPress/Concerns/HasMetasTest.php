<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Concerns;

use Dbout\WpOrm\Concerns\HasMetas;
use Dbout\WpOrm\Models\Meta\PostMeta;
use Dbout\WpOrm\Models\Post;

use Dbout\WpOrm\Tests\WordPress\TestCase;

class HasMetasTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetMeta(): void
    {

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
     * @covers ::hasMeta
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
     */
    public function testGetMetaValueWithoutCast(): void
    {
        $model = new Post();
        $model->setPostTitle('Hello world');

        $model->save();
        $model->setMeta('build-by', 'John D.');

        $this->assertEquals('John D.', $model->getMetaValue('build-by'));
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

    /**
     * @return void
     */
    public function testMeta(): void
    {

    }
}
