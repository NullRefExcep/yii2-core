<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2016 NRE
 */


namespace nullref\core\console;


use nullref\core\components\DotenvManger;
use Yii;
use yii\console\Controller;

class EnvController extends Controller
{
    public function actionIndex()
    {
        DotenvManger::modify();
    }
}