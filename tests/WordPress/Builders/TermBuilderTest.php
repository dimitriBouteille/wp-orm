<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Builders;

use Dbout\WpOrm\Builders\TermBuilder;
use Dbout\WpOrm\Models\Term;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class TermBuilderTest extends TestCase
{
    /**
     * @return void
     * @covers TermBuilder::findAllByTaxonomy
     */
    public function testFindAllByTaxonomyReturnsOnlyMatchingTerms(): void
    {
        $categoryTermId = self::factory()->term->create([
            'taxonomy' => 'category',
            'name'     => 'My category',
        ]);

        self::factory()->term->create([
            'taxonomy' => 'post_tag',
            'name'     => 'My tag',
        ]);

        $terms = Term::query()->findAllByTaxonomy('category');

        $ids = $terms->pluck(Term::TERM_ID)->toArray();

        $this->assertContains($categoryTermId, $ids);
        $this->assertCount(1, array_intersect([$categoryTermId], $ids));

        /** @var Term $first */
        $first = $terms->firstWhere(Term::TERM_ID, $categoryTermId);
        $this->assertNotNull($first);
        $this->assertEquals('My category', $first->getName());
    }

    /**
     * @return void
     * @covers TermBuilder::findAllByTaxonomy
     */
    public function testFindAllByTaxonomyReturnsEmptyWhenNoMatch(): void
    {
        self::factory()->term->create([
            'taxonomy' => 'category',
            'name'     => 'My category',
        ]);

        $terms = Term::query()->findAllByTaxonomy('unknown_taxonomy');

        $this->assertCount(0, $terms->toArray());
    }

    /**
     * @return void
     * @covers TermBuilder::findAllByTaxonomy
     */
    public function testFindAllByTaxonomyReturnsMultipleTerms(): void
    {
        $tagId1 = self::factory()->term->create([
            'taxonomy' => 'post_tag',
            'name'     => 'First tag',
        ]);

        $tagId2 = self::factory()->term->create([
            'taxonomy' => 'post_tag',
            'name'     => 'Second tag',
        ]);

        self::factory()->term->create([
            'taxonomy' => 'category',
            'name'     => 'A category',
        ]);

        $terms = Term::query()->findAllByTaxonomy('post_tag');

        $ids = $terms->pluck(Term::TERM_ID)->toArray();

        $this->assertContains($tagId1, $ids);
        $this->assertContains($tagId2, $ids);
        $this->assertCount(2, array_intersect([$tagId1, $tagId2], $ids));
    }
}
