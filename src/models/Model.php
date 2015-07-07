<?php

namespace nullref\core\models;

use nullref\core\behaviors\HasOneRelation;
use yii\db\ActiveRecord;

/**
 * Class Model
 * @package nullref\core\models
 */
class Model extends ActiveRecord
{
    public function rules()
    {
        $fields = [];
        foreach ($this->behaviors as $behavior) {
            if ($behavior instanceof HasOneRelation) {
                $fields[] = $behavior->getAttributeName();
            }
        }
        return array_merge(parent::rules(), [[$fields, 'safe']]);
    }

    public function attributeLabels()
    {
        $labels = [];
        foreach ($this->behaviors as $behavior) {
            if ($behavior instanceof HasOneRelation) {
                $fields[$behavior->getAttributeName()] = $behavior->getAttributeLabel();
            }
        }
        return array_merge(parent::attributeLabels(), $labels);
    }


} 