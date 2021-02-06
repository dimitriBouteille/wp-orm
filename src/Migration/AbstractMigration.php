<?php

namespace Dbout\WpOrm\Migration;

use \Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Builder;

/**
 * Class AbstractMigration
 * @package Dbout\WpOrm\Migration
 */
abstract class AbstractMigration extends \Phinx\Migration\AbstractMigration
{

    /**
     * @var Capsule
     */
    protected Capsule $capsule;

    /**
     * @var Builder|null
     */
    protected ?Builder $schema;

    /**
     * @return void
     */
    public function init()
    {
        $this->capsule = new Capsule();
        $this->capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => '',
            'port'      => '',
            'database'  => '',
            'username'  => '',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ]);

        $this->capsule->bootEloquent();
        $this->capsule->setAsGlobal();
        $this->schema = $this->capsule->schema();
    }
}