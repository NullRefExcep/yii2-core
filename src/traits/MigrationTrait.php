<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

namespace nullref\core\traits;

use yii\db\Connection;

/**
 * Trait MigrationTrait
 * Extend migration functionality
 *
 * @property Connection $db
 *
 * @package nullref\core\traits
 */
trait MigrationTrait
{
    /**
     * @param $tableName
     * @return bool
     */
    public function tableExist($tableName)
    {
        return $this->db->schema->getTableSchema($tableName, true) !== null;
    }

    /**
     * @param $tableName
     * @param $columnName
     * @return bool
     */
    public function hasColumn($tableName, $columnName)
    {
        return ($this->tableExist($tableName) && isset($this->db->schema->getTableSchema($tableName, true)->columns[$columnName]));
    }

    /**
     * @param null $tableOptions
     * @return null|string
     */
    public function getTableOptions($tableOptions = null)
    {
        if ((\Yii::$app->db->driverName === 'mysql') && ($tableOptions === null)) {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        return $tableOptions;
    }

    /**
     * @param $message
     * @param bool|false $default
     * @return bool
     */
    public function confirm($message, $default = false)
    {
        return \Yii::$app->controller->confirm($message,$default);
    }

    /**
     * @param $string
     * @return mixed
     */
    public function stdout($string)
    {
        return \Yii::$app->controller->stdout($string);
    }
}