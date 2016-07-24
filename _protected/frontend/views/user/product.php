<?php
/**
 * Created by PhpStorm.
 * User: Himauli
 * Date: 06-Jul-16
 * Time: 11:07 PM
 */

$form = \yii\widgets\ActiveForm::begin([
   'id'=>'product-id'
]);

?>

<div class="container">
    <div class="row">

        <?php echo $form->field($model,'name')->textInput(); ?>
        <?php echo $form->field($model,'created_at')->textInput(); ?>
        <?php echo $form->field($model,'updated_at')->textInput(); ?>

        <?php echo \yii\bootstrap\Html::submitButton('Submit', ['class'=>'btn btn-success']); ?>

    </div>
</div>

<?php
    \yii\widgets\ActiveForm::end();
?>