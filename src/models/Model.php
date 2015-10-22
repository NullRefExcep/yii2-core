<?php

namespace nullref\core\models;

use nullref\core\behaviors\relations\HasOneRelation;
use nullref\core\behaviors\relations\HasRelation;
use yii\db\ActiveRecord;

/**
 * Class Model
 * @package nullref\core\models
 */
class Model extends ActiveRecord
{
    const EVENT_BEFORE_LOAD = 'beforeLoad';
    const EVENT_AFTER_LOAD = 'afterLoad';

    /**
     * @param array $data
     * @param null $formName
     * @return bool
     */
    public function load($data, $formName = null)
    {
        $this->trigger(self::EVENT_BEFORE_LOAD);
        $result = parent::load($data, $formName);
        $this->trigger(self::EVENT_AFTER_LOAD);
        return $result;
    }

    /**
     * Make safe validation rules for behavior relations
     * @return array
     */
    public function rules()
    {
        $fields = [];
        foreach ($this->behaviors as $behavior) {
            if ($behavior instanceof HasRelation) {
                $fields[] = $behavior->getAttributeName();
                if ($behavior instanceof HasOneRelation) {
                    $fields[] = $behavior->selfField;
                }
            }
        }
        return array_merge(parent::rules(), [[$fields, 'safe']]);
    }

    protected $_labels = null;

    /**
     * Get labels from behavior relations
     * @return array
     */
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

    /**
     * @param array $row
     * @return static
     */
    public static function instantiate($row)
    {
        return \Yii::createObject(get_called_class(),[$row]);
    }


} 