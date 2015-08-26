<?php
use app\components\AppUtility;
use app\components\AppConstant;

$libtreeshowchecks = false;

if (isset($params['libtree']) && $params['libtree']=="popup") {
    $isAdmin = false;
    $isGrpAdmin = false;
    if (isset($params['cid']) && $params['cid']=="admin") {
        if ($myRights <AppConstant::GROUP_ADMIN_RIGHT) {
            echo AppConstant::REQUIRED_ADMIN_ACCESS;
            exit;
        } else if ($myRights < AppConstant::ADMIN_RIGHT) {
            $isGrpAdmin = true;
        } else if ($myRights == AppConstant::ADMIN_RIGHT) {
            $isAdmin = true;
        }
    }
    echo <<<END
END;

    echo <<<END
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" type="text/javascript"></script>
<form id="libselectform">
END;
}
echo "<script type=\"text/javascript\">";
if (isset($params['select'])) {
    $select = $params['select'];
} else if (!isset($select)) {
    $select = "child";
}

echo "var select = '$select';\n";
if (isset($params['type'])) {
    echo "var treebox = '{$params['type']}';\n";
} else {
    echo "var treebox = 'checkbox';\n";
}

if (isset($params['selectrights'])) {
    $selectrights = $params['selectrights'];
} else {
    $selectrights = 0;
}
$allsrights = 2+3*$selectrights;

$rights = array();
$sortorder = array();
foreach ($libraryData as $line)  {
    $id = $line['id'];
    $name = addslashes($line['name']);
    $parent = $line['parent'];
    if ($line['count']==0) {
        $isempty[$id] = true;
    }
    $ltlibs[$parent][] = $id;
    $parents[$id] = $parent;
    $names[$id] = $name;
    $rights[$id] = $line['userights'];
    $sortorder[$id] = $line['sortorder'];
    $ownerids[$id] = $line['ownerid'];
    $groupids[$id] = $line['groupid'];
}
//if parent has lower userights, up them to match child library
function setparentrights($alibid) {
    global $rights,$parents;
    if ($parents[$alibid]>0) {
        if ($rights[$parents[$alibid]] < $rights[$alibid]) {
            $rights[$parents[$alibid]] = $rights[$alibid];
        }
        setparentrights($parents[$alibid]);
    }
}
foreach ($rights as $k=>$n) {
    setparentrights($k);
}

if (isset($params['libs']) && $params['libs']!='') {
    $checked = explode(",",$params['libs']);
} else if (!isset($checked)) {
    $checked = array();
}
if (isset($params['locklibs']) && $params['locklibs']!='') {
    $locked = explode(",",$params['locklibs']);
} else if (!isset($locked)) {
    $locked = array();
}
$checked = array_merge($checked,$locked);
$toopen = array();

echo "var tree = {\n";
echo " 0:[\n";
$donefirst[0] = 2;
if ($params['type']=="radio" && $select == "child") {
    echo "[0,8,\"Unassigned\",0,";
    if (in_array(0,$checked)) { echo "1";} else {echo "0";}
    echo "]";
} else {
    echo "[0,8,\"Unassigned\",0,0]";
}
echo "]";

if (isset($ltlibs[0])) {
    if (isset($base)) {
        printlist($base);
    } else {
        printlist(0);
    }
}

function printlist($parent) {
    global $names,$ltlibs,$checked,$toopen, $select,$isempty,$rights,$sortorder,$ownerids,$isadmin,$selectrights,$allsrights,$published,$userid,$locked,$groupids,$groupid,$isgrpadmin,$donefirst;
    $newchildren = array();
    $allownongrouplibs = false;
    $arr = array();
    if ($parent==0 && isset($published)) {
        $arr = explode(',',$published);
    } else {
        $arr = $ltlibs[$parent];
    }
    if (count($arr)==0) {return;}
    if ($donefirst[$parent]==0) {
        echo ",\n$parent:[";
        $donefirst[$parent]==1;
    }
    if ($sortorder[$parent]==1) {
        $orderarr = array();
        foreach ($arr as $child) {
            $orderarr[$child] = $names[$child];
        }
        natcasesort($orderarr);
        $arr = array_keys($orderarr);
    }
    foreach ($arr as $child) {
        if ($rights[$child]>$allsrights || (($rights[$child]%3)>$selectrights && $groupids[$child]==$groupid) || $ownerids[$child]==$userid || ($isgrpadmin && $groupids[$child]==$groupid) ||$isadmin) {
            if (!$isadmin) {
                if ($rights[$child]==5 && $groupids[$child]!=$groupid) {
                    $rights[$child]=4;  //adjust coloring
                }
            }
            if ($donefirst[$parent]==2) {echo ",";} else {$donefirst[$parent]=2;}
            echo "[$child,{$rights[$child]},'{$names[$child]}',";
            if (isset($ltlibs[$child])) { //library has children
                if ($select == "parent" || $select=="all") {
                    if ($_GET['type']=="radio") {
                        if (in_array($child,$locked)) { //removed the following which prevented creation of sublibraries of "open to all" libs.  Not sure why I had that.   || ($select=="parent" && $rights[$child]>2 && !$allownongrouplibs && !$isadmin && !$isgrpadmin)) {
                            echo "1,";
                        } else {
                            echo ",";
                        }
                        if (in_array($child,$checked)) { echo "1";} //else {echo "0";}
                        echo "]";
                    } else {
                        if (in_array($child,$locked)) {
                            echo "1,";
                        } else {
                            echo ",";
                        }
                        if (in_array($child,$checked)) { echo "1";} //else {echo "0";}
                        echo "]";
                    }
                } else {
                    echo "-1,]";
                }
                $newchildren[] = $child;
            } else {  //no children
                if ($select == "child" || $select=="all" || $isempty[$child]==true) {
                    if ($_GET['type']=="radio") {
                        if (in_array($child,$locked) || ($select=="parent" && $rights[$child]>2 && !$allownongrouplibs && !$isadmin && !$isgrpadmin)) {
                            echo "1,";
                        } else {
                            echo ",";
                        }
                        if (in_array($child,$checked)) { echo "1";} //else {echo "0";}
                        echo "]";
                    } else {
                        if (in_array($child,$locked)) {
                            echo "1,";
                        } else {
                            echo ",";
                        }
                        if (in_array($child,$checked)) { echo "1";} //else {echo "0";}
                        echo "]";
                    }
                } else {
                    echo "-1,]";
                }
            }
        }
    }
    echo "]";
    foreach ($newchildren as $newchild) {
        $donefirst[$newchild] = false;
        printlist($newchild);
    }
}

echo "}";
echo "</script>";  //end tree definition script
if (isset($_GET['base'])) {
    $base = $_GET['base'];
}
echo "<input type=button value=\"Uncheck all\" onclick=\"uncheckall(this.form)\"/><br>";

if (isset($base)) {
    echo "<input type=hidden name=\"rootlib\" value=$base>{$names[$base]}</span>";
} else {
    if ($select == "parent") {
        echo "<input type=radio name=\"libs\" value=0 ";
        if (in_array(0,$checked)) { echo "CHECKED";	}
        echo "> <span id=\"n0\" class=\"r8\">Root</span>";
    } else {
        echo "<span class=\"r8\">Root</span>";
    }
}
echo "<div id=tree></div>";
echo "<script type=\"text/javascript\">\n";
echo "function initlibtree(showchecks) {";
echo "showlibtreechecks = showchecks;";
echo "var tree = document.getElementById(\"tree\");";
echo "while (tree.childNodes.length>0) { tree.removeChild(tree.firstChild);}";
echo "tree.appendChild(buildbranch(0)); ";
echo "if (showchecks) {";
$expand = array();
foreach ($checked as $child) {
    if (isset($base)) {
        if ($parents[$child]!=$base) {
            setshow($parents[$child]);
        }
    } else {
        if ($parents[$child]!=0) {
            setshow($parents[$child]);
        }
    }
}
function setshow($id) {
    global $parents,$base,$expand;
    array_unshift($expand,$id);
    if (isset($base)) {
        if (isset($parents[$id]) && $parents[$id]!=$base) {
            setshow($parents[$id]);
        }
    } else {
        if (isset($parents[$id]) && $parents[$id]!=0) {
            setshow($parents[$id]);
        }
    }
}
$setshowed = array();
for ($i=0;$i<count($expand);$i++) {
    if (in_array($expand[$i],$setshowed)) {
        continue;
    } else {
        echo "addbranch({$expand[$i]});\n";
        $setshowed[] = $expand[$i];
    }
}
echo "}";

if (isset($libtreeshowchecks) && $libtreeshowchecks == false) {
    echo "addLoadEvent(function() {initlibtree(false);})";
} else {
    echo "addLoadEvent(function() {initlibtree(true);})";
}
echo "}";
echo "</script>\n";

$colorcode =  "<p><b>Color Code</b><br/>";
$colorcode .= "<span class=r8>Open to all</span><br/>\n";
$colorcode .= "<span class=r4>Closed</span><br/>\n";
$colorcode .= "<span class=r5>Open to group, closed to others</span><br/>\n";
$colorcode .= "<span class=r2>Open to group, private to others</span><br/>\n";
$colorcode .= "<span class=r1>Closed to group, private to others</span><br/>\n";
$colorcode .= "<span class=r0>Private</span></p>\n";
if (isset($_GET['libtree']) && $_GET['libtree']=="popup") {
    echo <<<END
<input type=button value="Use Libraries" onClick="setlib(this.form)">
$colorcode
</form>
END;
} else {
    echo $colorcode;
}
?>