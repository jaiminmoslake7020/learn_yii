<?php
/**
 * Created by PhpStorm.
 * User: ketul
 * Date: 05/07/2016
 * Time: 10:50 PM
 */

namespace frontend\controllers;


use common\components\AppBasic;
use common\models\Device;
use common\models\User;
use yii\base\Application;
use yii\web\Controller;
use Yii;
use frontend\models\Books;

class UserController extends Controller
{

    public function actionCreate()
    {
        $model = new \frontend\models\User();
        $model2 = new Books();
        
        if ($model->load(Yii::$app->request->post())  && $model2->load(Yii::$app->request->post()))
        {

            AppBasic::printRT($model->attributes, 'SECOND User');
            AppBasic::printRT($model2->attributes, 'SECOND Books');
            AppBasic::printRT($_POST);

            //   1  &&  0  =>  0
            //$model2->author = 1 ;
            
            if ($model->save() )
            {

                $model2->author = $model->id;
                if($model2->save())
                {

                    echo "Form successfullyy saved.";
                    exit;

                }
                // form inputs are valid, do something here

            }
            else
            {
               // print_r($model->getErrors());
               // exit;
            }
        }




        return $this->render('create', [
            'model' => $model,
            'model2' => $model2,
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