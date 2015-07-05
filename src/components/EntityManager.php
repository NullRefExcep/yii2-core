<?php

namespace nullref\core\components;

/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

use nullref\core\interfaces\IEntityManager;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\ActiveQueryInterface;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

class EntityManager extends Component implements IEntityManager
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
        return call_user_func([$this->modelClass, 'findOne'], [$condition]);
    }

    public function findAll($condition = [])
    {
        if ($this->typification) {
            $condition = array_merge($condition, [$this->typeField => $this->type]);
        }
        return call_user_func([$this->modelClass, 'findAll'], [$condition]);
    }

    /**
     * @param array $condition
     * @return ActiveQueryInterface
     */
    public function find($condition = [])
    {
        if ($this->typification) {
            $condition = array_merge($condition, [$this->typeField => $this->type]);
        }
        return call_user_func([$this->modelClass, 'find'], [$condition]);
    }

    /**
     * @param string $index
     * @param string $value
     * @param array $condition
     * @param bool $asArray
     * @return array
     */
    public function getMap($index = 'id', $value = 'title', $condition = [], $asArray = true)
    {
        $query = static::find()->andWhere($condition);
        if ($this->typification) {
            $query->andWhere([$this->typeField => $this->type]);
        }
        if ($asArray) {
            $query->asArray();
        }
        return ArrayHelper::map($query->all(), $index, $value);
    }

    public function tableName()
    {
        return call_user_func([$this->modelClass, 'tableName']);
    }
}