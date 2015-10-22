<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

namespace nullref\core\behaviors\relations;


use yii\db\ActiveQuery;

class HasOneRelation extends HasRelation
{
    /**
     * Must implemented in specific relation
     * @return ActiveQuery
     */
    protected function getRelation()
    {
        return $this->owner->hasOne($this->foreignModel,[$this->foreignField=>$this->selfField]);
    }

}