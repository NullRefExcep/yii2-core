<?php

namespace nullref\core\console;

use nullref\core\components\ModuleInstaller;
use nullref\core\Installer;
use Symfony\Component\Finder\Finder;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ModuleController extends Controller
{
    public $db = 'db';

    /**
     *
     */
    public function actionIndex()
    {
        $this->run('/help', ['module']);
    }

    /**
     * Run installation
     *
     * @param $name
     * @throws \yii\base\InvalidConfigException
     */
    public function actionInstall($name)
    {
        $this->runInstallerCommand($name, 'install', 'Module module was installed successfully.');
    }

    /**
     * @param $name
     * @param $method
     * @param string $message
     * @throws \yii\base\InvalidConfigException
     */
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
                Console::output($message);
            } else {
                Console::output('Module installer don\'t found.');
            }
        } else {
            Console::output('Module don\'t found.');
        }
    }

    /**
     * @param $name
     * @return bool
     */
    protected function moduleExists($name)
    {
        return isset(\Yii::$app->extensions[$name]);
    }

    /**
     * @param $name
     * @return null|ModuleInstaller
     * @throws \yii\base\InvalidConfigException
     */
    protected function getInstaller($name)
    {
        $extension = \Yii::$app->extensions[$name];

        $namespace = false;
        foreach ($extension['alias'] as $key => $alias) {
            $path = \Yii::getAlias($alias);
            $files = iterator_to_array(Finder::create()->files()->name('Installer.php')->in($path));
            if (count($files)) {
                $namespace = str_replace('/', '\\', substr($key, 1));
                break;
            }
        }

        if ($namespace !== false) {
            $installerClassName = $namespace . '\\Installer';
            if (class_exists($installerClassName)) {
                return \Yii::createObject($installerClassName, ['db' => $this->db]);
            } else {
                return null;
            }
        }
    }

    /**
     *
     */
    public function actionMigrate()
    {
        $changes = $this->getChanges();
        foreach ($changes as $item) {
            $this->runInstallerCommand($item['module'], $item['action']);
        }
        Console::output('Migrate successfully.');
    }

    /**
     * @return array|mixed
     * @throws \yii\base\InvalidConfigException
     */
    protected function getChanges()
    {
        /** @var Installer $installer */
        $installer = \Yii::createObject(Installer::class, ['db' => $this->db]);

        return $installer->getChanges();
    }

    /**
     * @param $name
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUninstall($name)
    {
        if ($this->moduleExists($name)) {
            if (($installer = $this->getInstaller($name)) !== null) {
                if (\Yii::$app->getModule($installer->moduleId) === null) {
                    Console::output('Module was not installed');
                    return;
                }
                $installer->uninstall();
                Console::output('Module module was uninstalled successfully.');
            } else {
                Console::output('Module installer don\'t found.');
            }
        } else {
            Console::output('Module don\'t found.');
        }
    }

    /**
     * @param $name
     * @throws \yii\base\InvalidConfigException
     */
    public function actionReinstall($name)
    {
        $this->runInstallerCommand($name, ['uninstall', 'install'], 'Module module was reinstalled successfully.');
    }

    /**
     * @param $modelName1
     * @param $modelName2
     */
    public function actionBindModels($modelName1, $modelName2)
    {
        $model1 = new $modelName1();
        $model2 = new $modelName2();

        if ($this->confirm('Generate migration?')) {
            //@TODO
        }
    }
} 