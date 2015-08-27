<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\components\AppUtility;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\RegisterModel */

$this->title = 'Student Registration';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-detail-header">
    <?php echo $this->render("../itemHeader/_indexWithLeftContent", ['link_title' => [AppUtility::t('Home', false)], 'link_url' => [AppUtility::getHomeURL() . 'site/index']]); ?>
</div>
<div class = "title-container">
    <div class="row">
        <div class="pull-left page-heading">
            <div class="vertical-align title-page"><?php echo $this->title ?></div>
        </div>
    </div>
</div>
<div class="tab-content shadowBox non-nav-tab-item">
    <br>
<div class="student-signup-form">
    <div class="site-login">
        <div class="vcenter">
            <h3 class="student-signup-form-heading">Please fill out the following fields to SignUp</h3>
        </div>

        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'options' => ['class' => 'form-horizontal margin-left-fifteen'],
            'action' => '',
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-sm-3\">{input}</div>\n<div class=\"col-sm-6 clear-both col-sm-offset-2\">{error}</div>",
                'labelOptions' => ['class' => 'col-sm-2  text-align-left padding-top-seven'],
            ],
        ]); ?>
        <?= $form->field($model, 'username')->textInput(); ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'rePassword')->passwordInput() ?>
        <?= $form->field($model, 'firstName')->textInput(); ?>
        <?= $form->field($model, 'lastName') ?>
        <?= $form->field($model, 'email') ?>
        <?=
        $form->field($model, 'NotifyMeByEmailWhenIReceiveANewMessage',
            ['template' => "<div class=\"col-md-offset-2 col-md-9\">{input}</div>\n<div class=\"col-md-8\">{error}</div>",
            ])->checkboxList(['1' => 'Notify Me By Email When I Receive A New Message.']) ?>

       <div class="form-group margin-left-zero"> <span><?php echo "If you already know your course ID, you can enter it now. Otherwise, leave this blank and you can enroll later." ?></span></div>
        <br><br>
        <?= $form->field($model, 'courseID') ?>
        <?= $form->field($model, 'EnrollmentKey') ?>

        <div class="form-group">
             <div class="col-lg-offset-2 col-lg-9">
                <?= Html::submitButton('Sign Up', ['id' => 'sign-up-button','class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                &nbsp; &nbsp;<a class="btn btn-primary back-button" href="<?php echo AppUtility::getURLFromHome('site', 'login'); ?>">Back</a>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    </div>
</div>