<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

namespace nullref\core\components;

use Yii;
use yii\base\Module as BaseModule;


class Module extends BaseModule
{
    public function init()
    {
        parent::init();
        /** Add path for views overriding */
        Yii::$app->view->theme->pathMap['@nullref/' . $this->id] = '@app/modules/' . $this->id;
    }
} 