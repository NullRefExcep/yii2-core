<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

namespace nullref\core\behaviors;


use nullref\core\interfaces\IEntityManageble;
use nullref\core\traits\EntityManageble;
use yii\base\Behavior;

abstract class HasRelation extends Behavior implements IEntityManageble
{
    public abstract function getRelationName();

    public abstract function getAttributeName();

    public abstract function getAttributeLabel();

    protected abstract function getRelation();

} 