<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

namespace nullref\core\components\i18n;


use Yii;
use yii\helpers\ArrayHelper;
use yii\i18n\PhpMessageSource as BasePhpMessageSource;

class PhpMessageSource extends BasePhpMessageSource
{
    protected $_parentMessages = [];

    protected function loadMessages($category, $language)
    {
        $messages = parent::loadMessages($category, $language);

        if ((strpos($category, '*') > 0)) {
            return $messages;
        }
        $key = $category . '/' . $language;

        if (!isset($this->_parentMessages[$key])) {
            $parent = Yii::$app->getI18n()->getMessageSource($category . '*');
            $oldMessages = $parent->loadMessages($category, $language);
            $this->_parentMessages[$key] = array_merge($oldMessages, array_filter($messages));
        }
        return $this->_parentMessages[$key];
    }

}