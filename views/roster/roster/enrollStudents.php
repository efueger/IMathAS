<?php
use app\components\AppUtility;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = AppUtility::t('Enroll Student From Another Course', false);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-detail-header">
    <?php echo $this->render("../../itemHeader/_indexWithLeftContent", ['link_title' => ['Home', $course->name, AppUtility::t('Roster', false)], 'link_url' => [AppUtility::getHomeURL() . 'site/index', AppUtility::getHomeURL() . 'instructor/instructor/index?cid=' . $course->id, AppUtility::getHomeURL() . 'roster/roster/student-roster?cid=' . $course->id]]); ?>
</div>
<div class="title-container">
    <div class="row">
        <div class="pull-left page-heading">
            <div class="vertical-align title-page"><?php echo $this->title ?></div>
        </div>
    </div>
</div>
<div class="item-detail-content">
    <?php echo $this->render("../../instructor/instructor/_toolbarTeacher", ['course' => $course]); ?>
</div>
<div class="tab-content shadowBox"">
<?php echo $this->render("_toolbarRoster", ['course' => $course]); ?>
<div class="inner-content">
    <?php $form = ActiveForm::begin(
        [
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => "{label}\n<div class = \"col-md-3\">{input}</div>\n<div class = \"col-md-7 col-md-offset-2\">{error}</div>",
                'labelOptions' => ['class' => 'col-md-3'],
            ],
        ]
    ) ?>
    <div>
        <h4><?php echo AppUtility::t('Select students to enroll') ?> </h4>
        <?php echo AppUtility::t('Check') ?>: <a id="checkAll" class="check-all"
                                                 href="#"><?php echo AppUtility::t('All') ?></a> /
        <a id="checkNone" class="un-check-all" href="#"><?php echo AppUtility::t('None') ?></a>
        <br><br>

        <div id="list">
            <?php
            foreach ($data as $value) {
                echo "<tr><div class='checkbox student-enroll override-hidden'><label class='checkbox-size'><td>";
                if ($value['isCheck'] == 1)
                    echo "<input type='checkbox' name = 'student[" . $value['id'] . "]' value = '{$value['id']}' class = 'master' checked = 'true'><span class='cr'><i class='cr-icon fa fa-check'></i></span>";
                else
                    echo "<input type='checkbox' name = 'student[" . $value['id'] . "]' value = '{$value['id']}' class = 'master'><span class='cr'><i class='cr-icon fa fa-check'></i></span>";
                echo "</label></td>" . " " . "<td><span>{$value['lastName']}" . ", " . "{$value['firstName']}</span></td></div></tr>";
            }
            ?>
        </div>
        <br><br>
        <?php echo $form->field($model, 'section') ?>
    </div>
    <div class="form-group">
        <div class="col-md-offset-0 col-md-10 ">
            <br>
            <?php echo Html::submitButton(AppUtility::t('Enroll These Students', false), ['class' => 'btn btn-primary', 'id' => 'change-button', 'name' => 'enroll-students']) ?>
            <a class="btn btn-primary back-button"
               href="<?php echo AppUtility::getURLFromHome('roster/roster', 'enroll-from-other-course?cid=' . $cid) ?>"><?php echo AppUtility::t('Back ') ?></a>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
</div>

