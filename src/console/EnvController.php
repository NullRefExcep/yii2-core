<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2016 NRE
 */


namespace nullref\core\console;


use nullref\core\components\DotenvManger;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class EnvController extends Controller
{
    public function actionIndex()
    {
        DotenvManger::modify();

        $data = DotenvManger::load(Yii::getAlias('@app/..'));

        /**
         * Ask for db creating if mysql
         */
        if ($this->getEnvVar($data, 'DB_DRIVER') === 'mysql') {
            if (Console::confirm('Create DB?')) {
                $cmd = 'mysql -h ' . $this->getEnvVar($data, 'DB_HOST') .
                    ' -u ' . $this->getEnvVar($data, 'DB_USER') .
                    ' -p' . $this->getEnvVar($data, 'DB_PASS') .
                    ' -e \'CREATE SCHEMA IF NOT EXISTS ' . $this->getEnvVar($data, 'DB_NAME')
                    . ' DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;\'';
                Console::output(system($cmd));
            }
        }
    }

    /**
     * Get env var value by the key
     * @param $data
     * @param $key
     * @return null
     */
    protected function getEnvVar($data, $key)
    {
        foreach ($data as $datum) {
            if ($datum[0] == $key) {
                return $datum[1];
            }
        }
        return null;
    }
}