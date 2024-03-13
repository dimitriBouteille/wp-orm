<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Helpers;

trait WithHasManyRelation
{
    /**
     * @param callable $resultCollectionCallback
     * @param string $relationProperty
     * @param callable $expectedIdsCallback
     * @return void
     */
    protected function checkHasManyRelationResult(
        callable $resultCollectionCallback,
        string $relationProperty,
        callable $expectedIdsCallback
    ): void {
        $expectedIds = $expectedIdsCallback();
        $resultCollection = $resultCollectionCallback();
        $ids = $resultCollection->pluck($relationProperty);

        $this->assertCount(count($expectedIds), $resultCollection->toArray());
        $this->assertEqualsCanonicalizing($expectedIds, $ids->toArray());
    }
}
