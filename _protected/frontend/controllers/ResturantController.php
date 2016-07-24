<?php
/**
 * Created by PhpStorm.
 * User: Himauli
 * Date: 15-Jul-16
 * Time: 7:51 PM
 */

namespace frontend\controllers;

use common\components\AppBasic;
use yii\web\Controller;
use Yii;
use frontend\models\Food;
use frontend\models\Menu;


class ResturantController extends Controller{

    public function actionHotel()
    {
        $model = new Food();
        $model2 = new Menu();

        if ($model->load(Yii::$app->request->post())  && $model2->load(Yii::$app->request->post()))
        {

            AppBasic::printRT($model->attributes, 'First Order');
            AppBasic::printRT($model2->attributes, 'Menu');
            AppBasic::printRT($_POST);

            //   1  &&  0  =>  0
            //$model2->author = 1 ;

            if ($model->save() )
            {

                $model2->item_name = $model->item_name;
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




        return $this->render('items', [
            'model' => $model,
            'model2' => $model2,
        ]);
    }

}