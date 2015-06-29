<?php

namespace nullref\core;

use nullref\core\behaviors\AddAction;
use yii\base\ActionFilter;
use yii\base\Application;
use yii\base\BootstrapInterface;

/**
 * @author    Dmytro Karpovych
 * @copyright 2015 AtNiwe
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Module extends \yii\base\Module implements BootstrapInterface
{
    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {

    }


}