<?php

namespace nullref\core\components;

use yii\base\Component;
use yii\db\Connection;
use yii\di\Instance;

/**
 *
 */
class ModuleInstaller extends Component
{
    /**
     * @var Connection|array|string
     */
    protected $db = 'db';

    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::className());
        $this->db->getSchema()->refresh();
    }

    public function install()
    {
        //do some stuff
    }

    public function uninstall()
    {
        //do some stuff
    }
}



