<?php
/**
 * Created by PhpStorm.
 * User: DELL PC
 * Date: 7/5/2016
 * Time: 9:23 PM
 */

namespace frontend\controllers;


use yii\web\Controller;

class AnandVidhyaNagarController extends Controller
{
    public function actionRoad()
    {
        return $this->render('area');
    }
}