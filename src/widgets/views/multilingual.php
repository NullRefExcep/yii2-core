<?php
/**
 * @var $this \yii\web\View
 * @var $form \nullref\core\widgets\MultilingualForm
 * @var $model \yii\base\Model
 * @var $tab Closure
 */
use app\helpers\Languages;

?>
<style>
    .multilingual-tab-content {
        padding: 15px;
        margin-bottom: 5px;
        border: 1px solid #ddd;
        border-top: none;
        border-radius: 5px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
</style>
<div class="multilingual">
    <ul class="nav nav-tabs">
        <?php $first = true;
        foreach (Languages::getSlugMap() as $key => $lang): ?>
            <li class="<?= ($first) ? 'active' : '' ?>">
                <a data-toggle="tab" href="#<?= $lang ?>">
                    <?= Yii::t('translation', Languages::getMap()[$key]) ?>
                </a>
            </li>
            <?php $first = false; endforeach ?>
    </ul>
    <div class="tab-content multilingual-tab-content" style="padding: 10px 5px">
        <?php $first = true;
        foreach (Languages::getSlugMap() as $key => $lang): ?>
            <div id="<?= $lang ?>" class="tab-pane <?= ($first) ? 'active' : '' ?>">
                <?php $form->language = $lang ?>
                <?= call_user_func($tab, $form, $model) ?>
            </div>
            <?php $first = false; endforeach ?>
    </div>
</div>