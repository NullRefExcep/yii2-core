<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2016 NRE
 */


namespace nullref\core\interfaces;


interface ILanguage
{
    /**
     * Get key of language
     *
     * @return mixed
     */
    public function getId();

    /**
     * Get title of language
     * @return string
     */
    public function getTitle();

    /**
     * Slug of language
     * @return mixed
     */
    public function getSlug();
}