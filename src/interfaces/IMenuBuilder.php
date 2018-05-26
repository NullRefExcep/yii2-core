<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2018 NRE
 */

namespace nullref\core\interfaces;


interface IMenuBuilder
{
    /**
     * @param array $items
     * @return array
     */
    public function build($items);
} 