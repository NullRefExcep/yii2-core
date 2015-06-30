<?php
use nullref\core\widgets\Menu;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Url;

/** @var $this \yii\web\View */
?>

<!-- Navigation -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="<?= Url::to(['/admin']) ?>">SB Admin</a>
    </div>
    <!-- Top Menu Items -->
    <ul class="nav navbar-right top-nav">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i
                    class="fa fa-user"></i> <?= (\Yii::$app->admin->isGuest ? 'Guest' : \Yii::$app->admin->identity->getName()) ?>
                <b
                    class="caret"></b></a>
            <?= \yii\widgets\Menu::widget([
                'options' => ['class' => 'dropdown-menu'],
                'items' => [
                    Yii::$app->admin->isGuest ?
                        ['label' => 'Login', 'url' => [' / site / login']] :
                        ['label' => 'Logout(' . Yii::$app->admin->identity->email . ')',
                            'url' => ['/admin/default/logout'],
                            'linkOptions' => ['data - method' => 'post']],
                ]]) ?>
        </li>
    </ul>
    <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <?= Menu::widget([
            'items' => [
                ['label' => FA::icon('dashboard') . ' ' . Yii::t('admin', 'Dashboard'), 'url' => ['/admin/default']],
                ['label' => FA::icon('users') . ' ' . Yii::t('admin', 'Users'), 'items' => [
                    ['label' => FA::icon('user') . ' ' . Yii::t('admin', 'Clients'), 'url' => ['/admin/users/user']],
                    ['label' => FA::icon('user') . ' ' . Yii::t('admin', 'Admins'), 'url' => ['/admin/users/admin']],
                ]],
            ],
        ]) ?>
    </div>
    <!-- /.navbar-collapse -->
</nav>
