<?php

use nullref\core\generators\migration\Generator as Generator;

/**
 * @var $cmd    mixed
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */
?>
<?php if ($cmd['type'] == Generator::CMD_TYPE_ADD_FIELD): ?>

        if ($this->hasColumn('<?= $cmd['tableName'] ?>', '<?= $cmd['fieldName'] ?>')) {
            $this->dropColumn('<?= $cmd['tableName'] ?>', '<?= $cmd['fieldName'] ?>');
        }
<?php endif ?>
<?php if ($cmd['type'] == Generator::CMD_TYPE_ADD_TABLE): ?>

        if ($this->tableExist('<?= $cmd['tableName'] ?>')) {
            $this->dropTable('<?= $cmd['tableName'] ?>');
        }
<?php endif ?>
