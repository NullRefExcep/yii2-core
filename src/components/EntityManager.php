<?php

namespace nullref\core\components;

/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\Connection;

class EntityManager extends Component
{
    public $modelClass = '';
    public $queryClass = '';
    public $searchModelClass = '';
    /** @var string|Connection */
    public $db = '';

    public $typification = false;
    public $typeField = 'type';
    public $type = null;


    public static function getConfig($namespace, $modelName, $config = [])
    {
        $default = [
            'class' => static::className(),
            'modelClass' => $namespace . $modelName,
            'queryClass' => $namespace . $modelName . 'Query',
            'searchModelClass' => $namespace . 'Search' . $modelName,
        ];
        return array_merge($default, $config);
    }

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (empty($this->modelClass)) {
            throw new InvalidConfigException('You must set model class');
        }
        if (empty($this->queryClass)) {
            throw new InvalidConfigException('You must set query class');
        }
        if (empty($this->searchModelClass)) {
            throw new InvalidConfigException('You must set search model class');
        }
        if (isset($this->type)) {
            $this->typification = true;
        }
    }

    /**
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    public function createModel()
    {
        $model = Yii::createObject($this->modelClass);

        if ($this->typification) {
            $model->{$this->typeField} = $this->type;
        }
        return $model;
    }

    /**
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    public function createQuery()
    {
        return Yii::createObject($this->queryClass);
    }

    /**
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    public function createSearchModel()
    {
        $model = Yii::createObject($this->searchModelClass);

        if ($this->typification) {
            $model->{$this->typeField} = $this->type;
        }
        return $model;
    }

    public function findOne($condition)
    {
        if ($this->typification) {
            //$condition = array_merge($condition, [$this->typeField => $this->type]);
        }
        return call_user_func([$this->modelClass, 'findOne'], [$condition]);
    }

    public function findAll($condition = [])
    {
        if ($this->typification) {
            $condition = array_merge($condition, [$this->typeField => $this->type]);
        }
        return call_user_func([$this->modelClass, 'findAll'], [$condition]);
    }

    public function find($condition = [])
    {
        if ($this->typification) {
            $condition = array_merge($condition, [$this->typeField => $this->type]);
        }
        return call_user_func([$this->modelClass, 'find'], [$condition]);
    }

    public function tableName()
    {
        return call_user_func([$this->modelClass, 'tableName']);
    }
}