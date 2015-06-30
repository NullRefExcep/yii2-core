<?php
/** @var $this \yii\web\View */
/** @var $dependencies array */
$this->title = 'Composer manager';
?>
<div class="admin-index">

    <table class="table table-bordered table-striped table-responsive">
        <tr>
            <th>Name</th>
            <th>Version</th>
        </tr>
        <?php foreach ($dependencies as $item): ?>
            <tr>
                <td><?= $item['name'] ?></td>
                <td><?= $item['version'] ?></td>
            </tr>
        <?php endforeach ?>

    </table>
</div>