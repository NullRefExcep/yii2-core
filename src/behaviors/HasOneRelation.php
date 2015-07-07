<?php

namespace nullref\core\behaviors;

use nullref\category\models\Category;
use nullref\core\interfaces\IEntityManageble;
use nullref\core\traits\EntityManageble;
use yii\base\Behavior;
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
abstract class HasOneRelation extends Behavior implements IEntityManageble
{
    use EntityManageble;

    public abstract function getFieldName();

    public abstract function getAttributeName();

    public abstract function getAttributeLabel();

    public function __call($name, $params)
    {
        if ($name == 'get' . ucfirst($this->getFieldName())) {
            return $this->owner->hasOne($this->getManager()->getModelClass(), ['id' => $this->getAttributeName()]);
        }
        parent::__call($name, $params);
    }

}