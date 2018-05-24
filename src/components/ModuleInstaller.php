<?php

namespace nullref\core\components;

use nullref\core\behaviors\HasManyRelation;
use nullref\core\behaviors\HasOneRelation;
use Yii;
use yii\base\Component;
use yii\console\Application;
use yii\console\Controller;
use yii\db\Connection;
use yii\db\Schema;
use yii\di\Instance;
use yii\helpers\Console;
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

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\NotSupportedException
     */
    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::class);
        $this->db->getSchema()->refresh();
    }

    /**
     * @param $action
     * @return bool
     */
    public function hasChange($action)
    {
        return ($this->getChange($action) !== null);
    }

    /**
     * @param $action
     * @return mixed|null
     */
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

    /**
     * @return array|mixed
     */
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

    /**
     * @return mixed
     */
    public abstract function getModuleId();

    /**
     *
     */
    public function install()
    {
        $this->stdout(' Module "' . $this->getModuleId() . '" installing: ' . PHP_EOL, Console::FG_BLUE, Console::NEGATIVE, Console::BOLD);

        $this->addChange('install');
        if ($this->updateConfig) {
            $this->addToConfig();
            $config = require(Yii::getAlias('@app/config/console.php'));
            $moduleId = $this->moduleId;
            Yii::$app->setModule($moduleId, $config['modules'][$moduleId]);
            Yii::$app->init();
        }
        if ($this->runModuleMigrations || \Yii::$app->controller->confirm('Run migrations')) {
            \Yii::$app->runAction('modules-migrate/up', ['all', 'moduleId' => $this->getModuleId(), 'interactive' => false]);
        }
    }

    /**
     * @param $arg
     * @return bool|int|mixed
     */
    public function stdout($arg)
    {
        /** @var Controller $controller */
        $controller = Yii::$app->controller;
        if ($controller instanceof Controller) {
            return call_user_func_array([$controller, 'stdout'], func_get_args());
        }
        return Console::output($arg);
    }

    /**
     * @param $action
     * @param array $meta
     */
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

    /**
     *
     */
    public function uninstall()
    {
        $this->stdout('Module "' . $this->getModuleId() . '" uninstalling:' . PHP_EOL, Console::FG_RED, Console::NEGATIVE, Console::BOLD);

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
     * @throws \yii\base\Exception
     */
    protected function createFile($alias, $override = true)
    {
        $path = \Yii::getAlias($alias);
        if (file_exists($path) && $override) {
            @unlink($path);
        }
        $dir = dirname($path);
        if (!file_exists($dir)) {
            $this->createFolder($dir);
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
