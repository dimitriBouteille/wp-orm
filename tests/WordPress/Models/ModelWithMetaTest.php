<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Models;

use Dbout\WpOrm\Models\Article;
use Dbout\WpOrm\Models\Meta\PostMeta;
use Dbout\WpOrm\Models\Post;
use Dbout\WpOrm\Concerns\HasMeta;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class ModelWithMetaTest extends TestCase
{
    /**
     * @return void
     * @covers HasMeta::setMeta
     */
    public function _testSetMetaWithNewModel(): void
    {
        $model = new Article();
        $model->setPostName('hello-world');

        $meta = $model->setMeta('build-by', 'Dimitri B.');
        $model->save();

        var_dump('__testSetMetaWithNewModel', $model->getId(), get_post_meta($model->getId(), 'build-by', true));
        $this->assertEquals('Dimitri B.', get_post_meta($model->getId(), 'build-by', true));
        $this->assertEquals(null, $meta, 'The function must return null because the model does not yet exist.');
    }

    /**
     * @return void
     * @covers HasMeta::setMeta
     */
    public function testSetMetaWithExistingModel(): void
    {
        $model = new Post();
        $model->setPostTitle('Hello world');

        $model->save();
        $meta = $model->setMeta('build-by', 'John D.');

        var_dump('__testSetMetaWithExistingModel', $model->getId(), get_post_meta($model->getId(), 'build-by', true));

        $this->assertEquals('John D.', get_post_meta($model->getId(), 'build-by', true));
        $this->assertInstanceOf(PostMeta::class, $meta);

        $loadedMeta = $model->getMeta('build-by');
        $this->assertEquals($meta->getId(), $loadedMeta->getId());
    }

    /**
     * @return void
     * @covers HasMeta::hasMeta
     */
    public function testHasMeta(): void
    {
        $model = new Post();
        $model->setPostTitle('Hello world');
        $model->save();

        $model->setMeta('birthday-date', '17/09/1900');
        $this->assertTrue($model->hasMeta('birthday-date'));
    }
}
