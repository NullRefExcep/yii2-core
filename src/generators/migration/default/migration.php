<?php
/**
 *
 * @var $isManyToMany boolean
 * @var $tableName string
 * @var $name string
 * @var $modelKey1 string
 * @var $modelKey2 string
 *
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

echo "<?php\n";
?>

use yii\db\Migration;
use yii\db\Schema;

class <?= $name ?> extends Migration
{
    use \nullref\core\traits\MigrationTrait;

    protected $tableName = '<?= $tableName ?>';

    public function up()
    {<?php if($isManyToMany): ?>

        if (!$this->tableExist($this->tableName)) {
            $this->createTable($this->tableName, [
                '<?= $modelKey1 ?>' => Schema::TYPE_INTEGER,
                '<?= $modelKey2 ?>' => Schema::TYPE_INTEGER,
            ], $tableOptions);            $tableOptions = null;
            if (\Yii::$app->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
            }
        }

        <?php else: ?>

        if (!$this->hasColumn($this->tableName, '<?= $modelKey1 ?>')) {
            $this->addColumn($this->tableName, '<?= $modelKey1 ?>', Schema::TYPE_INTEGER);
        }<?php endif ?>

    }

    public function down()
    {<?php if($isManyToMany): ?>

        if ($this->tableExist($this->tableName)) {
            $this->dropTable($this->tableName);
        }
        <?php else: ?>

        if ($this->hasColumn($this->tableName, '<?= $modelKey1 ?>')) {
            $this->dropColumn($this->tableName, '<?= $modelKey1 ?>');
        }<?php endif ?>

        return true;
    }

}


