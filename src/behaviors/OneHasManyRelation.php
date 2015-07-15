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
        return $this->owner->hasMany($this->getManager()->getModelClass(), [$this->getFieldName() => 'id']);
    }

}