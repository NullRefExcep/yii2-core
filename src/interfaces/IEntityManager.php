<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

namespace nullref\core\interfaces;


interface IEntityManager
{
    public function createModel();

    public function createSearchModel();

    public function findOne($condition);

    public function findAll();

    public function getModelClass();
}