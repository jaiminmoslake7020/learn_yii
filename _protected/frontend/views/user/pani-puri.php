<?php
/**
 * Created by PhpStorm.
 * User: ketul
 * Date: 05/07/2016
 * Time: 10:52 PM
 */

use yii\bootstrap\ActiveForm;



?>

<div class="container">
    <div class="row">
        <div class="col-md-12">

            <?php
                // <form id="books-form">
                $form = ActiveForm::begin([
                    'id'=>'books-form'
                ]);
            ?>

            <?php
              echo  $form->field($model,'name');
            ?>

            <?php
              echo  $form->field($model,'author');
            ?>

            <?php
              echo  $form->field($model,'description');
            ?>

            <?php
            echo  $form->field($model,'created_at');
            ?>

            <?php
            echo  $form->field($model,'updated_at');
            ?>

            <?php
            echo  \yii\bootstrap\Html::submitButton('Create Book',[
                'class'=>'btn btn-success'
            ]);
            ?>

            <?php
                ActiveForm::end();
            ?>

        </div>
    </div>
</div>




