<?php

/**
 * Created by PhpStorm.
 * User: tudip
 * Date: 5/6/15
 * Time: 7:13 PM
 */

namespace app\models;
use Yii;

use app\components\AppUtility;
use app\models\_base\BaseImasForumPosts;
use app\models\_base\BaseImasForumViews;

class ForumView extends BaseImasForumViews
{


    public static function getbythreadId($threadid)
    {

        $views = Yii::$app->db->createCommand("SELECT userid,lastview,count(distinct userid) FROM imas_forum_views where threadid = $threadid")->queryAll();
//        AppUtility::dump($views);
        return $views;
    }



} 

