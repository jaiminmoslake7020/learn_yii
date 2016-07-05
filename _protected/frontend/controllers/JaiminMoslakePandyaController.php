<?php
/**
 * Created by PhpStorm.
 * User: DELL PC
 * Date: 7/5/2016
 * Time: 9:34 PM
 */
<<<<<<< Updated upstream:_protected/frontend/controllers/SalmanController.php
=======
class JaiminMoslakePandyaController extends Controller
{

    public function actionSearch()
    {
       $prod = "It should be printed in view";


       return $this->render('t1',

           [
              'dev'  => $prod,
               's1'  => 'abcd',
               'meh'  => 'efg',
               'neu'  => 'hij',
               'lila'  => 'klm',
               'sik'  => 'nop',
           ]

       );
    }
    #


    /**
     * Returns a list of behaviors that this component should behave as.
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
>>>>>>> Stashed changes:_protected/frontend/controllers/JaiminMoslakePandyaController.php

namespace frontend\controllers;


use yii\web\Controller;

class SalmanController extends Controller
{
    public function actionKatrina()
    {
        echo"Ranbir kapoor";
        exit;
        return $this->render('be4ara');
    }
}