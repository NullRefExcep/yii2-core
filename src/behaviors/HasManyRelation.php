<?php

namespace nullref\core\behaviors;

use voskobovich\behaviors\ManyToManyBehavior;
use yii\db\ActiveRecord;

/**
 * Behavior which provide connection with category
 * @author    Dmytro Karpovych
 *
 * @package nullref\category\behaviors
 *
 * @property ActiveRecord $owner
 */
abstract class HasManyRelation extends HasRelation
{
    public function getAttributeName()
    {
        return $this->attributeName;
    }

    public function getFromFieldName()
    {
        return $this->fromFieldName;
    }

    public function getToFieldName()
    {
        return $this->toFieldName;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function attach($owner)
    {
        $owner->attachBehavior($this->getRelationName(), [
            'class' => ManyToManyBehavior::className(),
            'relations' => [
                $this->getAttributeName() => $this->getRelationName(),
            ],
        ]);
        parent::attach($owner);
    }

    public function getRelationName()
    {
        return $this->getAttributeName() . 'Relation';
    }

    protected function getRelation()
    {
        return $this->owner->hasMany($this->getManager()->getModelClass(), ['id' => $this->getToFieldName()])
            ->viaTable($this->getTableName(), [$this->getFromFieldName() => 'id']);
    }

    public function __call($name, $params)
    {
        if ($name == 'get' . $this->getRelationName()) {
            return $this->getRelation();
        }
        return parent::__call($name, $params);
    }

    public function hasMethod($name)
    {
        if ($name == 'get' . $this->getRelationName()) {
            return true;
        }
        return parent::hasMethod($name);
    }
}