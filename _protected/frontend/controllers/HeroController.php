<?php
/**
 * Created by PhpStorm.
 * User: DELL PC
 * Date: 7/5/2016
 * Time: 9:28 PM
 */

namespace frontend\controllers;


use yii\web\Controller;

class HeroController extends Controller
{
    public function actionHero()
    {
        echo"Main tera hero...";
        exit;
        return $this->render('herooo');
    }

}