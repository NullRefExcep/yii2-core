<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

namespace nullref\core\components;

use Yii;
use yii\base\Event;
use yii\base\Module as BaseModule;
use yii\web\Application as WebApplication;


class Module extends BaseModule
{
    const EVENT_BEFORE_INIT = 'beforeInit';

    const EVENT_AFTER_INIT = 'afterInit';

    public function init()
    {
        $this->trigger(self::EVENT_BEFORE_INIT);

        parent::init();
        /** Add path for views overriding */
        if (Yii::$app instanceof WebApplication) {
            $view = Yii::$app->getView();
            if ($view->theme === null) {
                $view->theme = Yii::createObject(['class' => 'yii\base\Theme', 'pathMap' => []]);
            }
            $view->theme->pathMap['@nullref/' . $this->id] = '@app/modules/' . $this->id;
        }

        $this->trigger(self::EVENT_AFTER_INIT);
    }
} 