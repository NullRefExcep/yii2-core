<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2016 NRE
 */
namespace nullref\core\widgets;

use nullref\core\interfaces\ILanguageManager;
use Yii;
use yii\base\Model;
use yii\base\Widget;

class Multilingual extends Widget
{
    /** @var \Closure */
    public $tab;
    /** @var Model */
    public $model;
    /** @var MultilingualForm */
    public $form;

    /** @var ILanguageManager */
    private $languageManager;

    public function init()
    {
        parent::init();
        $this->languageManager = Yii::$app->get('languageManager');
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
            'languageManager' => $this->languageManager,
        ]);
    }
}
