<?php

namespace nullref\core\components;

use yii\base\Component;
use yii\db\Connection;
use yii\di\Instance;
use Composer\Installer\PackageEvent;

/**
 *
 */
class ModuleInstaller extends Component
{
    /**
     * @var Connection|array|string
     */
    protected $db = 'db';

    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::className());
        $this->db->getSchema()->refresh();
    }

    public function install(PackageEvent $event)
    {
        //do some stuff
    }

    public function uninstall(PackageEvent $event)
    {
        //do some stuff
    }

    /**
     * Check if table exist
     * @param $tableName
     * @return bool
     */
    public function tableExist($tableName)
    {
        return $this->db->schema->getTableSchema($tableName, true) !== null;
    }

    /**
     * Builds and executes a SQL statement for dropping a DB table.
     * @param string $table the table to be dropped. The name will be properly quoted by the method.
     */
    public function dropTable($table)
    {
        $this->db->createCommand()->dropTable($table)->execute();
    }

    /**
     * Create table by name, columns and options
     * @param $table
     * @param $columns
     * @param null $options
     */
    public function createTable($table, $columns, $options = null)
    {
        $this->db->createCommand()->createTable($table, $columns, $options)->execute();
    }
}



