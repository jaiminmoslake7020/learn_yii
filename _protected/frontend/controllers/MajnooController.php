<?php
/**
 * Created by PhpStorm.
 * User: DELL PC
 * Date: 7/5/2016
 * Time: 10:05 PM
 */

namespace frontend\controllers;


<<<<<<< Updated upstream
use yii\web\Controller;
=======
    /**
     * Displays the about static page.
     *
     * @return string
     */
    public function actionAbout()
    {
        //  /  frontend/views/majnoo/about.php
        //
        return $this->render('about');
    }
>>>>>>> Stashed changes

class MajnooController extends Controller
{
    public function actionBhai()
    {
        echo"Anil Kapoor";
        exit;
        return $this->render(welcomeback);
    }

}