<?php

use nullref\core\generators\migration\Generator as Generator;
/**
 * @var $cmd    mixed
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */
?>
<?php if ($cmd['type'] == Generator::CMD_TYPE_ADD_FIELD): ?>

        if (!$this->hasColumn('<?= $cmd['tableName'] ?>', '<?= $cmd['fieldName'] ?>')) {
            $this->addColumn('<?= $cmd['tableName'] ?>', '<?= $cmd['fieldName'] ?>', Schema::TYPE_INTEGER);
        }<?php endif ?>
<?php if ($cmd['type'] == Generator::CMD_TYPE_ADD_TABLE): ?>

        if (!$this->tableExist('<?= $cmd['tableName'] ?>')) {
            $this->createTable('<?= $cmd['tableName'] ?>', [
                '<?= $cmd['field1'] ?>' => Schema::TYPE_INTEGER,
                '<?= $cmd['field1'] ?>' => Schema::TYPE_INTEGER,
            ], $tableOptions);            $tableOptions = null;
            if (\Yii::$app->db->driverName === 'mysql') {
                $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
            }
        }<?php endif ?>


