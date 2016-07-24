<?php
/**
 * Created by PhpStorm.
 * User: Himauli
 * Date: 07-Jul-16
 * Time: 9:38 PM
 */

namespace frontend\controllers;

use frontend\models\Bikes;
use frontend\models\Cars;
use frontend\models\Ford;
use frontend\models\Honda;
use frontend\models\Hundai;
use frontend\models\Mahindra;
use frontend\models\Maruti;
use frontend\models\Nissan;
use frontend\models\Renault;
use frontend\models\Tata;
use yii\web\Controller;

class VehicalController extends Controller
{
    public function actionNewBike()
    {
        $model = new Bikes();


        return $this->render(
            'bike',['data'=>$model]
        );
    }
    public function actionNewCar()
    {
        $model = new Cars();


        return $this->render(
            'car',['data'=>$model]
        );
    }
    public function actionFord()
    {
        $model = new Ford();


        return $this->render(
            'ford',['data'=>$model]
        );
    }
    public function actionHonda()
    {
        $model = new Honda();


        return $this->render(
            'honda',['data'=>$model]
        );
    }
    public function actionHundai()
    {
        $model = new Hundai();


        return $this->render(
            'hundai',['data'=>$model]
        );
    }
    public function actionMahindra()
    {
        $model = new Mahindra();


        return $this->render(
            'mahindra',['data'=>$model]
        );
    }
    public function actionMaruti()
    {
        $model = new Maruti();


        return $this->render(
            'maruti',['data'=>$model]
        );
    }
    public function actionNissan()
    {
        $model = new Nissan();


        return $this->render(
            'nissan',['data'=>$model]
        );
    }
    public function actionRenault()
    {
        $model = new Renault();


        return $this->render(
            'renault',['data'=>$model]
        );
    }
    public function actionTata()
    {
        $model = new Tata();


        return $this->render(
            'tata',['data'=>$model]
        );
    }


}