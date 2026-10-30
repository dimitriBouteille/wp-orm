<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Migrations;

use Dbout\WpOrm\Exceptions\MigrationException;

class MigrationConfig
{
    public const string DEFAULT_TABLE = 'orm_migrations';

    /**
     * @param array<string> $paths Directories containing the migration files
     * @param string $table Migrations table name, without the WordPress prefix
     * @throws MigrationException
     */
    public function __construct(
        public readonly array $paths,
        public readonly string $table = self::DEFAULT_TABLE,
    ) {
        if ($this->paths === [] || !array_is_list($this->paths)) {
            throw new MigrationException('The "paths" option must be a non-empty list of directories.');
        }

        foreach ($this->paths as $path) {
            if (trim($path) === '') {
                throw new MigrationException('Each migration path must be a non-empty string.');
            }
        }

        if (preg_match('/^\w{1,64}\z/', $this->table) !== 1) {
            throw new MigrationException(sprintf(
                'Invalid migrations table name "%s", only letters, numbers and underscores are allowed (64 characters max).',
                $this->table
            ));
        }
    }

    /**
     * @param array<string, mixed> $config
     * @throws MigrationException
     * @return self
     */
    public static function fromArray(array $config): self
    {
        $unknown = array_diff(array_keys($config), ['paths', 'table']);
        if ($unknown !== []) {
            throw new MigrationException(sprintf('Unknown migration option(s): %s.', implode(', ', $unknown)));
        }

        if (!isset($config['paths']) || !is_array($config['paths'])) {
            throw new MigrationException('The "paths" option is required and must be an array.');
        }

        foreach ($config['paths'] as $path) {
            if (!is_string($path)) {
                throw new MigrationException('Each migration path must be a non-empty string.');
            }
        }

        $table = $config['table'] ?? self::DEFAULT_TABLE;
        if (!is_string($table)) {
            throw new MigrationException('The "table" option must be a string.');
        }

        return new self($config['paths'], $table);
    }
}
