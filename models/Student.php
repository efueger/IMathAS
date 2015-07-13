<?php
/**
 * Created by PhpStorm.
 * User: tudip
 * Date: 22/4/15
 * Time: 5:54 PM
 */

namespace app\models;


use app\components\AppConstant;
use app\components\AppUtility;
use app\models\_base\BaseImasStudents;
use yii\db\Query;

class Student extends BaseImasStudents {

    public function create($param)
    {
        $this->attributes = $param;
        $this->save();
    }
    public static function getByCourseId($courseId, $userId)
    {
        return static::findOne(['courseid' => $courseId, 'userid' => $userId]);
    }

    public static function getByUserId($id)
    {
        return static::findAll(['userid' => $id]);
    }

    public static function getByCId($cId)
    {
        return static::findOne(['courseid' => $cId]);
    }
    public static function getByUserIdentity($uid,$courseid)
    {

        return static::findAll(['userid' => $uid,'courseid' => $courseid]);
    }
    public function createNewStudent($userId,$cid,$param){

        $this->userid = $userId;
        $this->courseid = $cid;
        $this->section = empty($param['section']) ? null : $param['section'];
        $this->code = empty($param['code']) ? null : $param['code'];;
        $this->save();
    }
    public static function findByCid($cId){
        return static::findAll(['courseid'=>$cId]);
    }
    public function insertNewStudent($studentId,$courseId,$section)
    {
        $this->userid = $studentId;
        $this->courseid = $courseId;
        $this->section = empty($section) ? null : $section;
        $this->save();
    }
    public static function updateSectionAndCodeValue($section, $userid, $code, $cid,$params = null)
    {
        $student = Student::findOne(['userid' => $userid, 'courseid' => $cid]);
        $student->section = $section;
        $student->code = $code;
        if($params != null)
        {
           if($params['locked'] == 1) {
               $student->locked = strtotime(date('F d, o g:i a'));
           }
            else{
                $student->locked = 0;
            }
           $student->hidefromcourselist = $params['hidefromcourselist'];

            if($params['timelimitmult'] != 0)
            {
                $student->timelimitmult =  $params['timelimitmult'];
            }
        }
        $student->save();
    }
    public static function updateLatepasses($latepass,$userid,$cid)
    {
        $student = Student::findOne(['userid' => $userid, 'courseid' => $cid]);
        $student->latepass = $latepass;
        $student->save();
    }
    public static function findByCourseId($cId,$sortBy, $order){
        return static::find()->where(['courseid'=>$cId])->groupBy('section')->orderBy([$sortBy => $order])->all();
    }
    public static function updateLocked($userid,$courseid)
    {
        $student = Student::findOne(['userid' => $userid,'courseid' => $courseid]);
        $student->locked = strtotime(date('F d, o g:i a'));
        $student->save();
    }
    public static function deleteStudent($userid,$courseid)
    {
        $student = Student::findOne(['userid' => $userid,'courseid' => $courseid]);
        $student->delete();
    }
    public function assignSectionAndCode($newEntry,$id)
    {
        $this->userid = $id;
        $this->section = $newEntry['5'];
        $this->code = $newEntry['4'];
        $this->save();
    }
    public static function updateLockOrUnlockStudent($params)
    {
        $courseId = $params['courseId'];
        $studentId = $params['studentId'];
        $student = Student::findOne(['userid' => $studentId,'courseid' => $courseId]);
        if($params['lockOrUnlock'] == 1){
            $student->locked = 0;
            $student->save();
         }
        if($params['lockOrUnlock'] == 0)
        {
            $student->locked = strtotime(date('F d, o g:i a'));
            $student->save();
        }
    }
    public static function reduceLatepasses($userid, $cid, $n)
    {
        $student = Student::findOne(['userid' => $userid, 'courseid' => $cid]);
        if($student->latepass > $n){
            $student->latepass = $student->latepass - $n;
        }
        else{
            $student->latepass = 0;
        }
        $student->save();
    }
    public static function updateHideFromCourseList($userId, $courseId)
    {
        $student = Student::findOne(['userid' => $userId, 'courseid' => $courseId]);
        if($student){
            if($student->hidefromcourselist == 0){
                $student->hidefromcourselist = 1;
            }else{
                $student->hidefromcourselist = 0;
            }
            $student->save();
        }
    }
    public static function findHiddenCourse($userId)
    {
        return static::find()->where(['userid'=>$userId])->andWhere(['NOT LIKE', 'hidefromcourselist', 0 ])->all();
    }

    public static function findDistinctSection($courseId)
    {
        return static::find()->select('section')->distinct()->where(['courseid' => $courseId])->orderBy('section')->all();
    }

    public static function findStudentByCourseId($courseId,  $limuser, $secfilter, $hidelocked, $timefilter, $lnfilter, $isdiag, $hassection, $usersort)
    {
        $query = new Query();
        $query	->select(['imas_users.id', 'imas_users.SID', 'imas_users.FirstName', 'imas_users.LastName', 'imas_users.SID', 'imas_users.email', 'imas_students.section', 'imas_students.code', 'imas_students.locked', 'imas_students.timelimitmult', 'imas_students.lastaccess', 'imas_users.hasuserimg', 'imas_students.gbcomment'])
            ->from('imas_users')
            ->join(	'INNER JOIN',
                'imas_students',
                'imas_users.id = imas_students.userid'
            )
            ->where(['imas_students.courseid' => $courseId]);
        if($limuser > 0){
            $query->andWhere(['imas_users.id' => $limuser]);
        }
        if($secfilter != -1  && $limuser <= 0){
            $query->andWhere(['imas_students.section' => $secfilter]);
        }
        if($hidelocked){
            $query->andWhere(['imas_students.locked' => 0]);
        }
        if(isset($timefilter)){
            $tf = time() - 60*60*$timefilter;
            $query->andWhere(['>', 'imas_users.lastaccess' ,$tf]);
        }
        if (isset($lnfilter) && $lnfilter!='') {
            $query->andWhere(['LIKE', 'imas_users.LastName', $lnfilter.'%']) ;
        }
        if ($isdiag) {
            $query->orderBy('imas_users.email, imas_users.LastName, imas_users.FirstName');
        } else if ($hassection && $usersort==0) {
            $query->orderBy('imas_students.section, imas_users.LastName, imas_users.FirstName');
        } else {
            $query->orderBy('imas_users.LastName, imas_users.FirstName');
        }

        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }
    public static function getByCourse($cId)
    {
        return static::find()->where(['courseid' => $cId])->all();
    }

    public static function findCount($courseId)
    {
        $query = new Query();
        $query->select(['count(id)'])
                 ->from('imas_students')
                ->where(['courseid' => $courseId])
                ->andWhere(['NOT LIKE','section', 'NULL']);
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;

    }


    public static function findStudentByCourseIdForOutcomes($courseId,$limuser, $secfilter, $hidelocked, $timefilter, $lnfilter, $isdiag, $hassection, $usersort)
    {

        $query = new Query();
        $query	->select(['imas_users.id', 'imas_users.SID', 'imas_users.FirstName', 'imas_users.LastName', 'imas_users.SID', 'imas_users.email', 'imas_students.section', 'imas_students.code', 'imas_students.locked', 'imas_students.timelimitmult', 'imas_students.lastaccess', 'imas_users.hasuserimg', 'imas_students.gbcomment'])
            ->from('imas_users')
            ->join(	'INNER JOIN',
                'imas_students',
                'imas_users.id = imas_students.userid'
            )
            ->where(['imas_students.courseid' => $courseId]);

        if($limuser > 0)
        {
            $query->andWhere(['imas_users.id' => $limuser]);
        }
        if ($secfilter !=-1)
        {
            $query->andWhere(['imas_students.section' => $secfilter]);
        }

        if ($hidelocked) {
            $query->andWhere(['imas_students.locked' => 0]);
        }
        if(isset($timefilter))
        {
            $tf = time() - 60*60*$timefilter;
            $query->andWhere(['>', 'imas_users.lastaccess' ,$tf]);
        }
        if (isset($lnfilter) && $lnfilter!='') {
            $query->andWhere(['LIKE', 'imas_users.LastName', $lnfilter.'%']) ;
        }
        if ($isdiag)
        {
            $query->orderBy('imas_users.email,imas_users.LastName,imas_users.FirstName');
        }else if($hassection && $usersort==0)
        {
            $query->orderBy('imas_students.section,imas_users.LastName,imas_users.FirstName' );
        }else
        {
            $query->orderBy('imas_users.LastName,imas_users.FirstName');
        }
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }

    public static function findStudentsCompleteInfo($courseId){
        $query = new Query();
        $query	->select(['imas_users.id', 'imas_users.SID', 'imas_users.FirstName', 'imas_users.LastName', 'imas_users.SID', 'imas_users.email', 'imas_students.section', 'imas_students.code', 'imas_students.locked', 'imas_students.timelimitmult', 'imas_students.lastaccess', 'imas_users.hasuserimg', 'imas_students.gbcomment', 'imas_students.gbinstrcomment'])
            ->from('imas_users')
            ->join(	'INNER JOIN',
                'imas_students',
                'imas_users.id = imas_students.userid'
            )
            ->where(['imas_students.courseid' => $courseId])
            ->orderBy('imas_users.LastName');
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }
    public  static  function updateGbComments($userId,$values, $courseId, $commentType){
        $query = Student::findOne(['userid' => $userId, 'courseid' => $courseId]);
        if($query){
            if($commentType == 'instr'){
                $query->gbinstrcomment = trim($values);
            }else{
                $query->gbcomment = trim($values);
            }
            $query->save();
        }
    }
    public static function  findStudentToUpdateComment($courseId, $useridtype, $data){
        $query = new Query();
        $query	->select(['imas_users.id'])
            ->from('imas_users')
            ->join(	'INNER JOIN',
                'imas_students',
                'imas_users.id = imas_students.userid'
            )
            ->where(['imas_students.courseid' => $courseId]);
        if($useridtype == AppConstant::NUMERIC_ZERO){
            $query->andWhere(['imas_users.SID' => $data]);
        } else if($useridtype == AppConstant::NUMERIC_ONE){
            list($last,$first) = explode(',',$data);
            $first = str_replace(' ','',$first);
            $last = str_replace(' ','',$last);
            $query->andWhere(['imas_users.FirstName' => $first]);
            $query->andWhere(['imas_users.LastName' => $last]);
        } else {
            $query->andWhere(['0']);
        }
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }
    Public static function findStudentsToList($courseId)
    {
        $query = new Query();
        $query	->select(['imas_users.id', 'imas_users.FirstName', 'imas_users.LastName'])
            ->from('imas_users')
            ->join(	'INNER JOIN',
                'imas_students',
                'imas_users.id = imas_students.userid'
            )
            ->where(['imas_students.courseid' => $courseId])
            ->orderBy('imas_users.LastName');
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }
} 