<?php
/**
 * Created by PhpStorm.
 * User: DELL PC
 * Date: 7/5/2016
 * Time: 10:02 PM
 */

namespace frontend\controllers;


use yii\web\Controller;

class SaifController extends Controller
{
    public function actionKarina()
    {
        echo"Sahid kapoor";
        exit;
        return $this->render('LoveTriangle');
    }

}