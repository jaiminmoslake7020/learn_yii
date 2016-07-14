<?php
/**
 * Created by PhpStorm.
 * User: ketul
 * Date: 05/07/2016
 * Time: 10:50 PM
 */

namespace frontend\controllers;


use common\models\Device;
use common\models\User;
use yii\web\Controller;
use Yii;
use frontend\models\Books;

class UserController extends Controller
{
    
    public function actionCreate()
    {
        $model = new \frontend\models\User();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                // form inputs are valid, do something here

                echo "Form successfullyy saved.";
                exit;
                
            }
            else
            {
               // print_r($model->getErrors());
               // exit;
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


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

        if($model->load(Yii::$app->request->isPost))
        {
            if($model->validate())
            {
                
            }
        }

        return $this->render(
            'device-view',['data'=>$model]
        );
    }

}