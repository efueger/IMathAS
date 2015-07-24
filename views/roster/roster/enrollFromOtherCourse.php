
<?php
use app\components\AppUtility;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title = 'Enroll From Other Course';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-detail-header">
    <?php echo $this->render("../../itemHeader/_indexWithLeftContent",['link_title'=>['Home',$course->name,'Roster'], 'link_url' => [AppUtility::getHomeURL().'site/index',AppUtility::getHomeURL().'instructor/instructor/index?cid='.$course->id, AppUtility::getHomeURL().'/roster/roster/student-roster?cid='.$course->id], 'page_title' => $this->title]); ?>
</div>

<div class="item-detail-content">
    <?php echo $this->render("../../instructor/instructor/_toolbarTeacher", ['course' => $course, 'section' => 'roster']);?>
</div>

<div class="tab-content shadowBox"">
    <?php echo $this->render("_toolbarRoster", ['course' => $course]);?>

<div class="inner-content">
    <div class="title-middle center"><?php AppUtility::t('Enroll Student From Another Course');?></div>
    <?php $form =ActiveForm::begin(
        [
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-7 col-lg-offset-2\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-2 select-text-margin'],
            ],
        ]
    ) ?>
      <div>
          <h4><?php AppUtility::t('Select a course to choose students from');?>:</h4>
        <?php
             foreach($data as $value)
             {
                 echo "<tr><div class='radio student-enroll'><label class='checkbox-size'><td><input type='radio' name='name' value='{$value['id']}'><span class='cr'><i class='cr-icon fa fa-check'></i></span></label></td>"." " ."<td>{$value['name']}</td></div></tr>";
             }
        ?>
    </div>
    <div class="form-group">
        <div class="col-lg-11">
            <br>
            <?= Html::submitButton('Choose Students', ['class' => 'btn btn-primary','id' => 'change-button','name' => 'choose-button']) ?>
            <a class="btn btn-primary back-button" href="<?php echo AppUtility::getURLFromHome('roster/roster', 'student-roster?cid='.$course->id)  ?>"><?php AppUtility::t('Back');?></a>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
</div>
