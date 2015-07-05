<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

namespace nullref\core;

use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;
use yii\web\Application as WebApplication;

class Bootstrap implements BootstrapInterface
{

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        if ($app instanceof WebApplication) {
            $app->setComponents([
                'view' => ['class' => 'nullref\core\components\View'],
            ]);
        }
        if ($app instanceof ConsoleApplication) {
            $app->controllerMap['module'] = [
                'class' => 'nullref\core\console\ModuleController',
            ];
        }
    }
} 