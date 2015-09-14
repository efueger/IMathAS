<?php
use app\components\AppUtility;
use app\components\AssessmentUtility;
if (!$isTeacher || !$isTutor) {
    echo "Go away";
}

//DISPLAY
$placeinhead = '';

$placeinhead .= "<script type=\"text/javascript\">";
$placeinhead .= 'function chgtimefilter() { ';
$placeinhead .= '       var tm = document.getElementById("timetoggle").value; ';
$address = $urlmode . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/gb-testing.php?stu=$stu&cid=$cid";

$placeinhead .= "       var toopen = '$address&timefilter=' + tm;\n";
$placeinhead .= "  	window.location = toopen; \n";
$placeinhead .= "}\n";

$placeinhead .= 'function chglnfilter() { ';
$placeinhead .= '       var ln = document.getElementById("lnfilter").value; ';
$address = $urlmode . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/gb-testing.php?stu=$stu&cid=$cid";

$placeinhead .= "       var toopen = '$address&lnfilter=' + ln;\n";
$placeinhead .= "  	window.location = toopen; \n";
$placeinhead .= "}\n";

$placeinhead .= 'function chgsecfilter() { ';
$placeinhead .= '       var sec = document.getElementById("secfiltersel").value; ';
$address = $urlmode . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/gb-testing.php?stu=$stu&cid=$cid";

$placeinhead .= "       var toopen = '$address&secfilter=' + sec;\n";
$placeinhead .= "  	window.location = toopen; \n";
$placeinhead .= "}\n";
$placeinhead .= "</script>";



//show instructor view
$placeinhead .= "<script type=\"text/javascript\">function lockcol() { \n";
$placeinhead .= " var cont = document.getElementById(\"tbl-container\");\n";
$placeinhead .= " if (cont.style.overflow == \"auto\") {\n";
$placeinhead .= "   cont.style.height = \"auto\"; cont.style.overflow = \"visible\"; cont.style.border = \"0px\";";
//$placeinhead .= "document.getElementById(\"myTable\").className = \"gb\"; document.cookie = 'gblhdr-$cid=0';";
$placeinhead .= "  document.getElementById(\"lockbtn\").value = \"Lock headers\"; } else {";
$placeinhead .= " cont.style.height = \"75%\"; cont.style.overflow = \"auto\"; cont.style.border = \"1px solid #000\";\n";
//$placeinhead .= "document.getElementById(\"myTable\").className = \"gbl\"; document.cookie = 'gblhdr-$cid=1'; ";
$placeinhead .= "  document.getElementById(\"lockbtn\").value = \"Unlock headers\"; }";
$placeinhead .= "} ";

$placeinhead .= "</script>\n";
$placeinhead .= "<style type=\"text/css\"> table.gb { margin: 0px; } </style>";


//echo "<div class=breadcrumb>$breadcrumbbase <a href=\"course.php?cid={$_GET['cid']}\">$coursename</a> ";
echo "&gt; Diagnostic Gradebook</div>";
echo "<form method=post action=\"gradebook.php?cid=$cid\">";

echo '<div id="headergb-testing" class="pagetitle"><h2>Diagnostic Grade Book</h2></div>'; ?>
 <a href="<?php echo AppUtility::getURLFromHome('gradebook','gradebook/gradebook'.$course->id);?>">View regular gradebook</a>
<?php echo "<div class=cpmid>";

echo "Students starting in: <select id=\"timetoggle\" onchange=\"chgtimefilter()\">";
echo "<option value=1 "; AssessmentUtility::writeHtmlSelected($timefilter,1); echo ">last 1 hour</option>";
echo "<option value=2 "; AssessmentUtility::writeHtmlSelected($timefilter,2); echo ">last 2 hours</option>";
echo "<option value=4 "; AssessmentUtility::writeHtmlSelected($timefilter,4); echo ">last 4 hours</option>";
echo "<option value=24 "; AssessmentUtility::writeHtmlSelected($timefilter,24); echo ">last day</option>";
echo "<option value=168 "; AssessmentUtility::writeHtmlSelected($timefilter,168); echo ">last week</option>";
echo "<option value=720 "; AssessmentUtility::writeHtmlSelected($timefilter,720); echo ">last month</option>";
echo "<option value=8760 "; AssessmentUtility::writeHtmlSelected($timefilter,8760); echo ">last year</option>";
echo "</select>";
echo " Last name: <input type=text id=\"lnfilter\" value=\"$lnfilter\" />";
echo "<input type=button value=\"Filter by name\" onclick=\"chglnfilter()\" />";
echo "</div>";
AppUtility::dump($gradebookData);
$gbt = gbinstrdisp();
echo "</form>";
echo "</div>";
//echo "Meanings:  IP-In Progress, OT-overtime, PT-practice test, EC-extra credit, NC-no credit<br/><sup>*</sup> Has feedback, <sub>d</sub> Dropped score\n";
echo "Meanings:   NC-no credit";
/*if ($isteacher) {
	echo "<div class=cp>";
	echo "<a href=\"addgrades.php?cid=$cid&gbitem=new&grades=all\">Add Offline Grade</a><br/>";
	echo "<a href=\"gradebook.php?stu=$stu&cid=$cid&export=true\">Export Gradebook</a><br/>";
	echo "Email gradebook to <a href=\"gradebook.php?stu=$stu&cid=$cid&emailgb=me\">Me</a> or <a href=\"gradebook.php?stu=$stu&cid=$cid&emailgb=ask\">to another address</a><br/>";
	echo "<a href=\"gbsettings.php?cid=$cid\">Gradebook Settings</a>";
	echo "<div class=clear></div></div>";
}
*/



function gbinstrdisp() {
    global $isteacher,$istutor,$cid,$stu,$isdiag,$catfilter,$secfilter,$imasroot,$tutorsection;
    $hidenc = 1;
    $gbt = gbtable();
    //print_r($gbt);
    echo "<script type=\"text/javascript\" src=\"$imasroot/javascript/tablesorter.js\"></script>\n";
    echo "<div id=\"tbl-container\">";
    echo "<table class=gb id=myTable><thead><tr>";
    $n=0;


    for ($i=0;$i<count($gbt[0][0]);$i++) { //biographical headers
        if ($i==1 && $gbt[0][0][1]!='ID') { continue;}
        echo '<th>'.$gbt[0][0][$i];
        if (($gbt[0][0][$i]=='Section' || ($isdiag && $i==4)) && (!$istutor || $tutorsection=='')) {
            echo "<br/><select id=\"secfiltersel\" onchange=\"chgsecfilter()\"><option value=\"-1\" ";
            if ($secfilter==-1) {echo  'selected=1';}
            echo  '>All</option>';
            $query = "SELECT DISTINCT section FROM imas_students WHERE courseid='$cid' ORDER BY section";
            $result = mysql_query($query) or die("Query failed : " . mysql_error());
            while ($row = mysql_fetch_row($result)) {
                if ($row[0]=='') { continue;}
                echo  "<option value=\"{$row[0]}\" ";
                if ($row[0]==$secfilter) {
                    echo  'selected=1';
                }
                echo  ">{$row[0]}</option>";
            }
            echo  "</select>";

        } else if ($gbt[0][0][$i]=='Name') {
            echo '<br/><span class="small">N='.(count($gbt)-2).'</span>';
        }
        echo '</th>';

        $n++;
    }


    for ($i=0;$i<count($gbt[0][1]);$i++) { //assessment headers
        if (!$isteacher && $gbt[0][1][$i][4]==0) { //skip if hidden
            continue;
        }
        if ($hidenc==1 && $gbt[0][1][$i][4]==0) { //skip NC
            continue;
        } else if ($hidenc==2 && ($gbt[0][1][$i][4]==0 || $gbt[0][1][$i][4]==3)) {//skip all NC
            continue;
        }


        //name and points
        echo '<th class="cat'.$gbt[0][1][$i][1].'">'.$gbt[0][1][$i][0].'<br/>';
        if ($gbt[0][1][$i][4]==0 || $gbt[0][1][$i][4]==3) {
            echo $gbt[0][1][$i][2].' (Not Counted)';
        } else {
            echo $gbt[0][1][$i][2].'&nbsp;pts';
            if ($gbt[0][1][$i][4]==2) {
                echo ' (EC)';
            }
        }
        if ($gbt[0][1][$i][5]==1) {
            echo ' (PT)';
        }
        //links
        if ($isteacher) {
            if ($gbt[0][1][$i][6]==0) { //online
                echo "<br/><a class=small href=\"addassessment.php?id={$gbt[0][1][$i][7]}&cid=$cid&from=gb\">[Settings]</a>";
                echo "<br/><a class=small href=\"isolateassessgrade.php?cid=$cid&aid={$gbt[0][1][$i][7]}\">[Isolate]</a>";
            } else if ($gbt[0][1][$i][6]==1) { //offline
                echo "<br/><a class=small href=\"addgrades.php?stu=$stu&cid=$cid&grades=all&gbitem={$gbt[0][1][$i][7]}\">[Settings]</a>";
                echo "<br/><a class=small href=\"addgrades.php?stu=$stu&cid=$cid&grades=all&gbitem={$gbt[0][1][$i][7]}&isolate=true\">[Isolate]</a>";
            } else if ($gbt[0][1][$i][6]==2) { //discussion
                echo "<br/><a class=small href=\"addforum.php?id={$gbt[0][1][$i][7]}&cid=$cid&from=gb\">[Settings]</a>";
            }
        }

        echo '</th>';
        $n++;
    }

    echo '</tr></thead><tbody>';
    //create student rows
    for ($i=1;$i<count($gbt)-1;$i++) {
        if ($i%2!=0) {
            echo "<tr class=even onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='even'\">";
        } else {
            echo "<tr class=odd onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='odd'\">";
        }
        echo '<td class="locked" scope="row">';

        echo "<a href=\"gradebook.php?cid=$cid&stu={$gbt[$i][4][0]}\">";
        echo $gbt[$i][0][0];
        echo '</a></td>';

        for ($j=($gbt[0][0][1]=='ID'?1:2);$j<count($gbt[0][0]);$j++) {
            echo '<td class="c">'.$gbt[$i][0][$j].'</td>';
        }

        //assessment values

        for ($j=0;$j<count($gbt[0][1]);$j++) {
            if ($gbt[0][1][$j][4]==0) { //skip if hidden
                continue;
            }
            if ($hidenc==1 && $gbt[0][1][$j][4]==0) { //skip NC
                continue;
            } else if ($hidenc==2 && ($gbt[0][1][$j][4]==0 || $gbt[0][1][$j][4]==3)) {//skip all NC
                continue;
            }


            echo '<td class="c">';
            if (isset($gbt[$i][1][$j][5])) {
                echo '<span style="font-style:italic">';
            }
            if ($gbt[0][1][$j][6]==0) {//online
                if (isset($gbt[$i][1][$j][0])) {
                    if ($gbt[$i][1][$j][4]=='average') {
                        echo "<a href=\"gb-itemanalysis.php?stu=$stu&cid=$cid&asid={$gbt[$i][1][$j][4]}&aid={$gbt[0][1][$j][7]}\">";
                    } else {
                        echo "<a href=\"gb-viewasid.php?stu=$stu&cid=$cid&asid={$gbt[$i][1][$j][4]}&uid={$gbt[$i][4][0]}\">";
                    }
                    echo $gbt[$i][1][$j][0];
                    if ($gbt[$i][1][$j][3]==1) {
                        echo ' (NC)';
                    }
                    /*else if ($gbt[$i][1][$j][3]==2) {
                        echo ' (IP)';
                    } else if ($gbt[$i][1][$j][3]==3) {
                        echo ' (OT)';
                    } else if ($gbt[$i][1][$j][3]==4) {
                        echo ' (PT)';
                    } */
                    echo '</a>';
                    if ($gbt[$i][1][$j][1]==1) {
                        echo '<sup>*</sup>';
                    }
                } else { //no score
                    if ($gbt[$i][0][0]=='Averages') {
                        echo '-';
                    } else {
                        echo "<a href=\"gb-viewasid.php?stu=$stu&cid=$cid&asid=new&aid={$gbt[0][1][$j][7]}&uid={$gbt[$i][4][0]}\">-</a>";
                    }
                }
            } else if ($gbt[0][1][$j][6]==1) { //offline
                if ($isteacher) {
                    if ($gbt[$i][0][0]=='Averages') {
                        echo "<a href=\"addgrades.php?stu=$stu&cid=$cid&grades=all&gbitem={$gbt[0][1][$j][7]}\">";
                    } else {
                        echo "<a href=\"addgrades.php?stu=$stu&cid=$cid&grades={$gbt[$i][4][0]}&gbitem={$gbt[0][1][$j][7]}\">";
                    }
                }
                if (isset($gbt[$i][1][$j][0])) {
                    echo $gbt[$i][1][$j][0];
                    if ($gbt[$i][1][$j][3]==1) {
                        echo ' (NC)';
                    }
                } else {
                    echo '-';
                }
                if ($isteacher) {
                    echo '</a>';
                }
                if ($gbt[$i][1][$j][1]==1) {
                    echo '<sup>*</sup>';
                }
            } else if ($gbt[0][1][$j][6]==2) { //discuss
                if (isset($gbt[$i][1][$j][0])) {
                    echo $gbt[$i][1][$j][0];
                } else {
                    echo '-';
                }
            }
            if (isset($gbt[$i][1][$j][5])) {
                echo '<sub>d</sub></span>';
            }
            echo '</td>';
        }

    }
    echo "</tbody></table>";
    if ($n>0) {
        $sarr = array_fill(0,$n-1,"'N'");
    } else {
        $sarr = array();
    }
    array_unshift($sarr,"'S'");

    $sarr = implode(",",$sarr);
    if (count($gbt)<500) {
        echo "<script>initSortTable('myTable',Array($sarr),true,false);</script>\n";
    }


}

?>