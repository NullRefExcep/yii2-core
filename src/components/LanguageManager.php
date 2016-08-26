<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2016 NRE
 */


namespace nullref\core\components;


use nullref\core\interfaces\ILanguage;
use nullref\core\interfaces\ILanguageManager;
use nullref\core\objects\Language;
use yii\base\Component;

class LanguageManager extends Component implements ILanguageManager
{
    public $languages = [];

    protected $_currentLanguage;

    public function init()
    {
        parent::init();
        $languages = [];
        foreach ($this->languages as $config) {
            if (is_array($config) && !isset($config['class'])) {
                $config['class'] = Language::className();
            }
            $language = \Yii::createObject($config);
            $languages [$language->getId()] = $language;
        }
        $this->_currentLanguage = reset($languages);
        $this->languages = $languages;
    }

    public function getLanguages()
    {
        return $this->languages;
    }

    public function getCurrentLanguage()
    {
        return $this->_currentLanguage;
    }

    public function setCurrentLanguage($lang)
    {
        if ($lang instanceof ILanguage) {
            $this->_currentLanguage = $lang;
        }
    }
}