<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

namespace nullref\core\components;

use Yii;
use yii\base\Module as BaseModule;
use yii\helpers\ArrayHelper;
use yii\web\Application as WebApplication;


class Module extends BaseModule
{
    public function init()
    {
        parent::init();
        /** Add path for views overriding */
        if (Yii::$app instanceof WebApplication) {
            Yii::$app->setComponents([
                /** Init theme for views overriding */
                'view' => ArrayHelper::merge([
                    'theme' => [
                        'basePath' => '@app/views',
                        'pathMap' => ['@nullref/' . $this->id => '@app/modules/' . $this->id]
                    ]], Yii::$app->getComponents()['view'])
            ]);
        }
    }
} 