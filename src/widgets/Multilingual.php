<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2016 NRE
 */
namespace nullref\core\widgets;

use nullref\core\interfaces\ILanguageManager;
use Yii;
use yii\base\Widget;

class Multilingual extends Widget
{
    public $tab;
    public $model;
    public $languages = null;

    /**
     * @var MultilingualForm
     */
    public $form;

    public function init()
    {
        parent::init();
        if (!count($this->languages)) {
            /** @var ILanguageManager $languageManager */
            $languageManager = Yii::$app->get('languageManager');
            $this->languages = $languageManager->getLanguages();
        }
    }


    public function run()
    {
        $this->view->registerJs(<<<JS
jQuery('form .nav.nav-tabs').find('a').each(function (index, item) {
    var link = jQuery(item);
    var errors = jQuery(link.attr('href')).find('.has-error');
    if (errors.length) {
        link.css('color', '#A94442');
        link.html('<i class="fa fa-exclamation-triangle"></i>' + link.text());
    }
});
JS
        );
        $form = new MultilingualForm();
        ob_get_clean();
        return $this->render('multilingual', [
            'form' => $form,
            'tab' => $this->tab,
            'model' => $this->model,
            'languages' => $this->languages,
        ]);
    }
}