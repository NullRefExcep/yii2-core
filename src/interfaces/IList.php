<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

namespace nullref\core\interfaces;


interface IList
{
    /**
     * @return array
     */
    public function getList();

    /**
     * @param $key
     * @return mixed
     */
    public function getValue($key);
}