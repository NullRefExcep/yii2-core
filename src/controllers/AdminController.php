<?php

namespace nullref\core\controllers;

use yii\base\Controller;

/**
 *
 */
class AdminController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}

