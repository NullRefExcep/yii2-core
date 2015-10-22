<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

namespace nullref\core;

use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\console\Application as ConsoleApplication;
use yii\gii\Module as Gii;
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

        }
        if ($app instanceof ConsoleApplication) {
            $app->controllerMap['module'] = [
                'class' => 'nullref\core\console\ModuleController',
            ];
            $app->controllerMap['modules-migrate'] = [
                'class' => 'nullref\core\console\ModulesMigrateController',
            ];
        }
        if (YII_ENV_DEV) {
            Event::on(Gii::className(), Gii::EVENT_BEFORE_ACTION, function (Event $event) {
                /** @var Gii $gii */
                $gii = $event->sender;
                $gii->generators['relation-migration'] = [
                    'class' => 'nullref\core\generators\migration\Generator',
                ];
            });
        }
    }
} 