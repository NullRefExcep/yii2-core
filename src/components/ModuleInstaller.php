<?php

namespace nullref\core\components;

use nullref\core\behaviors\HasManyRelation;
use nullref\core\behaviors\HasOneRelation;
use Yii;
use yii\base\Component;
use yii\console\Application;
use yii\db\Connection;
use yii\db\Schema;
use yii\di\Instance;
use yii\helpers\FileHelper;

/**
 * @property string $moduleId
 */
abstract class ModuleInstaller extends Component
{
    /**
     * @var Connection|array|string
     */
    public $db = 'db';

    public $runModuleMigrations = false;
    public $updateConfig = true;

    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::className());
        $this->db->getSchema()->refresh();
    }

    public function hasChange($action)
    {
        return ($this->getChange($action) !== null);
    }

    public function getChange($action)
    {
        $changes = $this->getChanges();
        foreach ($changes as $change) {
            if ($change['action'] == $action && $change['module'] == $this->getModuleId()) {
                return $change;
            }
        }
        return null;
    }

    public function getChanges()
    {
        $array = [];
        if (file_exists($this->getChangesPath())) {
            $array = require($this->getChangesPath());
        }
        return $array;
    }

    /**
     * @return bool|string
     */
    protected function getChangesPath()
    {
        return \Yii::getAlias('@app/config/changes.php');
    }

    public abstract function getModuleId();

    public function install()
    {
        $this->addChange('install');
        if ($this->updateConfig) {
            $this->addToConfig();
            $config = require(Yii::getAlias('@app/config/console.php'));
            $moduleId = $this->moduleId;
            Yii::$app->setModule($moduleId, $config['modules'][$moduleId]);
        }
        if ($this->runModuleMigrations || \Yii::$app->controller->confirm('Run migrations')) {
            \Yii::$app->runAction('modules-migrate/up', ['all', 'moduleId' => $this->getModuleId(), 'interactive' => false]);
        }
    }

    public function addChange($action, $meta = [])
    {
        $changes = $this->getChanges();

        $changes[] = ['module' => $this->getModuleId(), 'action' => $action, 'meta' => $meta];

        $this->writeArrayToFile($this->getChangesPath(), $changes);
    }

    /**
     * Write config file
     * @param $var
     */
    protected function writeArrayToFile($path, $var)
    {
        file_put_contents($path, '<?php' . PHP_EOL . 'return ' . $this->varExport($var) . ';');
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


        $this->writeArrayToFile($this->getConfigPath(), $config);
    }

    /**
     * @return bool|string
     */
    protected function getConfigPath()
    {
        return \Yii::getAlias('@app/config/installed_modules.php');
    }

    /**
     * @return array
     */
    protected function getConfigArray()
    {
        return ['class' => str_replace('Installer', 'Module', get_called_class())];
    }

    public function uninstall()
    {
        $this->addChange('uninstall');
        if ($this->updateConfig) {
            $this->removeFromConfig();
        }
        if ($this->runModuleMigrations || \Yii::$app->controller->confirm('Down module migrations?')) {
            \Yii::$app->runAction('modules-migrate/down', ['all', 'moduleId' => $this->getModuleId(), 'interactive' => false]);
        }
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
        $this->writeArrayToFile($this->getConfigPath(), $config);
    }


    /**
     * Create file by alias
     *
     * @param $alias
     * @param bool $override
     */
    protected function createFile($alias, $override = true)
    {
        $path = \Yii::getAlias($alias);
        if (file_exists($path) && $override) {
            @unlink($path);
        }
        touch($path);
    }

    /**
     * Create directory by alias
     *
     * @param $alias
     * @param int $mode
     * @throws \yii\base\Exception
     */
    protected function createFolder($alias, $mode = 0775)
    {
        FileHelper::createDirectory(Yii::getAlias($alias), $mode);
    }

    /**
     * Delete file is exist
     * @param $alias
     */
    protected function deleteFile($alias)
    {
        $path = \Yii::getAlias($alias);
        if (file_exists($path)) {
            @unlink($path);
        }
    }
}