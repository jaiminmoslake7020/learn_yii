<?php
/**
 * Created by PhpStorm.
 * User: ketul
 * Date: 05/07/2016
 * Time: 10:50 PM
 */

namespace frontend\controllers;


use common\models\Device;
use yii\web\Controller;
use Yii;
use frontend\models\Books;

class UserController extends Controller
{

    public function actionIndex()
    {
        $model = new Books();

        return $this->render('pani-puri', [
            'model' => $model,
        ]);
    }


    public function actionAddDevice()
    {
        $model = new Device();


        return $this->render(
            'device-view',['data'=>$model]
        );
    }

}