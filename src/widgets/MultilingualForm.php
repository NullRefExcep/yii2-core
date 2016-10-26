<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2016 NRE
 */
namespace nullref\core\widgets;

use yii\widgets\ActiveForm;

class MultilingualForm extends ActiveForm
{
    public $language;

    public function field($model, $attribute, $options = [])
    {
        if ($this->language) {
            return parent::field($model, $attribute . '_' . $this->language, $options);
        }
        return parent::field($model, $attribute, $options);
    }
}