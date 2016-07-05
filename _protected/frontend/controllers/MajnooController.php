<?php
/**
 * Created by PhpStorm.
 * User: DELL PC
 * Date: 7/5/2016
 * Time: 10:05 PM
 */

namespace frontend\controllers;


use yii\web\Controller;

class MajnooController extends Controller
{
    public function actionBhai()
    {
        echo"Anil Kapoor";
        exit;
        return $this->render(welcomeback);
    }

}