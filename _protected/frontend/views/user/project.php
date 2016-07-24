<?php

$form = \yii\bootstrap\ActiveForm::begin([
    'id'=>'device-form'
]);

?>

<div class="container">
    <div class="row">

        <?php echo $form->field($model,'project_title')->textInput(); ?>
        <?php echo $form->field($model,'team_members')->textInput(); ?>

        <label>Members</label>
        <br/>
        <br/>
        <label>Member 1</label>
        <?php echo $form->field($modelMembers,'[0]member_name')->textInput(); ?>
        <?php echo $form->field($modelMembers,'[0]member_email')->textInput(); ?>

        <label>Member 2</label>
        <?php echo $form->field($modelMembers,'[1]member_name')->textInput(); ?>
        <?php echo $form->field($modelMembers,'[1]member_email')->textInput(); ?>

        <label>Member 3</label>
        <?php echo $form->field($modelMembers,'[2]member_name')->textInput(); ?>
        <?php echo $form->field($modelMembers,'[2]member_email')->textInput(); ?>


        <?php echo \yii\bootstrap\Html::submitButton('Submit', ['class'=>'btn btn-success']); ?>


    </div>
</div>



<?php

\yii\bootstrap\ActiveForm::end();

?>
