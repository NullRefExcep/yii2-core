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
    public function actionIndex()
    {
        $this->run('/help', ['module']);
    }

    public function actionInstall($name)
    {
        $namespace = 'nullref/yii2-' . $name;
        $module = \Yii::$app->extensions[$namespace];
        $installerClassName = '\\nullref\\' . $name . '\\Installer.php';
        /** @var ModuleInstaller $installer */
        $installer = \Yii::createObject($installerClassName);
        $installer->install();
    }
} 