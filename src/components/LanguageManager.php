<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2016 NRE
 */


namespace nullref\core\components;


use nullref\core\interfaces\ILanguage;
use nullref\core\interfaces\ILanguageManager;
use nullref\core\objects\Language;
use Yii;
use yii\base\Component;
use yii\web\Cookie;

class LanguageManager extends Component implements ILanguageManager
{
    /**
     * @var array
     * e.g.:
     * `[
     *      ['id' => 1, 'slug' => 'en', 'title' => 'English'],
     *      ['id' => 2, 'slug' => 'fr', 'title' => 'French'],
     *  ]`
     */
    public $languages = [];

    /** @var string */
    public $languageSessionKey = '_language';

    /** @var string */
    public $languageCookieName = '_language';

    /** @var int */
    public $languageCookieDuration = 2592000;

    /** @var array */
    public $languageCookieOptions = [];

    /** @var  ILanguage */
    protected $_language;

    /**
     * Try to read language from session and cookies
     */
    public function init()
    {
        parent::init();

        $slug = Yii::$app->session->get($this->languageSessionKey);
        if ($slug === null) {
            $slug = Yii::$app->request->getCookies()->getValue($this->languageCookieName);
        }

        $languages = [];
        $selectedLanguage = null;
        foreach ($this->languages as $config) {
            if (is_array($config) && !isset($config['class'])) {
                $config['class'] = Language::className();
            }
            /** @var ILanguage $language */
            $language = \Yii::createObject($config);
            $languages [$language->getId()] = $language;
            if ($slug === $language->getSlug()) {
                $selectedLanguage = $language;
            }
        }
        $this->languages = $languages;

        if ($selectedLanguage === null) {
            $selectedLanguage = reset($languages);
        }
        $this->setLanguage($selectedLanguage);

    }

    /**
     * @return array
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * @return ILanguage
     */
    public function getLanguage()
    {
        return $this->_language;
    }

    /**
     * @param ILanguage $language
     */
    public function setLanguage(ILanguage $language)
    {
        $this->_language = $language;

        $slug = $language->getSlug();
        Yii::$app->session[$this->languageSessionKey] = $slug;

        if ($this->languageCookieDuration) {
            $cookie = new Cookie(array_merge(
                ['httpOnly' => true],
                $this->languageCookieOptions,
                [
                    'name' => $this->languageCookieName,
                    'value' => $slug,
                    'expire' => time() + (int)$this->languageCookieDuration,
                ]
            ));
            Yii::$app->getResponse()->getCookies()->add($cookie);
        }
    }
}