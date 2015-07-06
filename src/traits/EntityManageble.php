<?php

namespace nullref\core\traits;

use nullref\core\interfaces\IEntityManager;

/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 *
 * @property $entityModuleId string
 * @property $entityManagerName string
 *
 */
trait EntityManageble
{
    /** @var  IEntityManager */
    protected $_manager;

    /** @return IEntityManager */
    public function getManager()
    {
        if (!isset($this->_manager)) {
            $this->_manager = \Yii::$app->getModule($this->entityModuleId)->get($this->entityManagerName);
        }
        return $this->_manager;
    }
} 