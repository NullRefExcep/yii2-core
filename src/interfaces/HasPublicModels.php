<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

namespace nullref\core\interfaces;


interface HasPublicModels
{
    /**
     * @return string[]
     */
    function getPublicModels();
}