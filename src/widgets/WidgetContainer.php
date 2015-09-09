<?php

namespace nullref\core\widgets;

use yii\base\InvalidConfigException;
use yii\base\Widget;

/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */
class WidgetContainer extends Widget
{
    /**
     * item example [
     * 'class'=>'ClassNameOfWidget',
     * 'config'=>[
     *  'id'=>'bla-bla',
     *  'otherField'=>72
     *  ]
     * ]
     * @var array
     */
    public $widgets = [];

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        $newWidgets = [];
        if (is_array($this->widgets)) {
            foreach ($this->widgets as $widget) {
                if (is_array($widget)) {
                    if (!isset($widget['class'])) {
                        new InvalidConfigException('widget config must has class field');
                    }
                    $newWidgets[] = [
                        'class' => $widget['class'],
                        'config' => (isset($widget['config'])) ? $widget['config'] : [],
                    ];
                } else {
                    $newWidgets[] = [
                        'class' => $widget,
                        'config' => [],
                    ];
                }
            }
            $this->widgets = $newWidgets;
        } else {
            throw new InvalidConfigException('"widgets" fields must be array');
        }
    }

    /**
     * @return string
     */
    public function run()
    {
        $result = '';
        foreach ($this->widgets as $widget) {
            $result .= call_user_func(array($widget['class'], 'widget'), $widget['config']);
        }
        return $result;
    }

}