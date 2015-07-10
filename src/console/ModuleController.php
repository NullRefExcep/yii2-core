<?php

namespace nullref\core\console;

use nullref\core\components\ModuleInstaller;
use nullref\core\Installer;
use yii\console\Controller;

/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ModuleController extends Controller
{
    public $db = 'db';

    public function actionIndex()
    {
        $this->run('/help', ['module']);
    }

    /**
     * Run installation
     * @param $name
     */
    public function actionInstall($name)
    {
        $this->runInstallerCommand($name, 'install', 'Module module was reinstalled successfully.');
    }

    /**
     * @param $name
     */
    public function actionUpdateDb($name)
    {
        $this->runInstallerCommand($name, 'updateDb', 'Module module was updated DB successfully.');
    }

    public function actionMigrate()
    {
        $changes = $this->getChanges();
        foreach ($changes as $item) {
            $this->runInstallerCommand($item['module'], $item['action']);
        }
        echo 'Migrate successfully.' . PHP_EOL;
    }

    /**
     * @param $name
     */
    public function actionUninstall($name)
    {
        if ($this->moduleExists($name)) {
            if (($installer = $this->getInstaller($name)) !== null) {
                $installer->uninstall();
                echo 'Module module was uninstalled successfully.' . PHP_EOL;
            } else {
                echo 'Module installer don\'t found.' . PHP_EOL;
            }
        } else {
            echo 'Module don\'t found.' . PHP_EOL;
        }
    }

    protected function getChanges()
    {
        /** @var Installer $installer */
        $installer = \Yii::createObject(Installer::className(), ['db' => $this->db]);

        return $installer->getChanges();
    }

    /**
     * @param $name
     */
    public function actionReinstall($name)
    {
        $this->runInstallerCommand($name, ['uninstall', 'install'], 'Module module was reinstalled successfully.');
    }

    protected function runInstallerCommand($name, $method, $message = '')
    {
        if ($this->moduleExists($name)) {
            if (($installer = $this->getInstaller($name)) !== null) {
                if (is_string($method)) {
                    $installer->$method();
                }
                if (is_array($method)) {
                    foreach ($method as $item) {
                        $installer->$item();
                    }
                }
                echo $message . PHP_EOL;
            } else {
                echo 'Module installer don\'t found.' . PHP_EOL;
            }
        } else {
            echo 'Module don\'t found.' . PHP_EOL;
        }
    }

    /**
     * @param $name
     * @return null|ModuleInstaller
     * @throws \yii\base\InvalidConfigException
     */
    protected function getInstaller($name)
    {
        $installerClassName = '\\nullref\\' . $name . '\\Installer';
        if (class_exists($installerClassName)) {
            return \Yii::createObject($installerClassName, ['db' => $this->db]);
        } else {
            return null;
        }
    }

    /**
     * @param $name
     * @return bool
     */
    protected function moduleExists($name)
    {
        $namespace = 'nullref/yii2-' . $name;
        return isset(\Yii::$app->extensions[$namespace]);
    }
} 