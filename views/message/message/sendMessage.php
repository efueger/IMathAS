<?php
use yii\helpers\Html;
use app\components\AppUtility;
use yii\bootstrap\ActiveForm;
use app\components\AppConstant;
$this->title = 'New Message';
if ($userRights->rights > AppConstant::STUDENT_RIGHT){

    $this->params['breadcrumbs'][] = ['label' => $course->name, 'url' => ['/instructor/instructor/index?cid=' . $course->id]];
}
else{
    $this->params['breadcrumbs'][] = ['label' => $course->name, 'url' => ['/course/course/index?cid=' . $course->id]];
}

$this->params['breadcrumbs'][] = ['label' => 'Messages', 'url' => ['/message/message/index?cid='.$course->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php if ($userRights->rights > AppConstant::STUDENT_RIGHT) { ?>
    <?php echo $this->render('../../instructor/instructor/_toolbarTeacher',['course' => $course]); ?>
    <input type="hidden" class="send-msg" value="<?php echo $course->id ?>">
    <input type="hidden" class="send-userId" value="<?php echo $course->ownerid ?>">
<?php } else {?>

    <?php echo $this->render('../../course/course/_toolbar', ['course' => $course]);?>
    <input type="hidden" class="send-msg" value="<?php echo $course->id ?>">
    <input type="hidden" class="send-userId" value="<?php echo $course->ownerid ?>">
<?php } ?>

<?php $form = ActiveForm::begin([
    'id' => 'login-form',
    'options' => [],
    'action' => '',
    'fieldConfig' => [
        'template' => "",
        'labelOptions' => [],
    ],
]); ?>
<div class="">
    <h2><b>New Message</b></h2>
    <input type="hidden" class="send-msg" value="<?php echo $course->id ?>">
    <div class="drop-down">
        <span class="col-md-1"><b>To</b></span>
        <?php if($newTo){?>

        <span class="col-md-4"><strong><?php echo ucfirst($username->FirstName) . " " . ucfirst($username->LastName);?></strong>

       <?php }else
       {?>
            <span class="col-md-4">
            <select name="seluid" class="dropdown form-control" id="seluid">
            <option value="0">Select a recipient</option>
            <?php foreach ($users as $user) { ?>
            <option value="<?php echo $user['id'] ?>">
                <?php echo ucfirst($user['LastName']).", ".ucfirst($user['FirstName']); ?>
            </option><?php } ?>
        </select>
        <?php }?>
        </span>
        <label style="color: white" id="to">Please select atleast one user</label>
    </div>
    <br><br><br>

    <div>
        <span class="col-md-1"><b>Subject</b></span>
        <span class="col-md-4"><?php echo '<input class="textbox subject form-control" type="text" maxlength="100" >'; ?></span>
        <label style="color: white"  id="subjecttext">Subject cannot be blank</label>
    </div>
    <br><br><br>
    <div>
        <span class="col-md-1"><b>Message</b></span>
        <?php echo "<span class='left col-md-11'><div class= 'editor'>
        <textarea id='message' name='message' style='width: 100%;' rows='20' cols='200'>";
        echo "</textarea></div></span><br>"; ?>
    </div>

    <div class="col-lg-offset-1 col-md-8">
        <br>
        <a class="btn btn-primary" id="mess">Send Message</a>
    </div>
</div>
<?php ActiveForm::end(); ?>