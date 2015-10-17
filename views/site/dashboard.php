<?php
use app\components\AppUtility;
use app\components\AppConstant;
?>
    <title>IMathAS</title>
    <meta http-equiv="X-UA-Compatible" content="IE=7, IE=Edge"/>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <meta name="viewport">

<?php
$msgTotal = array_sum($newMsgCnt);
?>
<div class="tab-content shadowBox non-nav-tab-item">
<div class=mainbody>
    <div class="headerwrapper"></div>
    <div class="midwrapper">
        <div class="pagetitle col-lg-12" id="headerhome"><h2>Welcome to IMathAS, <?php echo AppUtility::getFullName($user->FirstName, $user->LastName); ?>
               <?php if ($myRights == AppConstant::ADMIN_RIGHT && count($brokenCnt) > 0) {
                echo '<span class="red">'.array_sum($brokenCnt).'</span> questions, '.(array_sum($brokencnt)-$brokencnt[0]).' public, reported broken systemwide';
                } ?>
        </div>

        <?php for ($i=0; $i<3; $i++) {
        if ($i==0) {
        echo '<div id="homefullwidth">';
            }
            if ($twoColumn) {
            if ($i == 1) {
            echo '<div id="leftcolumn">';
                } else if ($i==2) {
                echo '<div id="rightcolumn">';
                    }
                    }
                    for ($j=0; $j<count($pagelayout[$i]); $j++) {
                    switch ($pagelayout[$i][$j]) {
                    case 0:
                    if ($myRights > AppConstant::STUDENT_RIGHT) {
                        AppUtility::printCourses($page_teacherCourseData,_('Courses you\'re teaching'),'teach', $showNewMsgNote = null, $showNewPostNote = null, $stuHasHiddenCourses = null, $myRights = null, $newMsgCnt = null, $newPostCnt = null);
                    }
                    break;
                    case 1:
                        AppUtility::printCourses($page_tutorCourseData,_('Courses you\'re tutoring'),'tutor', $showNewMsgNote = null, $showNewPostNote = null, $stuHasHiddenCourses = null, $myRights = null, $newMsgCnt = null, $newPostCnt = null);
                    break;
                    case 2:
                        AppUtility::printCourses($page_studentCourseData,_('Courses you\'re taking'),'take', $showNewMsgNote = null, $showNewPostNote = null, $stuHasHiddenCourses = null, $myRights = null, $newMsgCnt = null, $newPostCnt = null);
                    break;
                    case 10:
                        AppUtility::printMessagesGadget($page_newmessagelist, $page_coursenames);
                    break;
                    case 11:
                        AppUtility::printPostsGadget($page_newpostlist, $page_coursenames, $postThreads);
                    break;
                    }
                    }
                    if ($i==2 || $twoColumn) {
                    echo '</div>';
                    }
                } ?>
        <div class="col-lg-12" id="homefullwidth">

        </div>
        <div class="clear"></div>
    </div>
</div>
</div>