<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */
namespace nullref\core\interfaces;

use yii\rbac\BaseManager;

interface IRoleContainer
{
    /**
     * @param BaseManager $authManger
     * @return \yii\rbac\Role[]
     */
    public function getRoles(BaseManager $authManger);

    /**
     * @return array
     */
    public function getTitles();
}