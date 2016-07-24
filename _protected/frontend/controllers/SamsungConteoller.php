<?php
/**
 * Created by PhpStorm.
 * User: Himauli
 * Date: 06-Jul-16
 * Time: 9:33 PM
 */

namespace frontend\controllers;


use frontend\models\Article;
use yii\base\Controller;

class SamsungConteoller extends Controller
{
    public function actionAndroid()
    {
        $model = new Article();

        return $this->render('android', [
            'model' => $model,
        ]);
    }
}