<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

namespace nullref\core\behaviors\relations;

use yii\base\Behavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

/**
 *
 * Class HasRelation
 * @package nullref\core\behaviors\relations
 *
 * @property ActiveRecord $owner
 */
abstract class HasRelation extends Behavior
{
    public $selfField = 'id';

    public $attributeName;
    public $attributeLabel;

    public $foreignModel;
    public $foreignField = 'id';

    /**
     * Must implemented in specific relation
     * @return ActiveQuery
     */
    protected abstract function getRelation();

    /**
     * @return string
     */
    public function getAttributeName()
    {
        return $this->attributeName;
    }

    /**
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public function __call($name, $params)
    {
        if ($name == 'get' . $this->getAttributeName()) {
            return $this->getRelation();
        }
        return parent::__call($name, $params);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasMethod($name)
    {
        if ($name == 'get' . $this->getAttributeName()) {
            return true;
        }
        return parent::hasMethod($name);
    }

    /**
     * @param string $name
     * @param bool|true $checkVars
     * @return bool
     */
    public function canGetProperty($name, $checkVars = true)
    {
        if ($name ==  $this->getAttributeName()) {
            return true;
        }
        return parent::canGetProperty($name, $checkVars);
    }

    public function __get($name)
    {
        if ($name ==  $this->getAttributeName()) {
            return $this->getRelation();
        }
        return parent::__get($name);
    }


    /**
     * @return mixed
     */
    public function getAttributeLabel()
    {
        return empty($this->attributeLabel) ? Inflector::camel2words($this->getAttributeName()) : $this->attributeLabel;
    }

} 