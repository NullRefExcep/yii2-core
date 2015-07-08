<?php

namespace nullref\core\components;

use nullref\core\behaviors\HasManyRelation;
use nullref\core\behaviors\HasOneRelation;
use nullref\core\Module;
use Yii;
use yii\base\Component;
use yii\db\Connection;
use yii\db\Schema;
use yii\di\Instance;

/**
 * @property string $moduleId
 */
abstract class ModuleInstaller extends Component
{
    /**
     * @var Connection|array|string
     */
    public $db = 'db';

    public abstract function getModuleId();

    public $updateConfig = true;

    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::className());
        $this->db->getSchema()->refresh();
    }

    public function install()
    {
        if ($this->updateConfig) {
            $this->addToConfig();
        }
    }

    public function uninstall()
    {
        if ($this->updateConfig) {
            $this->removeFromConfig();
        }
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

    public function hasColumn($tableName, $columnName)
    {
        return ($this->tableExist($tableName) && isset($this->db->schema->getTableSchema($tableName, true)->columns[$columnName]));
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
     * @param $table
     * @param $column
     * @param $type
     * @param bool $override
     */
    public function addColumn($table, $column, $type, $override = false)
    {
        if ($this->hasColumn($table, $column)) {
            if ($override) {
                $this->db->createCommand()->dropColumn($table, $column)->execute();
            } else {
                return;
            }
        }
        $this->db->createCommand()->addColumn($table, $column, $type)->execute();
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


    /**
     * Add module item to config
     */
    protected function addToConfig()
    {
        $path = $this->getConfigPath();
        $config = require($path);
        if (isset($config[$this->moduleId])) {
            if (\Yii::$app->controller->confirm('Rewrite exist config?')) {
                $config[$this->moduleId] = $this->getConfigArray();
                echo 'Module config was rewrote' . PHP_EOL;
            }
        } else {
            $config[$this->moduleId] = $this->getConfigArray();
        }


        $this->writeConfigFile($config);
    }

    /**
     * @return array
     */
    protected function getConfigArray()
    {
        return ['class' => str_replace('Installer', 'Module', get_called_class())];
    }

    /**
     * Remove module item from config
     */
    protected function removeFromConfig()
    {
        $path = $this->getConfigPath();
        $config = require($path);

        if (isset($config[$this->moduleId])) {
            unset($config[$this->moduleId]);
        }
        $this->writeConfigFile($config);
    }

    /**
     * Write config file
     * @param $var
     */
    protected function writeConfigFile($var)
    {
        file_put_contents($this->getConfigPath(), '<?php' . PHP_EOL . 'return ' . $this->varExport($var) . ';');
    }

    /**
     * @return bool|string
     */
    protected function getConfigPath()
    {
        return \Yii::getAlias('@app/config/installed_modules.php');
    }

    /**
     * var_export as php 5.4
     * @param $var
     * @param string $indent
     * @return mixed|string
     */
    protected function varExport($var, $indent = "")
    {
        switch (gettype($var)) {
            case "string":
                return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
            case "array":
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = "$indent    "
                        . ($indexed ? "" : $this->varExport($key) . " => ")
                        . $this->varExport($value, "$indent    ");
                }
                return "[\n" . implode(",\n", $r) . $indent . "\n    ]";
            case "boolean":
                return $var ? "true" : "false";
            default:
                return var_export($var, true);
        }
    }

    protected function createFile($alias, $override = true)
    {
        $path = \Yii::getAlias($alias);
        if (file_exists($path) && $override) {
            @unlink($path);
        }
        touch($path);
    }

    protected function deleteFile($alias)
    {
        $path = \Yii::getAlias($alias);
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    /**
     * Add column for category relation if entity has it
     */
    public function update()
    {
        $module = Yii::$app->getModule($this->moduleId);
        /** @var Module $module */
        foreach ($module->getComponents() as $id => $component) {
            if (is_array($component)) {
                $component = $module->get($id);
            }
            if ($component instanceof EntityManager) {
                $model = $component->createModel();
                foreach ($model->behaviors as $behavior) {
                    if ($behavior instanceof HasOneRelation) {
                        $this->addColumn($model->tableName(), $behavior->getAttributeName(), Schema::TYPE_INTEGER);
                    }
                }
                foreach ($model->behaviors as $behavior) {
                    if ($behavior instanceof HasManyRelation) {
                        if (!$this->tableExist($behavior->getTableName())) {
                            $this->createTable($behavior->getTableName(), [
                                $behavior->getToFieldName() => Schema::TYPE_INTEGER,
                                $behavior->getFromFieldName() => Schema::TYPE_INTEGER,
                            ]);
                            $this->db->createCommand()
                                ->addPrimaryKey($behavior->getFromFieldName() . $behavior->getToFieldName(),
                                    $behavior->getTableName(), [
                                        $behavior->getToFieldName(),
                                        $behavior->getFromFieldName()
                                    ])
                                ->execute();
                        }
                    }
                }
            }

        }
    }
}