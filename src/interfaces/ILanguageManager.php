<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2016 NRE
 */


namespace nullref\core\interfaces;


interface ILanguageManager
{
    const LANG_MODE_SINGLE = 0;
    const LANG_MODE_TRANSLATION = 1;
    const LANG_MODE_MONOLINGUAL = 2;

    /**
     * @return ILanguage[]
     */
    public function getLanguages();

    /**
     * @return ILanguage
     */
    public function getCurrentLanguage();

    /**
     * @param $lang ILanguage
     * @return bool
     */
    public function setCurrentLanguage($lang);
}