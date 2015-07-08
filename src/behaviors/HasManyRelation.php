<?php

namespace nullref\core\behaviors;

use nullref\category\models\Category;
use yii\db\ActiveRecord;

/**
 * Behavior which provide connection with category
 * @author    Dmytro Karpovych
 *
 * @package nullref\category\behaviors
 *
 * @property ActiveRecord $owner
 * @property Category $category
 */
abstract class HasManyRelation extends HasRelation
{
    public abstract function getFromFieldName();

    public abstract function getToFieldName();

    public abstract function getTableName();

    public function canGetProperty($name, $checkVars = true)
    {
        if ($name == $this->getAttributeName()) {
            return true;
        }
        return parent::canGetProperty($name, $checkVars);
    }

    public function __get($name)
    {
        if ($name == $this->getAttributeName()) {
            return $this->owner->hasMany($this->getManager()->getModelClass(), ['id' => $this->getToFieldName()])
                ->viaTable($this->getTableName(), [$this->getFromFieldName() => 'id']);
        }
        return parent::__get($name);
    }
}