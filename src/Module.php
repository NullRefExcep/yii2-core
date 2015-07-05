<?php

namespace nullref\core;

use yii\base\Module as BaseModule;

/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */
class Module extends BaseModule
{
    /**
     * @return string
     */
    public static function getRootDir()
    {
        //@TODO make better implementation
        return dirname(dirname(dirname(dirname(__DIR__))));
    }

}