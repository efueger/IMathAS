<?php
use app\components\AppUtility;
use kartik\date\DatePicker;
use kartik\time\TimePicker;

$this->title = 'Make Exception';
$this->params['breadcrumbs'][] = ['label' => $course->name, 'url' => ['/instructor/instructor/index?cid=' . $course->id]];
$this->params['breadcrumbs'][] = ['label' => 'List Students', 'url' => ['/roster/roster/student-roster?cid='.$course->id]];
$this->params['breadcrumbs'][] = $this->title;
echo $this->render('../../instructor/instructor/_toolbarTeacher');
?>
<div id="headermassexception" class="pagetitle"><h2>Manage Exceptions</h2></div>
<form action="make-exception" method="post" id="roster-form">
    <div class="student-roster-exception">
        <input type="hidden" name="isException" value="1"/>
        <input type="hidden" name="courseid" value="<?php echo $course->id ?>"/>
        <input type="hidden" name="studentInformation" value='<?php echo $studentDetails ?>'/>
        <div><div>
            <?php  if(sizeof((unserialize($studentDetails))) == 1){
            foreach (unserialize($studentDetails) as $studentDetail) {
                echo "<h3 class='name pull-left'>".ucfirst($studentDetail['LastName']).", ". ucfirst($studentDetail['FirstName']);
                if($section != "")
                echo "</h3><h4 class='pull-left margin-zero'>&nbsp;(section: ".$section.")</h4>";
                else{
                    echo " </h3>";
                }
            }
            }
            ?>
            </div>
            <br class="form">
            <div>
            <?php

            if($existingExceptions){
            echo"<h4>Existing Exceptions</h4><p>Select exceptions to clear</p>"
            ?>
            <?php
                foreach($existingExceptions as $entry){
                    echo "<ul><li>".$entry['Name']."<ul>";
                        foreach($entry['assessments'] as $singleAssessment){
//                                AppUtility::dump($singleAssessment['exceptionId']);
                            echo "<li><input type='checkbox' name='clears[]' value='{$singleAssessment['exceptionId']}'>".' '."{$singleAssessment['assessmentName']}".' ('."{$singleAssessment['exceptionDate']}".') '."</li>";
                        }
                    echo "</ul></li>";
            ?>
            <?php echo "</ul>"; } echo "<input type='submit'  class='btn btn-primary ' value='Record Changes'>";}
            else{
                echo"<p>No exceptions currently exist for the selected students.</p>";
            }
            ?>
            </div>
        </div>
        <div>
            <h4>Make New Exception</h4>
            <span class="form select-text-margin">Available After:</span>

              <div class="col-lg-3 pull-left" id="datepicker-id1" >
                  <?php
                  echo DatePicker::widget([
                      'name' => 'First_Date_Picker',
                      'type' => DatePicker::TYPE_COMPONENT_APPEND,
                      'value' => date("m-d-Y"),
                      'pluginOptions' => [
                          'autoclose' => true,
                          'format' => 'mm-dd-yyyy' ]
                  ]);
                  ?>
              </div><div class="end pull-left select-text-margin right">at</div>
            <div class="col-lg-4" id="timepicker-id" >
                <?php

                echo TimePicker::widget([
                    'name' => 'datetime_1',
                    'options' => ['placeholder' => 'Select operating time ...'],
                    'convertFormat' => true,
                    'value' => date('g:i A'),
                    'pluginOptions' => [
                        'format' => 'd-M-Y g:i A',
                        'todayHighlight' => true,
                    ]
                ]);
                ?>
            </div>
            <br class="form">

            <span class="form select-text-margin">Available Until:</span>

                 <div class="col-lg-3 pull-left" id="datepicker-id2" >
                         <?php
                         echo DatePicker::widget([
                             'name' => 'Second_Date_Picker',
                             'type' => DatePicker::TYPE_COMPONENT_APPEND,
                             'value' => date("m-d-Y"),
                             'pluginOptions' => [
                                 'autoclose' => true,
                                 'format' => 'mm-dd-yyyy' ]
                         ]);
                         ?>
                 </div><div class="end pull-left select-text-margin right">at</div>
            <div class="col-lg-4" id="timepicker-id1" >
                <?php
                echo TimePicker::widget([
                    'name' => 'datetime_2',
                    'options' => ['placeholder' => 'Select operating time ...'],
                    'convertFormat' => true,
                    'value' => date('g:i A','10:00 AM'),
                    'pluginOptions' => [
                        'format' => 'd-M-Y g:i A',
                        'todayHighlight' => true,
                    ]
                ]);
                ?>
            </div>
            </span>
            <br class="form">
            <p>Set Exception for assessments:</p>
            <ul>
                <?php foreach ($assessments as $assessment) { ?>
                <?php echo "<li><input type='checkbox' name='addexc[]' value='{$assessment->id}'>".' '. ucfirst($assessment->name)."</li>";?>
                <?php } ?>
            </ul>
            <input type="submit" class="btn btn-primary " id="change-record" value="Record Changes">
        </div>
        <br>
        <div>
            <p><input type="checkbox" name="forceregen">Force student to work on new versions of all questions? Students will keep any scores earned, but must work new versions of questions to improve score.</p>
            <p><input type="checkbox" name="forceclear">Clear student's attempts?  Students will <b>not</b>  keep any scores earned, and must rework all problems.</p>
            <p><input type="checkbox" name="eatlatepass">Deduct <input type="input" name="latepassn" size="1" value="1">  LatePass(es) from each student. These students all have 0 latepasses.</p>
            <p><input type="checkbox" name="waivereqscore"> Waive "show based on an another assessment" requirements, if applicable.</p>
            <p><input type="checkbox" name="sendmsg"> Send message to these students?</p>
        </div>
        <div>
            <span><p><h4>Students Selected:</h4></span><ul>
            <span class="col-md-12"><?php foreach (unserialize($studentDetails) as $studentDetail) { ?>
               <?php echo "<li>".ucfirst($studentDetail['LastName']).",". ucfirst($studentDetail['FirstName'])." (". ($studentDetail['SID']).")</li>" ?>
           <?php } ?></ul>
        </span>
        </div>

    </div>
</form>