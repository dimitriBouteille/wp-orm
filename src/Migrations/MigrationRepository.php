<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Migrations;

use Illuminate\Database\Migrations\DatabaseMigrationRepository;

/**
 * wpdb returns every value as a string, batch numbers and ids are cast back to integers.
 */
class MigrationRepository extends DatabaseMigrationRepository
{
    /**
     * @inheritDoc
     */
    public function getMigrations($steps)
    {
        return $this->castRecords(parent::getMigrations($steps));
    }

    /**
     * @inheritDoc
     */
    public function getMigrationsByBatch($batch)
    {
        return $this->castRecords(parent::getMigrationsByBatch($batch));
    }

    /**
     * @inheritDoc
     */
    public function getLast()
    {
        return $this->castRecords(parent::getLast());
    }

    /**
     * @inheritDoc
     */
    public function getMigrationBatches()
    {
        return array_map(intval(...), parent::getMigrationBatches());
    }

    /**
     * @inheritDoc
     */
    public function getLastBatchNumber()
    {
        return (int) parent::getLastBatchNumber();
    }

    /**
     * @param array<object> $records
     * @return array<object>
     */
    protected function castRecords(array $records): array
    {
        foreach ($records as $record) {
            if (isset($record->id)) {
                $record->id = (int) $record->id;
            }

            if (isset($record->batch)) {
                $record->batch = (int) $record->batch;
            }
        }

        return $records;
    }
}
