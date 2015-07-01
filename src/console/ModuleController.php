<?php

namespace nullref\core\console;

use nullref\core\components\ModuleInstaller;
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

    public function actionInstall($name)
    {
        if ($this->moduleExists($name)) {
            if (($installer = $this->getInstaller($name)) !== null) {
                $installer->install();
                echo 'Module module was installed successfully.' . PHP_EOL;
            } else {
                echo 'Module installer don\'t found.' . PHP_EOL;
            }
        } else {
            echo 'Module don\'t found.' . PHP_EOL;
        }
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

    /**
     * @param $name
     */
    public function actionReinstall($name)
    {
        if ($this->moduleExists($name)) {
            if (($installer = $this->getInstaller($name)) !== null) {
                $installer->uninstall();
                $installer->install();
                echo 'Module module was reinstalled successfully.' . PHP_EOL;
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