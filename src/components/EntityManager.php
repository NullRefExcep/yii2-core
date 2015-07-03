<?php

namespace nullref\core\components;

/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class EntityManager extends Component
{
    public $modelClass = '';
    public $queryClass = '';
    public $searchModelClass = '';

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
    }


    /**
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    public function createModel()
    {
        return Yii::createObject($this->modelClass);
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
        return \Yii::createObject($this->searchModelClass);
    }

    public function findOne($condition)
    {
        return call_user_func(array($this->modelClass, 'findOne'), [$condition]);
    }

    public function findAll($condition)
    {
        return call_user_func(array($this->modelClass, 'findAll'), [$condition]);
    }

    public function find($condition)
    {
        return call_user_func(array($this->modelClass, 'find'), [$condition]);
    }
}