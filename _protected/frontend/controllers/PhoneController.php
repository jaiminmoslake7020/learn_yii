<?php
/**
 * Created by PhpStorm.
 * User: Himauli
 * Date: 15-Jul-16
 * Time: 6:39 PM
 */

namespace frontend\controllers;

use common\components\AppBasic;
use yii\web\Controller;
use Yii;
use frontend\models\Product;
use frontend\models\Company;

class PhoneController extends Controller{

    public function actionMobile()
    {
        $model = new Product();
        $model2 = new Company();

        if ($model->load(Yii::$app->request->post())  && $model2->load(Yii::$app->request->post()))
        {

            AppBasic::printRT($model->attributes, 'First Product');
            AppBasic::printRT($model2->attributes, 'First Company');
            AppBasic::printRT($_POST);

            //   1  &&  0  =>  0
            //$model2->author = 1 ;

            if ($model->save() )
            {

                $model2->product_id = $model->product_id;
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




        return $this->render('mobile', [
            'model' => $model,
            'model2' => $model2,
        ]);
    }


}