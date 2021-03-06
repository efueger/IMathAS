<?php
	//Displays New Message list
	//(c) 2009 David Lippman
	
	//isread is bitwise:
	//1      2         4                   8
	//Read   Deleted   Deleted by Sender   Tagged
	
	require("../validate.php");
	if ($cid!=0 && !isset($teacherid) && !isset($tutorid) && !isset($studentid)) {
	   require("../header.php");
	   echo "You are not enrolled in this course.  Please return to the <a href=\"../index.php\">Home Page</a> and enroll\n";
	   require("../footer.php");
	   exit;
	}
	if (isset($teacherid)) {
		$isteacher = true;	
	} else {
		$isteacher = false;
	}
	$cansendmsgs = false;
	$threadsperpage = $listperpage;
	
	$cid = $_GET['cid'];
	
	if (isset($_POST['read'])) {
		$checklist = "'".implode("','",$_POST['checked'])."'";
		$query = "UPDATE imas_msgs SET isread=(isread|1) WHERE id IN ($checklist) AND (isread&1)=0";
		mysql_query($query) or die("Query failed : $query " . mysql_error());
	}
	if (isset($_POST['remove'])) {
		$checklist = "'".implode("','",$_POST['checked'])."'";
		$query = "DELETE FROM imas_msgs WHERE id IN ($checklist) AND (isread&4)=4";
		mysql_query($query) or die("Query failed : $query " . mysql_error());
		$query = "UPDATE imas_msgs SET isread=(isread|2) WHERE id IN ($checklist)";
		mysql_query($query) or die("Query failed : $query " . mysql_error());
	}
	if (isset($_GET['removeid'])) {
		$query = "DELETE FROM imas_msgs WHERE id='{$_GET['removeid']}' AND (isread&4)=4";
		mysql_query($query) or die("Query failed : $query " . mysql_error());
		$query = "UPDATE imas_msgs SET isread=(isread|2) WHERE id='{$_GET['removeid']}'";
		mysql_query($query) or die("Query failed : $query " . mysql_error());
	}
	
	$pagetitle = "New Messages";
	require("../header.php");
	
	echo "<div class=breadcrumb><a href=\"../index.php\">Home</a> ";
	if ($cid>0) {
		echo "&gt; <a href=\"../course/course.php?cid=$cid\">$coursename</a> ";
	}
	echo "&gt; New Message List</div>";
	echo '<div id="headernewmsglist" class="pagetitle"><h2>New Messages</h2></div>';	
	
?>

	<form id="qform" method="post" action="newmsglist.php?page=<?php echo $page;?>&cid=<?php echo $cid;?>">

	Check: <a href="#" onclick="return chkAllNone('qform','checked[]',true)">All</a> <a href="#" onclick="return chkAllNone('qform','checked[]',false)">None</a>
	With Selected: <input type=submit name="read" value="Mark as Read">
	<input type=submit name="remove" value="Delete">
		
<?php
	$query = "SELECT imas_msgs.id,imas_msgs.title,imas_msgs.senddate,imas_msgs.replied,imas_users.LastName,imas_users.FirstName,imas_msgs.isread,imas_courses.name ";
	$query .= "FROM imas_msgs LEFT JOIN imas_users ON imas_users.id=imas_msgs.msgfrom LEFT JOIN imas_courses ON imas_courses.id=imas_msgs.courseid WHERE ";
	$query .= "imas_msgs.msgto='$userid' AND (imas_msgs.isread&3)=0 ";
	$query .= "ORDER BY imas_courses.name, senddate DESC ";
	$result = mysql_query($query) or die("Query failed : $query " . mysql_error());
	if (mysql_num_rows($result)==0) {
		echo "<p>No new messages</p>";
	} else {
		$lastcourse = '';
		while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
			if ($line['name']!=$lastcourse) {
				if($lastcourse!='') {
					echo '</tbody></table>';
				}
				echo '<h4>Course: '.$line['name'].'</h4>';
				echo '<table class="gb"><thead><tr><th></th><th>Message</th><th>Replied</th><th>From</th><th>Course</th><th>Sent</th></tr></thead><tbody>';
				$lastcourse = $line['name'];
			}
			if (trim($line['title'])=='') {
				$line['title'] = '[No Subject]';
			}
			$n = 0;
			while (strpos($line['title'],'Re: ')===0) {
				$line['title'] = substr($line['title'],4);
				$n++;
			}
			if ($n==1) {
				$line['title'] = 'Re: '.$line['title'];
			} else if ($n>1) {
				$line['title'] = "Re<sup>$n</sup>: ".$line['title'];
			}
			echo "<tr><td><input type=checkbox name=\"checked[]\" value=\"{$line['id']}\"/></td><td>";
			echo "<a href=\"viewmsg.php?page$page&cid=$cid&filtercid=$filtercid&type=new&msgid={$line['id']}\">";
			if (($line['isread']&1)==0) {
				echo "<b>{$line['title']}</b>";
			} else {
				echo $line['title'];
			}
			echo "</a></td><td>";
			if ($line['replied']==1) {
				echo "Yes";
			}
			if ($line['LastName']==null) {
				$line['LastName'] = "[Deleted]";
			}
			echo "</td><td>{$line['LastName']}, {$line['FirstName']}</td>";
			if ($line['name']==null) {
				$line['name'] = "[Deleted]";
			}
			echo "<td>{$line['name']}</td>";
			$senddate = tzdate("F j, Y, g:i a",$line['senddate']);
			echo "<td>$senddate</td></tr>";
		}
		echo '</tbody></table>';
	}
?>
	</form>
<?php
	
	echo "<p><a href=\"sentlist.php?cid=$cid\">Sent Messages</a></p>";
	
	require("../footer.php");
?>
		
	
