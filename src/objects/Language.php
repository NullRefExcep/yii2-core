<?php

/**
 * @author    Dmytro Karpovych
 * @copyright 2016 NRE
 */

namespace nullref\core\objects;

use nullref\core\interfaces\ILanguage;

/**
 * Class Language
 * @package nullref\core\objects
 */
class Language implements ILanguage
{
    protected static $id_counter = 0;
    protected $_title;
    protected $_id;
    protected $_slug;

    /**
     * Language constructor.
     * @param $title
     * @param $slug
     */
    public function __construct($title, $slug)
    {
        $this->_id = self::$id_counter++;
        $this->_slug = $slug;
        $this->_title = $title;
    }

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
     * Get title of language
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Slug of language
     * @return mixed
     */
    public function getSlug()
    {
        return $this->_slug;
    }
}