<?php
use app\components\AppUtility;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Enroll From Other Course';
//$this->params['breadcrumbs'][] = ['label' => '', 'url' => ['']]];
$this->params['breadcrumbs'][] = ['label' => 'List students', 'url' => ['/roster/roster/student-roster?cid='.$_GET['cid']]];
$this->params['breadcrumbs'][] = $this->title;
?>

<h2>Enroll Student From Another Course</h2>
<div class="site-login"><br>
<h4>Select a course to choose students from:</h4>

    <table class="radio-div">
        <thead>
        <tr>
            <th></th>
            <th></th>

        </tr>
        </thead>
        <tbody class="table-body"></tbody>
    </table>
    <br>
    <a class="btn btn-primary" id="choose-students">Choose Students</a>
    <div class="radio-div"></div>


</div>

<div>
<h4>Select students to enroll:</h4>
    <br>
    check: <a id="check-all-box" class="check-all" href="#">All</a> /
    <a id="uncheck-all-box" class="uncheck-all" href="#">None</a>

    <table class="check-div">
        <thead>
        <tr>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        </thead>
        <tbody class="check-table-body"></tbody>
    </table>

</div>



<script type="text/javascript">
    $(document).ready(function () {
        var cid = $(".send-msg").val();
        var userId = $(".send-userId").val();
        var allMessage = {cid: cid, userId: userId};
        jQuerySubmit('get-course-ajax',allMessage,'getCourseSuccess');
        chooseStudent();
        });
    var list;
    var studentList;
    function getCourseSuccess(response){
        var result= JSON.parse(response);
            if(result.status==0){
            list=result.query;
            showList(list);
          }
    }
    function showList(list)
    {
        var html=" ";
        var count=0;
        $.each(list,function(index,list){
            html+="<tr><td><input type='radio' name='radio' value='"+list+"'></td>";
            html+="<td>"+list+"</td>";


        });

        $('.table-body').append(html);

    }
    function chooseStudent()
    {
        $('#choose-students').click(function(){
            var markArray;
            $('.table-body input[name="radio"]:checked').each(function(){
                //markArray.push($(this).val());
                markArray=this.value;
                $(this).prop('checked',false);
            });
            var readvalue={checkedvalue: markArray};
           jQuerySubmit('get-student-ajax',readvalue,'getStudentSuccess');
    });
    }
    function getStudentSuccess(response){console.log(response);
        alert(response);
          var result=JSON.parse(response);

                     if(result.status==0){
                         var studentData = result.record;
                         $.each(studentData, function(index, student){
                             alert(JSON.stringify(student));
                             $.each(student, function(index, stud){
                                 alert(JSON.stringify(stud));
                                 alert('hii')

                             });
                         });
                  studentList=stud.data;
                showStudentList(studentList);
            }
        }

    </script>
