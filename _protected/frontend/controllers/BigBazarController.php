<?php
/**
 * Created by PhpStorm.
 * User: DELL PC
 * Date: 7/5/2016
 * Time: 9:08 PM
 */

namespace frontend\controllers;


use yii\web\Controller;

class BigBazarController extends Controller
{
    public function actionDMart()
    {
        return $this->render('shopping');
    }
}