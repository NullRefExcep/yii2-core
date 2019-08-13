<?php

/**
 * @author    Dmytro Karpovych
 * @copyright 2016 NRE
 */

namespace nullref\core\objects;

use nullref\core\interfaces\ILanguage;
use yii\base\BaseObject;

/**
 * Class Language
 * @package nullref\core\objects
 */
class Language extends BaseObject implements ILanguage
{
    protected $_title;
    protected $_id;
    protected $_slug;

    /**
     * Get slug of language
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * Get title of language
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @param $title
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }

    /**
     * Slug of language
     * @return mixed
     */
    public function getSlug()
    {
        return $this->_slug;
    }

    /**
     * @param $slug
     */
    public function setSlug($slug)
    {
        $this->_slug = $slug;
    }
}
