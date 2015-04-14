<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\RegisterModel */

$this->title = 'New Use Registration';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to signUp:</p>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger">
            <?php echo Yii::$app->session->getFlash('error') ?>
        </div>
    <?php endif; ?>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => ['class' => 'form-horizontal'],
        'action' => '',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

    <?= $form->field($model, 'username')->textInput(); ?>

    <?= $form->field($model, 'password')->passwordInput() ?>

    <?= $form->field($model, 'rePassword')->passwordInput() ?>

    <?= $form->field($model, 'FirstName')->textInput(); ?>

    <?= $form->field($model, 'LastName') ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'NotifyMeByEmailWhenIReceiveANewMessage',
        ['template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
    ])->checkbox() ?>

    <?php echo "If you already know your course ID, you can enter it now. Otherwise, leave this blank and you can enroll later." ?>

    <?= $form->field($model, 'courseID') ?>

    <?= $form->field($model, 'EnrollmentKey') ?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('SignUp', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>