<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

namespace nullref\core\interfaces;


use nullref\core\components\EntityManager;

interface IEntityManageble
{
    /** @return EntityManager */
    public function getManager();
} 