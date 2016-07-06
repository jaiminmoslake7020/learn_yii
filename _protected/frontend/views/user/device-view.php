<?php
/**
 * Created by PhpStorm.
 * User: ketul
 * Date: 06/07/2016
 * Time: 10:49 PM
 */


$form = \yii\bootstrap\ActiveForm::begin([
    'id'=>'device-form'
]);

?>

<div class="container">
    <div class="row">

        <?php echo $form->field($data,'device_name')->textInput(); ?>
        <?php echo $form->field($data,'created_at')->textInput(); ?>
        <?php echo $form->field($data,'updated_at')->textInput(); ?>

        <?php echo \yii\bootstrap\Html::submitButton('Submit', ['class'=>'btn btn-success']); ?>


    </div>
</div>

<?php

\yii\bootstrap\ActiveForm::end();

?>
