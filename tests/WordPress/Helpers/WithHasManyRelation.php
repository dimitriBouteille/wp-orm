<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Helpers;

use Illuminate\Support\Collection;

trait WithHasManyRelation
{
    /**
     * @param Collection|null $resultCollection
     * @param string $relationProperty
     * @param callable $expectedIdsCallback
     * @return void
     */
    protected function checkHasManyRelationResult(
        ?Collection $resultCollection,
        string $relationProperty,
        callable $expectedIdsCallback
    ): void {
        $ids = $resultCollection->pluck($relationProperty);
        $expectedIds = $expectedIdsCallback();

        $this->assertCount(count($expectedIds), $resultCollection->toArray());
        $this->assertEqualsCanonicalizing($expectedIds, $ids->toArray());
    }
}
