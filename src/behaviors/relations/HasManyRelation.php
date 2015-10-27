<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

namespace nullref\core\behaviors\relations;


use yii\db\ActiveQuery;

class HasManyRelation extends HasRelation
{
    public $viaTable;
    public $viaLink;

    /**
     * Must implemented in specific relation
     * @return ActiveQuery
     */
    protected function getRelation()
    {
        $q = $this->owner->hasMany($this->foreignModel, [$this->selfField => $this->foreignField]);
        if (is_string($this->viaTable) && is_array($this->viaLink)){
            $q->viaTable($this->viaTable, $this->viaLink);
        }
        return $q;
    }

}