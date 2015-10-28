<?php
/**
 *
 * @var $commands array
 * @var $name string
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

    public function up()
    {
        <?php foreach ($commands as $cmd): ?>
            <?php include '_up-cmd.php' ?>
        <?php endforeach ?>
    }

    public function down()
    {
        <?php foreach ($commands as $cmd): ?>
            <?php include '_down-cmd.php' ?>
        <?php endforeach ?>

        return true;
    }

}


