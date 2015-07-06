<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\FileInput;
use app\components\AppUtility;
use app\components\AppConstant;
use kartik\date\DatePicker;
use kartik\time\TimePicker;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\changeUserInfoForm */
$this->title = 'Manage Offline Grades';
$this->params['breadcrumbs'][] = ['label' => $course->name, 'url' => ['/instructor/instructor/index?cid=' . $course->id]];
$this->params['breadcrumbs'][] = ['label' => 'Gradebook', 'url' => ['/gradebook/gradebook/gradebook?cid=' . $course->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<fieldset xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
    <legend>Manage Offline Grades</legend>
    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data'],
        'action' => 'manage-offline-grades?cid=' . $course->id,
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-7 col-lg-offset-2\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-3 select-text-margin'],
        ],
    ]); ?>
    <input type="hidden" id="course-id" value="<?php echo $course->id ?>">
    <?php

    $model->ShowGrade = AppUtility::getStringVal(AppConstant::NUMERIC_TWO);
    $model->Count = AppUtility::getStringVal(AppConstant::NUMERIC_ONE);
    $model->Gradetype = AppUtility::getStringVal(AppConstant::NUMERIC_ONE);

    ?>
    <div style="border: 1px solid #000000"><a href="#">Upload multiple offline grades</a></div>
    Check: <a name="check-all-box" class="check-all" href="#">All</a>/<a name="uncheck-all-box" class="uncheck-all"
                                                                       href="#">None</a>

    <table>
        <thead>
        <tr>
            <th></th>
            <th></th>

        </tr>
        </thead>
        <tbody class="grade-name-table-body">

        <?php
        foreach ($gradeNames as $singlegradeNames) { ?>
            <tr>
                <!--            --><?php ?>
                <td><input type='checkbox' id='Checkbox' name='grade-name-check[<?php echo $singlegradeNames['id']?>]'
                          value="<?php echo $singlegradeNames['id']?>" ></td>
                <td><?php echo $singlegradeNames['name'] ?></td>

            </tr>
        <?php } ?>
        <tbody>
    </table>
    With selected,<input type="button" class="btn btn-primary" id="mark-delete" value="Delete"> or make changes below
    <div style="border: 1px solid #000000">
        <h4><strong>Offline Grade Options</strong></h4>

<!--        <div>-->
            <div>

                   <span class="col-md-2">
                        <input type='checkbox' id='Checkbox' value="1" name='Show-after-check'>Show after:
                    </span>
                   <span class="col-md-10" id="always-replies-radio-list">

                    <input type="radio" name="Show-after" value="1">Always<br>

                    <input type="radio" name="Show-after" class="end pull-left " id="always" value="2">

                       <div class="col-md-3" id="datepicker-id">
                            <?php
                             echo DatePicker::widget([
                                 'name' => 'endDate',
                                 'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                 'value' => date("m/d/Y", strtotime("+1 week")),
                                 'pluginOptions' => [
                                     'autoclose' => true,
                                     'format' => 'mm/dd/yyyy']
                             ]);
                             echo '</div>'; ?>
                             <?php
                             echo '<label class="end pull-left  select-text-margin"> At</label>';
                            echo '<div class="pull-left col-lg-4">';

                             echo TimePicker::widget([
                                 'name' => 'startTime',
                                 'options' => ['placeholder' => 'Select operating time ...'],
                                 'convertFormat' => true,
                                 'value' => date('g:i A'),
                                 'pluginOptions' => [
                                     'format' => "m/d/Y g:i A",
                                     'todayHighlight' => true,
                                 ]
                             ]);

//                                         echo '</div>'; ?>
                        </div>


                    </span>
                </div>
            <div>
                            <span class="col-md-2">
                                <input type='checkbox' id='Checkbox' value="1" name='count-check'>Count:
                            </span>
                 <span class="col-md-10" id="always-replies-radio-list">

                            <input type="radio" name="count" value="1">Count in Gradebook<br>
                           <input type="radio" name="count" value="2">Don't count in grade total and hide from students<br>
                     <input type="radio" name="count" value="3">Don't count in grade total<br>
                        <input type="radio" name="count" value="4">Count as Extra Credit<br>
                   </span>
            </div>
             <div>
                 <span class="col-lg-2 select-text-margin pull-left">
                        <input type='checkbox' id='Checkbox' value="1" name='gradebook-category-check'>Gradebook category:
                 </span>

                <div class="col-lg-4">
                    <select name="rubric" class="form-control">
                        <option value="0" selected>default</option>
<!--                           --><?php //foreach ($rubricsData as $single) { ?>
<!--                        <option-->
<!--                                                                    value="--><?php //echo $single['id'] ?><!--">-->
<!--                        --><?php //echo $single['name']; ?><!--</option>-->
<!--                                                            --><?php //} ?>
                    </select>
                </div>

            </div>

            <div>
                 <span class="col-lg-2 select-text-margin pull-left">
                             <input type='checkbox' value="1" id='Checkbox' name='grade-name-check'>Tutor Access:
                 </span>
                <!--                            <label class="col-lg-3 select-text-margin pull-left">Use Scoring Rubric</label>-->
                <div class="col-lg-4">
                    <select name="tutor-access-value" class="form-control">
                        <option value="2" selected>No Access</option>
                        <option value="0" selected>View Scores</option>
                        <option value="1" selected>View and edit Scores</option>
                    </select>
                </div>


            </div>


<div class="form-group">
    <div class=" col-lg-8 display_field">
        <?= Html::submitButton('Save', ['class' => 'btn btn-primary col-lg-offset-3']) ?>
        <a class="btn btn-primary back-button-change-student-info"
           href="<?php echo AppUtility::getURLFromHome('roster/roster', 'student-roster?cid=' . $courseId) ?>">Back</a>
    </div>
</div>
<!--<br><br><br><br><br><br><br><br><br><br><br><br><br>-->
        <br>
    </div>
</fieldset>
    <?php ActiveForm::end(); ?>
