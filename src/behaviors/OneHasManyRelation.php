<?php

namespace nullref\core\behaviors;

use yii\db\ActiveRecord;

/**
 * Behavior which provide connection with category
 * @author    Dmytro Karpovych
 *
 * @package nullref\category\behaviors
 *
 * @property ActiveRecord $owner
 */
abstract class OneHasManyRelation extends ManyHasOneRelation
{
    protected function getRelation()
    {
        $query = $this->owner->hasMany($this->getManager()->getModelClass(), [$this->getFieldName() => 'id']);
        if ($this->getManager()->isSoftDelete()){
            return $query->where([$this->getManager()->tableName().'.'.$this->getManager()->getDeleteField() => null]);
        }
        return $query;
    }

}