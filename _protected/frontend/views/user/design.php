<?php
/**
 * Created by PhpStorm.
 * User: Himauli
 * Date: 06-Jul-16
 * Time: 11:19 PM
 */

$form = \yii\bootstrap\ActiveForm::begin();

?>



<div class="container">
    <div class="row">

        <?php echo $form->field($model,'design_type')->textInput();?>
        <?php echo $form->field($model,'created_at')->textInput();?>
        <?php echo $form->field($model,'updated_at')->textInput();?>

        <?php echo \yii\bootstrap\Html::submitButton('Submit',['class'=>'btn btn-success'])?>

    </div>
</div>

<?php
    \yii\bootstrap\ActiveForm::end();
?>

