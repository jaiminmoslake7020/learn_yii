<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\User */
/* @var $form ActiveForm */
?>
<div class="user-create">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->errorSummary($model); ?>

        <?= $form->field($model, 'username') ?>
        <?= $form->field($model, 'email') ?>
        <?= $form->field($model, 'birth_date') ?>
        <?= $form->field($model, 'password')->passwordInput(); ?>
        <?= $form->field($model, 'confirm_password')->passwordInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- user-create -->
