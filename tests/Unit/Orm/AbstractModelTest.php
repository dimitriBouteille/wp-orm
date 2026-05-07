<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\Unit\Orm;

use Dbout\WpOrm\Orm\AbstractModel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractModel::class)]
#[CoversMethod(AbstractModel::class, '__call')]
class AbstractModelTest extends TestCase
{
    /**
     * Build a bare AbstractModel subclass without explicit accessors so the
     * raw __call regex behavior is observable. Concrete models like Comment
     * mask the bug by adding explicit *Attribute() accessors.
     *
     * @return AbstractModel
     */
    private function bareModel(): AbstractModel
    {
        return new class () extends AbstractModel {
            protected $table = 'fake';
            public $timestamps = false;
        };
    }

    /**
     * Pin: the snake_case conversion done in __call() splits acronyms
     * letter-by-letter ("IP" → "_i_p", not "_ip").
     *
     * The regex `(?<!^)[A-Z]` inserts an underscore before EVERY uppercase
     * letter, including the second letter of an acronym. The expected
     * snake_case for getCommentAuthorIP would be `comment_author_ip`, but
     * __call actually queries `comment_author_i_p`.
     *
     * If __call() is ever rewritten with a proper snake_case converter
     * (e.g. Str::snake), this test will fail and signal that:
     *   1. the bug is fixed
     *   2. concrete models like Comment can drop their compensating accessors.
     *
     * @param string $methodName  Magic getter call.
     * @param string $buggyKey    The (incorrect) snake_case key __call queries.
     * @param string $correctKey  The snake_case key Str::snake() would produce.
     * @return void
     */
    #[Group('regression-pin')]
    #[TestWith(['getCommentAuthorIP',  'comment_author_i_p',  'comment_author_ip'])]
    #[TestWith(['getCommentPostID',    'comment_post_i_d',    'comment_post_id'])]
    public function testCallSplitsAcronymsLetterByLetter(
        string $methodName,
        string $buggyKey,
        string $correctKey
    ): void {
        $model = $this->bareModel();

        // Seed the buggy key — that is what __call currently looks up.
        $model->setAttribute($buggyKey, 'value-on-buggy-key');
        // Seed the correct key — proves __call does NOT use it today.
        $model->setAttribute($correctKey, 'value-on-correct-key');

        $this->assertSame(
            'value-on-buggy-key',
            $model->__call($methodName, []),
            sprintf(
                'Pin: __call queries `%s` instead of `%s`. If this fails, the snake_case '
                . 'converter has been fixed — drop the compensating accessors in Comment.',
                $buggyKey,
                $correctKey
            )
        );
    }

    /**
     * Pin: regular CamelCase (without acronyms) snake_cases correctly.
     *
     * Documents the cases where __call DOES work, so a future refactor can
     * confirm it preserves these.
     *
     * @return void
     */
    #[Group('regression-pin')]
    public function testCallHandlesRegularCamelCaseCorrectly(): void
    {
        $model = $this->bareModel();
        $model->setAttribute('post_title', 'Hello world');

        $this->assertSame('Hello world', $model->__call('getPostTitle', []));
    }

    /**
     * Pin: setter form goes through the same snake_case conversion.
     *
     * @return void
     */
    #[Group('regression-pin')]
    public function testSetCallAlsoSplitsAcronymsLetterByLetter(): void
    {
        $model = $this->bareModel();
        $model->__call('setCommentAuthorIP', ['127.0.0.1']);

        $this->assertSame('127.0.0.1', $model->getAttribute('comment_author_i_p'));
        $this->assertNull($model->getAttribute('comment_author_ip'));
    }
}
