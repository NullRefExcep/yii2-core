<?php

namespace nullref\core;

use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Module as BaseModule;
use yii\console\Application as ConsoleApplication;

/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */
class Module extends BaseModule implements BootstrapInterface
{
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
        if ($app instanceof ConsoleApplication) {
            $app->controllerMap['module'] = [
                'class' => 'nullref\core\console\ModuleController',
            ];
        }
    }

}