<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2017 NRE
 */

namespace nullref\core\widgets;


use yii\base\Model;
use yii\base\Widget;
use yii\helpers\Html;

class ActiveRangeInputGroup extends Widget
{
    /**
     * @var Model the data model that this widget is associated with.
     */
    public $model;

    public $attributeFrom;

    public $attributeTo;

    public $inputBuilder;
    /**
     * @var array the HTML attributes for the input tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $inputOptions = [
        'class' => 'form-control',
        'style' => 'width: 50%;',
    ];
    public $options = [];

    public function init()
    {
        parent::init();
        if ($this->inputBuilder === null) {
            $this->inputBuilder = function ($model, $attribute, $inputOptions) {
                return Html::activeTextInput($model, $attribute, $inputOptions);
            };
        }
    }

    public function run()
    {
        return Html::tag('div',
            call_user_func_array($this->inputBuilder, [$this->model, $this->attributeFrom, $this->inputOptions]) .
            call_user_func_array($this->inputBuilder, [$this->model, $this->attributeTo, $this->inputOptions]),
            array_merge(['class' => 'input-group'], $this->options));
    }
}
