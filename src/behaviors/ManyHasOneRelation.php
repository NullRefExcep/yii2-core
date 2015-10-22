<?php

namespace nullref\core\behaviors;

use nullref\core\traits\EntityManageble;
use yii\db\ActiveRecord;

/**
 * Behavior which provide connection with category
 * @author    Dmytro Karpovych
 *
 * @package nullref\category\behaviors
 *
 * @property ActiveRecord $owner
 */
abstract class ManyHasOneRelation extends HasRelation
{
    use EntityManageble;

    protected function getRelation()
    {
        return $this->owner->hasOne($this->getManager()->getModelClass(), ['id' => $this->getFieldName()]);
    }

    public function getAttributeName()
    {
        return $this->attributeName;
    }

    public function getRelationName()
    {
        return $this->relationName;
    }

    public function getFieldName()
    {
        return $this->fieldName;
    }

}