<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */

namespace nullref\core\interfaces;


use nullref\core\models\Model;

interface IEntityManager
{
    public function createModel();

    public function createSearchModel();

    /**
     * @param $condition
     * @return Model
     */
    public function findOne($condition);

    /**
     * @param $model Model
     * @return mixed
     */
    public function delete($model);

    public function findAll();

    public function getModelClass();
}