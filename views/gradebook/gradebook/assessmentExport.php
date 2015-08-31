<?php
if (!$isTeacher) {
    echo "This page not available to students";
}else{

function getpts($sc) {
    if (strpos($sc,'~')===false) {
        if ($sc>0) {
            return $sc;
        } else {
            return 0;
        }
    } else {
        $sc = explode('~',$sc);
        $tot = 0;
        foreach ($sc as $s) {
            if ($s>0) {
                $tot+=$s;
            }
        }
        return round($tot,1);
    }
}

function evalqsandbox($seed,$qqqcontrol,$qqqanswer) {
    $sa = '';

    srand($seed);
    eval($qqqcontrol);
    srand($seed+1);
    eval($qqqanswer);

    if (isset($anstypes) && !is_array($anstypes)) {
        $anstypes = explode(",",$anstypes);
    }
    if (isset($anstypes)) { //is multipart
        if (isset($showanswer) && !is_array($showanswer)) {
            $sa = $showanswer;
        } else {
            $sapts =array();
            for ($i=0; $i<count($anstypes); $i++) {
                if (isset($showanswer[$i])) {
                    $sapts[] = $showanswer[$i];
                } else if (isset($answer[$i])) {
                    $sapts[] = $answer[$i];
                } else if (isset($answers[$i])) {
                    $sapts[] = $answers[$i];
                }
            }
            $sa = implode('&',$sapts);
        }
    } else {
        if (isset($showanswer)) {
            $sa = $showanswer;
        } else if (isset($answer)) {
            $sa = $answer;
        } else if (isset($answers)) {
            $sa = $answers;
        }
    }
    return $sa;
}

   //ask for options
    $pagetitle = "Assessment Export";

//    echo "<div class=breadcrumb>$breadcrumbbase <a href=\"course.php?cid=$cid\">$coursename</a> ";
//    echo "&gt; <a href=\"gradebook.php?stu=0&cid=$cid\">Gradebook</a> &gt; <a href=\"gb-itemanalysis.php?aid=$aid&cid=$cid\">Item Analysis</a> ";
//    echo '&gt; Assessment Export</div>';
    echo '<div id="headergb-aidexport" class="pagetitle"><h2>Assessment Results Export</h2></div>';
?>
     <form method="post" action="assessment-export?aid=<?php echo $assessmentId ?>&cid=<?php echo $course->id?>">
  <?php  echo 'What do you want to include in the export:<br/>';
    echo '<input type="checkbox" name="pts" value="1"/> Points earned<br/>';
    echo '<input type="checkbox" name="ptpts" value="1"/> Multipart broken-down Points earned<br/>';
    echo '<input type="checkbox" name="ba" value="1"/> Scored Attempt<br/>';
    echo '<input type="checkbox" name="bca" value="1"/> Correct Answers for Scored Attempt<br/>';
    echo '<input type="checkbox" name="la" value="1"/> Last Attempt<br/>';
    echo '<input type="submit" name="options" value="Export" />';
    echo '<p>Export will be a commas separated values (.CSV) file, which can be opened in Excel</p>';
    //echo '<p class="red"><b>Note</b>: Attempt information from shuffled multiple choice, multiple answer, and matching questions will NOT be correct</p>';
    echo '</form>';




}
?>