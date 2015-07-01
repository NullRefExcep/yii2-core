<?php

namespace nullref\core;

use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;

/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Module extends \yii\base\Module implements BootstrapInterface
{
    public $layout = 'main';

    /**
     * @return string
     */
    public static function getRootDir()
    {
        //@TODO make better implementation
        return dirname(dirname(dirname(dirname(__DIR__))));
    }

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        \Yii::$app->setComponents(['admin' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\User',
        ]]);

        if ($app instanceof ConsoleApplication) {
            $app->controllerMap['module'] = [
                'class' => 'nullref\core\console\ModuleController',
            ];
        }
    }

    public function init()
    {
        $this->setLayoutPath('@vendor/nullref/yii2-core/src/views/layouts');
        parent::init();
    }


}