<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

namespace nullref\core\components;

use Yii;
use yii\base\Module as BaseModule;
use yii\web\Application as WebApplication;


class Module extends BaseModule
{
    public function init()
    {
        parent::init();
        /** Add path for views overriding */
        if (Yii::$app instanceof WebApplication) {
            $view = Yii::$app->getView();
            if ($view->theme === null) {
                $view->theme = Yii::createObject(['class' => 'yii\base\Theme', 'pathMap' => []]);
            }
            $view->theme->pathMap['@nullref/' . $this->id] = '@app/modules/' . $this->id;
        }
    }
} 