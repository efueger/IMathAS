<?php
use app\components\AppUtility;
$this->title = 'Diagnostic One-time Passwords';
$this->params['breadcrumbs'] = $this->title;
?>
<div class="item-detail-header">
<?php
    echo $this->render("../../itemHeader/_indexWithLeftContent",['link_title'=>['Home','Admin'], 'link_url' => [AppUtility::getHomeURL().'site/index',AppUtility::getHomeURL().'admin/admin/index'], 'page_title' => $this->title]);
   ?>
</div>
<div class = "title-container">
    <div class="row">
        <div class="pull-left page-heading">
            <div class="vertical-align title-page"><?php echo $this->title ?></div>
        </div>
    </div>
</div>
<div class="tab-content shadowBox non-nav-tab-item">
<?php
        echo '<span class=col-lg-4><b>'.$nameOfDiag['name'].'</b></span><br/>';
        if (isset($params['generate']))
        {
            if (isset($params['n'])) {

                echo "<span class=col-lg-3>Newly generated passwords</b></span> <span class=col-lg-2><a href=".AppUtility::getURLFromHome('admin', 'admin/diag-one-time?id='.$diag.'&view=true').">View all</a></span><br><br/>";
                echo '<div class="col-lg-12"><table class="table table-bordered table-striped table-hover data-table">
                <thead>
                    <tr>
                    <th>Codes</th>
                    <th>Good For</th>
                    </tr>
                </thead>
                <tbody>';
                foreach ($code_list as $code) {
                    echo "<tr><td>{$code['code']}</td><td>{$code['goodfor']}</td></tr>";
                }
                echo '</tbody></table></div>';
            } else
            {
                echo "<form method='post' action='diag-one-time?id=$diag&generate=true'>";
                echo '<div class="col-lg-6"><div class="col-lg-2 padding-top-five">Generate</div><div class="col-lg-2 padding-left-zero"><input type="text" class="form-control" size="1" value="1" name="n" /></div><div class="col-lg-1 padding-left-zero padding-top-five">passwords </div><br/><br>';
                echo '<div class="col-lg-12"><div class="col-lg-4 padding-left-zero padding-top-five">Allow multi-use within</div><div class="col-lg-2 padding-left-zero"><input type="text" class="form-control" size="1" value="0" name="multi" /></div><div class="col-lg-6 padding-left-zero padding-top-five"> minutes (0 for one-time-only use)</div></div>';
                echo '<span class="col-lg-6"><input type="submit" value="Go" /></span>';
                echo '</form>';
            }
        } else if (isset($_GET['delete'])) {
            echo "<div class='col-lg-10'>Are you sure you want to delete all one-time passwords for this diagnostic?</div>\n<br>";
            echo "<br><div class='col-lg-10'><input type=button value=\"Delete\" onclick=\"window.location='diag-one-time?id=$diag&delete=true'\">\n";
            echo "<input type=button value=\"Nevermind\" class=\"secondarybtn\" onclick=\"window.location='index'\"></div>\n";
        } else {
            echo "<div class=col-lg-3><b>All one-time passwords</b></div> <div class=col-lg-1><a href=".AppUtility::getURLFromHome('admin', 'admin/diag-one-time?id='.$diag.'&generate=true')." ?>Generate</a></div>
             <div class=col-lg-1><a href=".AppUtility::getURLFromHome('admin','admin/diag-one-time?id=' .$diag.'&delete=check').">Delete All</a></div><br/><br/>";
            echo '<div class="col-lg-12"><table class="table table-bordered table-striped table-hover data-table">
                    <thead>
                        <tr>
                            <th>Codes</th>
                            <th>Good For</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody  >';
            foreach ($code_list as $row) {
                echo "<tr>
                        <td>{$row['code']}</td>
                        <td>{$row['goodfor']}</td>
                        <td>{$row['time']}</td>
                    </tr>";
            }
            echo '</tbody></table></div>';
        }
        ?>
 </div>