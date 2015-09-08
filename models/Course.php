<?php
namespace app\models;


use app\components\AppUtility;
use app\components\AppConstant;
use app\models\_base\BaseImasCourses;
use Yii;
use yii\db\Exception;
use yii\db\Query;

class Course extends BaseImasCourses {

    public function create($user, $bodyParams)
    {
        $params = $bodyParams['CourseSettingForm'];
        $course['ownerid'] = $user->id;
        $course['name'] = $params['courseName'];
        $course['enrollkey'] = $params['enrollmentKey'];
        $availables = isset($params['available']) ? $params['available'] : AppConstant::AVAILABLE_NOT_CHECKED_VALUE;
        $course['available'] = AppUtility::makeAvailable($availables);
        $course['picicons'] = AppConstant::PIC_ICONS_VALUE;
        $course['allowunenroll'] = AppConstant::UNENROLL_VALUE;
        $course['copyrights'] = $params['copycourse'];
        $course['msgset'] = $params['messageSystem'];
        $toolsets = isset($params['navigationLink']) ? $params['navigationLink'] : AppConstant::NAVIGATION_NOT_CHECKED_VALUE;
        $isTemplate = isset($params['courseAsTemplate']) ? $params['courseAsTemplate'] : AppConstant::NUMERIC_ZERO;
        $course['istemplate'] = AppUtility::createIsTemplate($isTemplate);
        $course['toolset']  = AppUtility::makeToolset($toolsets);
        $course['cploc']= AppConstant::CPLOC_VALUE;
        $course['deflatepass']= $params['latePasses'];
        $course['showlatepass']= AppConstant::SHOWLATEPASS;
        $course['theme']= $params['theme'];
        $course['deftime'] = AppUtility::calculateTimeDefference($bodyParams['start_time'],$bodyParams['end_time']);
        $course['end_time'] = $bodyParams['end_time'];
        $course['chatset'] = AppConstant::CHATSET_VALUE;
        $course['topbar'] = AppConstant::TOPBAR_VALUE;
        $course['hideicons'] = AppConstant::HIDE_ICONS_VALUE;
        $course['itemorder'] = AppConstant::ITEM_ORDER;
        $course = AppUtility::removeEmptyAttributes($course);
        $this->attributes = $course;
        $this->save();


        return $this->id;
    }

    public static function getByIdAndEnrollmentKey($id, $enroll)
    {
       return static::findOne(['id' =>$id, 'enrollkey' => $enroll]);
    }

    public static function getByCourseName($name)
    {
        return static::findAll(['name' => $name]);
    }

    public static function getById($cid)
    {
        return static::findOne(['id' => $cid]);
    }
    public static function getByIdandOwnerId($id, $ownerId)
    {
        return static::findOne(['id' =>$id, 'ownerid' => $ownerId]);
    }

    public static function deleteCourse($cid)
    {
        $connection = Yii::$app->getDb();
        $transaction = $connection->beginTransaction();
        try {
            $connection->createCommand()->delete('imas_courses', 'id ='.$cid)->execute();
            $connection->createCommand()->delete('imas_assessments', 'courseid ='.$cid)->execute();
            $connection->createCommand()->delete('imas_badgesettings', 'courseid ='.$cid)->execute();
            $connection->createCommand()->delete('imas_calitems', 'courseid ='.$cid)->execute();
            $connection->createCommand()->delete('imas_content_track', 'courseid ='.$cid)->execute();
            $connection->createCommand()->delete('imas_diags', 'cid ='.$cid)->execute();
            $connection->createCommand()->delete('imas_external_tools', 'courseid ='.$cid)->execute();
            $connection->createCommand()->delete('imas_drillassess', 'courseid ='.$cid)->execute();
            $connection->createCommand()->delete('imas_firstscores', 'courseid ='.$cid)->execute();
            $connection->createCommand()->delete('imas_forums', 'courseid ='.$cid)->execute();
            $connection->createCommand()->delete('imas_gbcats', 'courseid ='.$cid)->execute();
            $connection->createCommand()->delete('imas_gbitems', 'courseid ='.$cid)->execute();
            $connection->createCommand()->delete('imas_gbscheme', 'courseid ='.$cid)->execute();
            $connection->createCommand()->delete('imas_inlinetext', 'courseid ='.$cid)->execute();
            $connection->createCommand()->delete('imas_items', 'courseid ='.$cid)->execute();
            $connection->createCommand()->delete('imas_linkedtext', 'courseid ='.$cid)->execute();
            $connection->createCommand()->delete('imas_login_log', 'courseid ='.$cid)->execute();
            $connection->createCommand()->delete('imas_lti_courses', 'courseid ='.$cid)->execute();
            $connection->createCommand()->delete('imas_msgs', 'courseid ='.$cid)->execute();
            $connection->createCommand()->delete('imas_outcomes', 'courseid ='.$cid)->execute();
            $connection->createCommand()->delete('imas_students', 'courseid ='.$cid)->execute();
            $connection->createCommand()->delete('imas_stugroupset', 'courseid ='.$cid)->execute();
            $connection->createCommand()->delete('imas_teachers', 'courseid ='.$cid)->execute();
            $connection->createCommand()->delete('imas_tutors', 'courseid ='.$cid)->execute();
            $connection->createCommand()->delete('imas_wikis', 'courseid ='.$cid)->execute();
            $transaction->commit();
            return true;

        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    public static function findCourseDataArray()
    {
        $query = new Query();
        $query	->select(['imas_users.id as userid','imas_users.FirstName', 'imas_users.LastName', 'imas_courses.name', 'imas_courses.id as courseid'])
            ->from('imas_courses')
            ->join(	'LEFT OUTER JOIN',
                'imas_users',
                'imas_users.id = imas_courses.ownerid'
            );
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }
    public static function findByName($name){
        return static::findOne(['name'=>$name]);
    }
    public static function updatePassHours($latepasshours,$cid)
    {
        $student = Course::findOne(['id' => $cid]);
        $student->latepasshrs = $latepasshours;
        $student->save();
    }

    public static function setItemOrder($itemList, $courseId)
    {
        $course = Course::findOne(['id' => $courseId]);
        $course->itemorder = $itemList;
        $course->save();
    }

    public static function getItemOrder($courseId)
    {
        return Course::findOne(['id' => $courseId]);
    }

    public static function getOutcome($courseId){
        return Course::find()->select('outcomes')->where(['id' => $courseId])->one();
    }

    public function SaveOutcomes($courseId,$outcomeGrp)
    {

        $isRecord = Course::findAll(['id' =>$courseId]);
        if($isRecord)
        {
               foreach($isRecord as $outcome)
               {
                   $outcome->outcomes = $outcomeGrp;
                   $outcome->save();
               }
        }
    }

    public static function getByCourseIdOutcomes($courseId)
    {
        return Course::find()->select('outcomes')->where(['id' => $courseId])->all();
    }

    public static function setBlockCount($itemOrder,$blockCount,$courseId)
    {
        $course = Course::findOne(['id' => $courseId]);
        $course->itemorder = $itemOrder;
        $course->blockcnt = $blockCount;
        $course->save();
    }
    public static function getByCourseAndUser($cid)
    {
        return Yii::$app->db->createCommand("SELECT imas_courses.name,imas_courses.available,imas_courses.lockaid,imas_courses.copyrights,imas_users.groupid,imas_courses.theme,imas_courses.newflag,imas_courses.msgset,imas_courses.topbar,imas_courses.toolset,imas_courses.deftime,imas_courses.picicons FROM imas_courses,imas_users WHERE imas_courses.id= $cid AND imas_users.id=imas_courses.ownerid")->queryAll();
    }
    public static function UpdateItemOrder($finalBlockItems,$course,$blockCnt)
    {
        $isRecord = Course::findOne(['id' =>$course]);
        if($isRecord)
        {
            $isRecord->itemorder = $finalBlockItems;
            $isRecord->blockcnt = $blockCnt;
            $isRecord->save();
        }
    }

    public static function getByCourseAndGroupId($params,$user)
    {
        return Yii::$app->db->createCommand("SELECT imas_courses.id FROM imas_courses,imas_users WHERE imas_courses.id='{$params['cid']}' AND imas_courses.ownerid=imas_users.id AND imas_users.groupid='{$user['groupid']}'")->queryAll();
    }

    public static function getByAvailable($params){
        if(isset($params['cid'])){
            $courseId = intval($params['cid']);
            return Yii::$app->db->createCommand("SELECT id FROM imas_courses WHERE (istemplate&8)=8 AND available<4 AND id= $courseId")->queryAll();
        }else{
            return Yii::$app->db->createCommand("SELECT id FROM imas_courses WHERE (istemplate&8)=8 AND available<4")->queryAll();
        }
    }

    public static function setOwner($params,$user){
        if($user->rights < AppConstant::GROUP_ADMIN_RIGHT){
            $courseData = Course::findAll(['id' => $params['cid'],'ownerid' => $user->id]);
        }else{
            $courseData = Course::findOne(['id' => $params['cid']]);
        }
        if($courseData){
            $courseData->ownerid = $params['newOwner'];
            $courseData->save();
            return $courseData->id;
        }
    }

    public static function updateNewFlag($courseId)
    {
        $course = Course::find()->where(['id' => $courseId])->one();
        $newflag = $course['newflag'];
        $newflag = $newflag ^ AppConstant::NUMERIC_ONE;
        $course->newflag = $newflag;
        $course->save();
    }
 /*
  *Query To Show Courses available For Teacher in My classes drop-down
  */
    public static  function getGetMyClasses($userId)
    {
        $items = [];
        $myClasses = Course::find()->where(['ownerid' => $userId])->all();
        foreach($myClasses as $key => $singleClass)
        {
            $items[] = ['label' => $singleClass->name, 'url' => '../../instructor/instructor/index?cid='.$singleClass['id']];
            if(count($myClasses) == $key+AppConstant::NUMERIC_ONE)
            {
                array_push($items,'<li class="divider"></li>');
                array_push($items,['label' => 'Manage Questions', 'url' => '#']);
                array_push($items,['label' => 'Questions Libraries', 'url' => '#']);
            }
        }
        return $items;
    }

    public static function  getCidForCopyingCourse($userId,$ctc)
    {

        $query = Yii::$app->db->createCommand("SELECT imas_courses.id FROM imas_courses,imas_teachers WHERE imas_courses.id=imas_teachers.courseid AND imas_teachers.userid= :userid AND imas_courses.id= :ctc");
        $query->bindValue('userid',$userId);
        $query->bindValue('ctc',$ctc);
        $data = $query->queryOne();
        return $data;
    }

    public static function getEnrollKey($ctc)
    {
        $query = new Query();
        $query	->select(['enrollkey','copyrights'])
            ->from('imas_courses')
            ->where('id= :id',[':id' => $ctc]);
        $command = $query->createCommand();
        $data = $command->queryOne();
        return $data;

    }
    public static function getDataForCopyCourse($ctc)
    {
        $query = Yii::$app->db->createCommand("SELECT imas_users.groupid FROM imas_courses,imas_users,imas_teachers WHERE imas_courses.id=imas_teachers.courseid AND imas_teachers.userid=imas_users.id AND imas_courses.id= :ctc");
        $query->bindValue('ctc',$ctc);
        $data = $query->queryOne();
        return $data;
    }

    public static function getDataByCtc($toCopy,$ctc)
    {
        $query = new Query();
        $query	->select($toCopy)
            ->from('imas_courses')
            ->where('id=:id',[':id' => $ctc]);
        $command = $query->createCommand();
        $data = $command->queryone();
        return $data;

    }

    public static function updateCourseForCopyCourse($courseId,$sets)
    {

        $query = Yii::$app->db->createCommand("UPDATE imas_courses SET $sets WHERE id=:cid");
        $query->bindValue('cid',$courseId);
        $query->query();
    }
    public static function updateOutcomes($newOutcomeArr,$courseId)
    {
        $outcomes = Course::find()->where(['id' => $courseId])->one();
        if($outcomes)
        {
            $outcomes->outcomes = $newOutcomeArr;
            $outcomes->save();
        }

    }
    public static function getBlockCnt($courseId)
    {
        $query = new Query();
        $query	->select(['blockcnt'])
            ->from('imas_courses')
            ->where('id= :id',[':id' => $courseId]);
        $command = $query->createCommand();
        $data = $command->queryOne();
        return $data;

    }

    public static function getCourseData($myRights, $showcourses, $userId)
    {
        $query = new Query();
        $query	->select(['imas_courses.id','imas_courses.ownerid','imas_courses.name','imas_courses.available','imas_users.FirstName','imas_users.LastName'])
            ->from('imas_courses')
            ->join('JOIN',
                'imas_users',
                'imas_courses.ownerid=imas_users.id'
            );
        if($myRights > AppConstant::ADMIN_RIGHT){
            $query->andWhere(['imas_courses.available<4']);
        }
        if (($myRights >= AppConstant::LIMITED_COURSE_CREATOR_RIGHT && $myRights < AppConstant::GROUP_ADMIN_RIGHT) || $showcourses==0){
            $query->andWhere(['imas_courses.ownerid' => $userId]);
        }
        if ($myRights >= AppConstant::GROUP_ADMIN_RIGHT && $showcourses > 0)
        {
            $query->andWhere(['imas_courses.ownerid' => $showcourses]);
            $query->orderBy('imas_users.LastName,imas_courses.name');
        } else{
            $query->orderBy('imas_courses.name');
        }
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }

    public static function getPicIcons($ctc)
    {
        $query = new Query();
        $query	->select(['itemorder','picicons'])
            ->from('imas_courses')
            ->where('id= :id',[':id' => $ctc]);
        $command = $query->createCommand();
        $data = $command->queryOne();
        return $data;
    }
    public static function getDataByJoins($groupId,$userId)
    {
        $query = "SELECT ic.id,ic.name,ic.copyrights,iu.LastName,iu.FirstName,iu.email,it.userid,iu.groupid FROM imas_courses AS ic,imas_teachers AS it,imas_users AS iu,imas_groups WHERE ";
        $query .= "it.courseid=ic.id AND it.userid=iu.id AND iu.groupid=imas_groups.id AND iu.groupid<>'$groupId' AND iu.id<>'$userId' AND ic.available<4 ORDER BY imas_groups.name,iu.LastName,iu.FirstName,ic.name";
        $data= Yii::$app->db->createCommand($query)->queryAll();
        return $data;
    }
    public static function getFromJoinOnTeacher($userId,$courseId)
    {
        $query  = Yii::$app->db->createCommand("SELECT ic.id,ic.name FROM imas_courses AS ic,imas_teachers WHERE imas_teachers.courseid=ic.id AND imas_teachers.userid='$userId' and ic.id<>'$courseId' AND ic.available<4 ORDER BY ic.name")->queryAll();
        return $query;
    }
    public static function getFromJoinsData($groupId,$userId)
    {
        $query = "SELECT ic.id,ic.name,ic.copyrights,iu.LastName,iu.FirstName,iu.email,it.userid FROM imas_courses AS ic,imas_teachers AS it,imas_users AS iu WHERE ";
        $query .= "it.courseid=ic.id AND it.userid=iu.id AND iu.groupid='$groupId' AND iu.id<>'$userId' AND ic.available<4 ORDER BY iu.LastName,iu.FirstName,ic.name";
        $data= Yii::$app->db->createCommand($query)->queryAll();
        return $data;
    }

    public static function getTemplate()
    {
        $query = "SELECT id,name,copyrights FROM imas_courses WHERE (istemplate&1)=1 AND copyrights=2 AND available<4 ORDER BY name";
        $data= Yii::$app->db->createCommand($query)->queryAll();
        return $data;
    }
    public static function getGroupTemplate($groupId)
    {
        $query = "SELECT ic.id,ic.name,ic.copyrights FROM imas_courses AS ic JOIN imas_users AS iu ON ic.ownerid=iu.id WHERE ";
        $query .= "iu.groupid='$groupId' AND (ic.istemplate&2)=2 AND ic.copyrights>0 AND ic.available<4 ORDER BY ic.name";
        $data= Yii::$app->db->createCommand($query)->queryAll();
        return $data;
    }

    public static function queryForCourse($id)
    {
        $query = "SELECT ic.id,ic.name FROM imas_courses AS ic JOIN imas_students AS istu ON istu.courseid=ic.id AND istu.userid=".$id;
        $data= Yii::$app->db->createCommand($query)->queryAll();
        return $data;
    }
    public static function queryFromCourseForTutor($id)
    {
        $query = "SELECT ic.id,ic.name FROM imas_courses AS ic JOIN imas_tutors AS istu ON istu.courseid=ic.id AND istu.userid=".$id;
        $data= Yii::$app->db->createCommand($query)->queryAll();
        return $data;
    }
    public static function queryFromCourseForTeacher($id)
    {
        $query = "SELECT ic.id,ic.name FROM imas_courses AS ic JOIN imas_teachers AS istu ON istu.courseid=ic.id AND istu.userid=".$id;
        $data= Yii::$app->db->createCommand($query)->queryAll();
        return $data;
    }
    public static function getItemOrderAndBlockCnt($ctc)
    {
        $query = new Query();
        $query	->select(['itemorder','blockcnt'])
            ->from('imas_courses')
            ->where('id= :id',[':id' => $ctc]);
        $command = $query->createCommand();
        $data = $command->queryOne();
        return $data;
    }
    public static function getByUserId($userId)
    {
        $query = Yii::$app->db->createCommand("SELECT imas_courses.id,imas_courses.name FROM imas_courses,imas_teachers WHERE imas_courses.id=imas_teachers.courseid AND imas_teachers.userid='$userId' ORDER BY imas_courses.name")->queryAll();
        return $query;
    }
    public static function UpdateItemOrderAndBlockCnt($itemOrder,$blockCnt,$courseId,$num)
    {
        $isRecord = Course::findOne(['id' =>$courseId]);
        if($isRecord)
        {
            $isRecord->itemorder = $itemOrder;
            if($num == AppConstant::NUMERIC_ZERO)
            {
                $isRecord->blockcnt = $blockCnt+AppConstant::NUMERIC_ONE;
            }
            $isRecord->save();
        }
    }

    public static function getDataByTemplate()
    {
        $query = "SELECT id FROM imas_courses WHERE (istemplate&4)=4";
        return Yii::$app->db->createCommand($query)->queryAll();
    }

    public static function getBlckTitles($search)
    {
        $query = new Query();
        $query	->select(['id','itemorder','name'])
            ->from('imas_courses')
            ->where(['LIKE','itemorder',$search])->limit(40);
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }

    public static function getOutComeByCourseId($cid)
    {
        $query = new Query();
        $query	->select(['outcomes'])
            ->from('imas_courses')
            ->where(['id' => $cid]);
        $command = $query->createCommand();
        $data = $command->queryone();
        return $data;
    }
}
