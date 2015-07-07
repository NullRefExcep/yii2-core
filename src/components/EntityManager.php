<?php

namespace nullref\core\components;

/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

use nullref\core\interfaces\IEntityManager;
use Yii;
use yii\base\Component;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

class EntityManager extends Component implements IEntityManager
{
    public $model = '';
    public $query = '';
    public $searchModel = '';
    /** @var string|Connection */
    public $db = '';

    public $typification = false;
    public $typeField = 'type';
    public $type = null;


    public static function getConfig($namespace, $modelName, $config = [])
    {
        $default = [
            'class' => static::className(),
            'model' => $namespace . $modelName,
            'query' => $namespace . $modelName . 'Query',
            'searchModel' => $namespace . $modelName . 'Search',
        ];
        return array_merge($default, $config);
    }

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (empty($this->model)) {
            throw new InvalidConfigException('You must set model class');
        }
        if (is_array($this->model) && isset($this->model['class']) && isset($this->model['relations'])) {
            foreach ($this->model['relations'] as $id => $config) {
                Event::on($this->model['class'], ActiveRecord::EVENT_INIT, function (Event $e) use ($id, $config) {
                    /** @var Component $model */
                    $model = $e->sender;
                    $model->attachBehavior($id, $config);
                });
            }
            unset($this->model['relations']);
        }
        if (empty($this->query)) {
            throw new InvalidConfigException('You must set query class');
        }
        if (empty($this->searchModel)) {
            throw new InvalidConfigException('You must set search model class');
        }
        if (isset($this->type)) {
            $this->typification = true;
        }
    }

    /**
     * @return ActiveRecord
     * @throws \yii\base\InvalidConfigException
     */
    public function createModel()
    {
        $model = Yii::createObject($this->model);

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
        return Yii::createObject($this->query);
    }

    /**
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    public function createSearchModel()
    {
        $model = Yii::createObject($this->searchModel);

        if ($this->typification) {
            $model->{$this->typeField} = $this->type;
        }
        return $model;
    }

    public function findOne($condition)
    {
        return call_user_func([$this->getModelClass(), 'findOne'], [$condition]);
    }

    public function findAll($condition = [])
    {
        if ($this->typification) {
            $condition = array_merge($condition, [$this->typeField => $this->type]);
        }
        return call_user_func([$this->getModelClass(), 'findAll'], [$condition]);
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
        return call_user_func([$this->getModelClass(), 'find'], [$condition]);
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
        return call_user_func([$this->getModelClass(), 'tableName']);
    }

    /**
     * @return string
     */
    public function getModelClass()
    {
        if (is_array($this->model) && isset($this->model['class'])) {
            return $this->model['class'];
        }
        return $this->model;
    }

    /**
     * @return string
     */
    public function getQueryClass()
    {
        if (is_array($this->query) && isset($this->query['class'])) {
            return $this->query['class'];
        }
        return $this->query;
    }

}