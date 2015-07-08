<?php

namespace nullref\core\models;

use nullref\core\behaviors\HasRelation;
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
            if ($behavior instanceof HasRelation) {
                $fields[] = $behavior->getAttributeName();
            }
        }
        return array_merge(parent::rules(), [[$fields, 'safe']]);
    }

    protected $_labels = null;

    public function attributeLabels()
    {
        if ($this->_labels === null) {
            $this->_labels = [];
            foreach ($this->behaviors as $behavior) {
                if ($behavior instanceof HasRelation) {
                    $this->_labels[$behavior->getAttributeName()] = $behavior->getAttributeLabel();
                }
            }
        }
        return array_merge(parent::attributeLabels(), $this->_labels);
    }


} 