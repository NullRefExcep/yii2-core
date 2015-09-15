<?php

namespace nullref\core\components;

/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

use nullref\core\interfaces\IEntityManager;
use nullref\core\models\Model;
use Yii;
use yii\base\Component;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
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

    public $softDelete = true;
    public $deleteField = 'deleted';
    public $deletedValue = true;

    public $extraData = false;
    public $extraField = 'data';
    public $extraFields = [];

    protected $_tableName;

    /**
     * @return string
     */
    public function tableName()
    {
        if (!isset($this->_tableName)) {
            $this->_tableName = call_user_func([$this->getModelClass(), 'tableName']);
        }
        return $this->_tableName;
    }

    /**
     * @return bool
     */
    public function isSoftDelete()
    {
        return boolval($this->softDelete);
    }

    /**
     * @return string
     */
    public function getDeleteField()
    {
        return $this->deleteField;
    }

    /**
     * @param $namespace
     * @param $modelName
     * @param array $config
     * @return array
     */
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
     * @param Model $model
     * @return void
     */
    public function delete($model)
    {
        if ($this->softDelete) {
            $model->setAttribute($this->deleteField,$this->deletedValue);
            $model->save(false, [$this->deleteField]);
        } else {
            $model->delete();
        }
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
        if (empty($this->query)) {
            throw new InvalidConfigException('You must set query class');
        }
        if (empty($this->searchModel)) {
            throw new InvalidConfigException('You must set search model class');
        }
        if (is_array($this->model) && isset($this->model['class']) && isset($this->model['relations'])) {
            /** Set relations behaviors **/
            foreach ($this->model['relations'] as $id => $config) {
                Event::on($this->model['class'], ActiveRecord::EVENT_INIT, function (Event $e) use ($id, $config) {
                    /** @var Component $model */
                    $model = $e->sender;
                    $model->attachBehavior($id, $config);
                });
                Event::on($this->getModelSearchClass(), ActiveRecord::EVENT_INIT, function (Event $e) use ($id, $config) {
                    /** @var Component $model */
                    $model = $e->sender;
                    $model->attachBehavior($id, $config);
                });
            }
            unset($this->model['relations']);
        }
        if (isset($this->type)) {
            $this->typification = true;
        }
        if ($this->extraData) {
            if (empty($this->extraField)) {
                throw new InvalidConfigException('You must set extra field');
            }
            if (is_array($this->extraFields)) {
                /** Check what kind of array is set */
                $fields = $this->extraFields;
                $extraField = $this->extraField;
                if (!(count(array_filter(array_keys($this->extraFields), 'is_string')) > 0)) {
                    $fields = [];
                    foreach ($this->extraFields as $field) {
                        $fields[$field] = '';
                    }
                }
                Event::on($this->getModelClass(), ActiveRecord::EVENT_AFTER_FIND, function (Event $e) use ($fields, $extraField) {
                    /** @var Component $model */
                    $model = $e->sender;
                    $model->{$extraField} = array_merge($fields, $model->{$extraField});
                });

            } else {
                throw new InvalidConfigException('You must set extra fields');
            }
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
        $model = $this->getModelClass();
        /** @var ActiveQuery $query */
        $query = $model::find();
        if (is_array($query)) {
            yii::configure($query, $this->query);
        }

        if ($this->typification) {
            $query->andWhere([$this->tableName() . '.' . $this->typeField => $this->type]);
        }

        if ($this->softDelete) {
            $query->andWhere([$this->tableName() . '.' . $this->deleteField => null]);
        }
        return $query;
    }

    /**
     * @return mixed
     */
    public function createSearchModel()
    {
        $model = Yii::createObject($this->searchModel);

        if ($this->typification) {
            $model->{$this->typeField} = $this->type;
        }
        if ($this->softDelete) {
            $model->{$this->deleteField} = null;
        }
        return $model;
    }

    /**
     * @param $condition
     * @return Model
     */
    public function findOne($condition)
    {
        if(!is_array($condition)){
            $condition = ['id'=>$condition];
        }
        if ($this->typification) {
            $condition = array_merge($condition, [$this->typeField => $this->type]);
        }
        if ($this->softDelete) {
            $condition = array_merge($condition, [$this->deleteField => null]);
        }
        return call_user_func([$this->getModelClass(), 'findOne'], $condition);
    }

    /**
     * @param array $condition
     * @return mixed
     */
    public function findAll($condition = [])
    {
        if ($this->typification) {
            $condition = array_merge($condition, [$this->typeField => $this->type]);
        }
        return call_user_func([$this->getModelClass(), 'findAll'], $condition);
    }

    /**
     * @param array $condition
     * @return ActiveQueryInterface
     */
    public function find($condition = [])
    {
        if ($this->typification) {
            $condition = array_merge($condition, [$this->tableName() . '.' . $this->typeField => $this->type]);
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
            $query->andWhere([$this->tableName() . '.' . $this->typeField => $this->type]);
        }
        if ($this->softDelete) {
            $query->andWhere([$this->deleteField => null]);
        }
        if ($asArray) {
            $query->asArray();
        }
        return ArrayHelper::map($query->all(), $index, $value);
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
    public function getModelSearchClass()
    {
        if (is_array($this->searchModel) && isset($this->searchModel['class'])) {
            return $this->searchModel['class'];
        }
        return $this->searchModel;
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

    /**
     * @param ActiveQuery $query
     */
    public function decorateQuery($query)
    {
        if ($this->typification) {
            $query->andWhere([$this->tableName() . '.' . $this->typeField => $this->type]);
        }

        if ($this->softDelete) {
            $query->andWhere([$this->tableName() . '.' . $this->deleteField => null]);
        }
    }
}