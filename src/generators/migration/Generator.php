<?php

namespace nullref\core\generators\migration;

use nullref\core\behaviors\ManyHasManyRelation;
use nullref\core\behaviors\ManyHasOneRelation;
use nullref\core\behaviors\OneHasManyRelation;
use nullref\core\behaviors\relations\HasManyRelation;
use nullref\core\behaviors\relations\HasOneRelation;
use nullref\core\behaviors\relations\HasRelation;
use yii\db\ActiveRecord;
use yii\gii\CodeFile;
use yii\gii\Generator as BaseGenerator;

/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */
class Generator extends BaseGenerator
{
    const CMD_TYPE_ADD_TABLE = 'add-table';
    const CMD_TYPE_ADD_FIELD = 'add-field';

    public $modelClass;
    public $modelAttribute;

    public $isManyToMany = false;

    public $migrationClass = 'yii\db\Migration';

    public function requiredTemplates()
    {
        return ['migration.php'];
    }

    public function generate()
    {
        /** @var ActiveRecord $model */
        $model = \Yii::createObject($this->modelClass);

        $commands = [];

        foreach ($model->behaviors as $key => $behavior) {
            if (($behavior instanceof  HasRelation) && ($behavior->attributeName == $this->modelAttribute)){
                $commands[] = [
                    'type'=>self::CMD_TYPE_ADD_FIELD,
                    'tableName'=>$model->tableName(),
                    'fieldName' => $behavior->selfField,
                ];
                $commands[] = [
                    'type'=>self::CMD_TYPE_ADD_FIELD,
                    'tableName'=>call_user_func([$behavior->foreignModel,'tableName']),
                    'fieldName' => $behavior->foreignField,
                ];
            }
        }
        $files = [];

        $name = 'm' . gmdate('ymd_Hi') . '00_models_relation';
        $code = $this->render('migration.php', [
            'isManyToMany' => $this->isManyToMany,
            'name' => $name,
            'commands' => $commands,
        ]);
        $files[] = new CodeFile(
            \Yii::getAlias('@app/migrations') . '/' . $name . '.php',
            $code
        );

        return $files;
    }

    /**
     * Checks if class is model
     * @param $attribute
     */
    public function validateModelClass($attribute)
    {
        /* @var $class ActiveRecord */
        $class = $this->{$attribute};
        try {
            $reflection = new \ReflectionClass($class);
            if (!$reflection->isSubclassOf(ActiveRecord::className())) {
                $this->addError($attribute, 'Class must be model');
            }
        } catch (\Exception $e) {
            $this->addError($attribute, $e->getMessage());
        }
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['modelClass'], 'validateModelClass'],
            [['modelClass', 'modelAttribute', 'migrationClass'], 'required'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Relation Migration Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates a migration';
    }
}