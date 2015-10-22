<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\module\Generator */
?>
<div class="migration-form">
    <?php

    echo $form->field($generator, 'modelClass');

    echo $form->field($generator, 'modelAttribute');

    echo $form->field($generator, 'migrationClass');

    ?>
</div>
