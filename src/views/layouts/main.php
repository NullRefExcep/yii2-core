<?php
use yii\helpers\Html;
use nullref\core\assets\AdminAsset;
use yii\widgets\Breadcrumbs;
use nullref\core\widgets\Alert;

/* @var $this \yii\web\View */
/* @var $content string */

AdminAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>

<div id="wrapper">

    <?= $this->render('header') ?>

    <div id="page-wrapper">

        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-12">
                    <?php if (isset($this->params['breadcrumbs'])) {
                        echo Breadcrumbs::widget([
                            'links' => $this->params['breadcrumbs'],

                            'homeLink' => [
                                'label' => Yii::t('admin', 'Dashboard'),
                                'url' => ['/admin/default/index'],
                            ],
                        ]);
                    } ?>
                </div>
            </div>
            <!-- /.row -->

            <?= Alert::widget() ?>

            <?= $content ?>

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
