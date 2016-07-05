<?php
/**
 * Created by PhpStorm.
 * User: DELL PC
 * Date: 7/5/2016
 * Time: 9:16 PM
 */

namespace frontend\controllers;


use yii\web\Controller;

class HeartKillerController extends Controller
{
    public function actionVrundavan()
    {
        return $this->render('garba');
    }
}