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
        $namespace = 'nullref/yii2-' . $name;
        $installerClassName = '\\nullref\\' . $name . '\\Installer';
        if (isset(\Yii::$app->extensions[$namespace])) {
            if (class_exists($installerClassName)) {
                /** @var ModuleInstaller $installer */
                $installer = \Yii::createObject($installerClassName, ['db' => $this->db]);
                $installer->install();
                echo 'Module module was installed successfully.' . PHP_EOL;
            } else {
                echo 'Module installer don\'t found.' . PHP_EOL;
            }
        } else {
            echo 'Module don\'t found.' . PHP_EOL;
        }
    }
} 