<?php

namespace app\models;

use app\components\AppConstant;
use app\components\AppUtility;
use app\models\_base\BaseImasStudents;
use app\controllers\AppController;
use yii\db\Query;

class Student extends BaseImasStudents
{

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

    public static function getByUserIdentity($uid, $courseid)
    {
        return static::findAll(['userid' => $uid, 'courseid' => $courseid]);
    }

    public function createNewStudent($userId, $cid, $params)
    {
        $this->userid = $userId;
        $this->courseid = $cid;
        $this->section = empty($params['section']) ? null : $params['section'];
        $this->code = empty($params['code']) ? null : $params['code'];
        if (isset($params['latepass'])) {
            $this->latepass = $params['latepass'];
        }
        $this->save();
        return Student::find()->max('id');
    }

    public static function findByCid($cId)
    {
        return static::findAll(['courseid' => $cId]);
    }

    public function insertNewStudent($studentId, $courseId, $section)
    {
        $this->userid = $studentId;
        $this->courseid = $courseId;
        $this->section = empty($section) ? null : $section;
        $this->save();
    }

    public static function updateSectionAndCodeValue($section, $userid, $code, $cid, $params = null)
    {
        $student = Student::findOne(['userid' => $userid, 'courseid' => $cid]);
        $student->section = $section;
        $student->code = $code;
        if($params != null)
        {
           if($params['locked'] == AppConstant::NUMERIC_ONE) {
               $student->locked = AppController::dateToString();
           }
            else{
                $student->locked = AppConstant::NUMERIC_ZERO;
            }
            $student->hidefromcourselist = $params['hidefromcourselist'];

            if ($params['timelimitmult'] != AppConstant::NUMERIC_ZERO) {
                $student->timelimitmult = $params['timelimitmult'];
            }
        }
        $student->save();
    }

    public static function updateLatepasses($latepass, $userid, $cid)
    {
        $student = Student::findOne(['userid' => $userid, 'courseid' => $cid]);
        $student->latepass = $latepass;
        $student->save();
    }

    public static function findByCourseId($cId, $sortBy, $order)
    {
        return static::find()->where(['courseid' => $cId])->groupBy('section')->orderBy([$sortBy => $order])->all();
    }

    public static function updateLocked($userid, $courseid)
    {
        $student = Student::findOne(['userid' => $userid,'courseid' => $courseid]);
        $student->locked = AppController::dateToString();
        $student->save();
    }

    public static function deleteStudent($userid, $courseid)
    {
        $student = Student::findOne(['userid' => $userid, 'courseid' => $courseid]);
        $student->delete();
    }

    public function assignSectionAndCode($newEntry, $id)
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
        $student = Student::findOne(['userid' => $studentId, 'courseid' => $courseId]);
        if ($params['lockOrUnlock'] == AppConstant::NUMERIC_ONE) {
            $student->locked = AppConstant::NUMERIC_ZERO;
            $student->save();
         }
        if($params['lockOrUnlock'] == AppConstant::NUMERIC_ZERO)
        {
            $student->locked = AppController::dateToString();
            $student->save();
        }
    }

    public static function reduceLatepasses($userid, $cid, $n)
    {
        $student = Student::findOne(['userid' => $userid, 'courseid' => $cid]);
        if ($student->latepass > $n) {
            $student->latepass = $student->latepass - $n;
        } else {
            $student->latepass = AppConstant::NUMERIC_ZERO;
        }
        $student->save();
    }

    public static function updateHideFromCourseList($userId, $courseId)
    {
        $student = Student::findOne(['userid' => $userId, 'courseid' => $courseId]);
        if ($student) {
            if ($student->hidefromcourselist == AppConstant::NUMERIC_ZERO) {
                $student->hidefromcourselist = AppConstant::NUMERIC_ONE;
            } else {
                $student->hidefromcourselist = AppConstant::NUMERIC_ZERO;
            }
            $student->save();
        }
    }

    public static function findHiddenCourse($userId)
    {
        return static::find()->where(['userid' => $userId])->andWhere(['NOT LIKE', 'hidefromcourselist', 0])->all();
    }

    public static function findDistinctSection($courseId)
    {
        return static::find()->select('section')->distinct()->where(['courseid' => $courseId])->orderBy('section')->all();
    }

    public static function findStudentByCourseId($courseId, $limuser, $secfilter, $hidelocked, $timefilter, $lnfilter, $isdiag, $hassection, $usersort)
    {
        $query = "SELECT imas_users.id,imas_users.SID,imas_users.FirstName,imas_users.LastName,imas_users.SID,imas_users.email,imas_students.section,imas_students.code,imas_students.locked,imas_students.timelimitmult,imas_students.lastaccess,imas_users.hasuserimg,imas_students.gbcomment ";
        $query .= "FROM imas_users,imas_students WHERE imas_users.id=imas_students.userid AND imas_students.courseid=$courseId ";
        if ($limuser>0) { $query .= "AND imas_users.id=$limuser ";}
        if ($secfilter!=-1 && $limuser<=0) {
            $query .= "AND imas_students.section='$secfilter' ";
        }
        if ($hidelocked) {
            $query .= "AND imas_students.locked=0 ";
        }
        if (isset($timefilter)) {
            $tf = time() - 60*60*$timefilter;
            $query .= "AND imas_users.lastaccess>$tf ";
        }
        if (isset($lnfilter) && $lnfilter!='') {
            $query .= "AND imas_users.LastName LIKE '$lnfilter%' ";
        }
        if ($isdiag) {
            $query .= "ORDER BY imas_users.email,imas_users.LastName,imas_users.FirstName";
        } else if ($hassection && $usersort==0) {
            $query .= "ORDER BY imas_students.section,imas_users.LastName,imas_users.FirstName";
        } else {
            $query .= "ORDER BY imas_users.LastName,imas_users.FirstName";
        }
        $data = \Yii::$app->db->createCommand($query)->queryAll();
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
            ->andWhere(['NOT LIKE', 'section', 'NULL']);
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }

    public static function findStudentByCourseIdForOutcomes($courseId, $limuser, $secfilter, $hidelocked, $timefilter, $lnfilter, $isdiag, $hassection, $usersort)
    {

        $query = new Query();
        $query->select(['imas_users.id', 'imas_users.SID', 'imas_users.FirstName', 'imas_users.LastName', 'imas_users.SID', 'imas_users.email', 'imas_students.section', 'imas_students.code', 'imas_students.locked', 'imas_students.timelimitmult', 'imas_students.lastaccess', 'imas_users.hasuserimg', 'imas_students.gbcomment'])
            ->from('imas_users')
            ->join('INNER JOIN',
                'imas_students',
                'imas_users.id = imas_students.userid'
            )
            ->where(['imas_students.courseid' => $courseId]);

        if ($limuser > AppConstant::NUMERIC_ZERO) {
            $query->andWhere(['imas_users.id' => $limuser]);
        }
        if ($secfilter != AppConstant::NUMERIC_NEGATIVE_ONE) {
            $query->andWhere(['imas_students.section' => $secfilter]);
        }

        if ($hidelocked) {
            $query->andWhere(['imas_students.locked' => 0]);
        }
        if (isset($timefilter)) {
            $tf = time() - AppConstant::MINUTE * AppConstant::SECONDS * $timefilter;
            $query->andWhere(['>', 'imas_users.lastaccess', $tf]);
        }
        if (isset($lnfilter) && $lnfilter != '') {
            $query->andWhere(['LIKE', 'imas_users.LastName', $lnfilter . '%']);
        }
        if ($isdiag) {
            $query->orderBy('imas_users.email,imas_users.LastName,imas_users.FirstName');
        } else if ($hassection && $usersort == AppConstant::NUMERIC_ZERO) {
            $query->orderBy('imas_students.section,imas_users.LastName,imas_users.FirstName');
        } else {
            $query->orderBy('imas_users.LastName,imas_users.FirstName');
        }
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }

    public static function findStudentsCompleteInfo($courseId)
    {
        $query = new Query();
        $query->select(['imas_users.id', 'imas_users.SID', 'imas_users.FirstName', 'imas_users.LastName', 'imas_users.SID', 'imas_users.email', 'imas_students.section', 'imas_students.code', 'imas_students.locked', 'imas_students.timelimitmult', 'imas_students.lastaccess', 'imas_users.hasuserimg', 'imas_students.gbcomment', 'imas_students.gbinstrcomment'])
            ->from('imas_users')
            ->join('INNER JOIN',
                'imas_students',
                'imas_users.id = imas_students.userid'
            )
            ->where(['imas_students.courseid' => $courseId])
            ->orderBy('imas_users.LastName');
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }

    public static function updateGbComments($userId, $values, $courseId, $commentType)
    {
        $query = Student::findOne(['userid' => $userId, 'courseid' => $courseId]);
        if ($query) {
            if ($commentType == 'instr') {
                $query->gbinstrcomment = trim($values);
            } else {
                $query->gbcomment = trim($values);
            }
            $query->save();
        }
    }

    public static function  findStudentToUpdateComment($courseId, $useridtype, $data)
    {
        $query = new Query();
        $query->select(['imas_users.id'])
            ->from('imas_users')
            ->join('INNER JOIN',
                'imas_students',
                'imas_users.id = imas_students.userid'
            )
            ->where(['imas_students.courseid' => $courseId]);
        if ($useridtype == AppConstant::NUMERIC_ZERO) {
            $query->andWhere(['imas_users.SID' => $data]);
        } else if ($useridtype == AppConstant::NUMERIC_ONE) {
            list($last, $first) = explode(',', $data);
            $first = str_replace(' ', '', $first);
            $last = str_replace(' ', '', $last);
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
        $query->select(['imas_users.id', 'imas_users.FirstName', 'imas_users.LastName'])
            ->from('imas_users')
            ->join('INNER JOIN',
                'imas_students',
                'imas_users.id = imas_students.userid'
            )
            ->where(['imas_students.courseid' => $courseId])
            ->orderBy('imas_users.LastName');
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }

    public static function setLastAccess($studentid, $now)
    {
        $studentData = Student::findOne(['id' => $studentid]);
        if ($studentData) {
            $studentData->lastaccess = $now;
            $studentData->save();
        }
    }

    public static function getById($studentId)
    {
        return Student::find()->where(['id' => $studentId])->one();
    }

    public static function findStudentToUpdateFeedbackAndScore($data, $params, $course, $usercol)
    {

        $query = new Query();
        $query->select(['imas_users.id', 'imas_users.FirstName', 'imas_users.LastName'])
            ->from('imas_users')
            ->join('INNER JOIN',
                'imas_students',
                'imas_users.id = imas_students.userid'
            )
            ->where(['imas_students.courseid' => $course->id])
            ->orderBy('imas_users.LastName');
        if ($params['userIdType'] == AppConstant::NUMERIC_ZERO) {
            $data[$usercol] = str_replace("'", "\\'", trim($data[$usercol]));
            $query->andWhere(['imas_users.SID' => $data[$usercol]]);
        } else if ($params['userIdType'] == AppConstant::NUMERIC_ONE) {
            list($last, $first) = explode(',', $data[$usercol]);
            $first = str_replace("'", "\\'", trim($first));
            $last = str_replace("'", "\\'", trim($last));
            $query->andWhere(['imas_users.FirstName' => $first]);
            $query->andWhere(['imas_users.LastName' => $last]);
        } else {
            $query .= "0";
        }
        $command = $query->createCommand();
        $data = $command->queryOne();
        return $data;
    }

    public static function getByCourseAndGrades($courseId, $grades, $hassection, $sortorder)
    {

        $query = new Query();
        $query->select(['imas_users.id', 'imas_users.LastName', 'imas_users.FirstName', 'imas_students.section', 'imas_students.locked'])
            ->from('imas_users')
            ->join('INNER JOIN',
                'imas_students',
                'imas_users.id = imas_students.userid'
            );

        if ($grades != 'all') {
            $query->where(['imas_students.courseid' => $courseId])
                ->andWhere(['imas_users.id' => $grades]);
        } else {

            $query->where(['imas_students.courseid' => $courseId]);
        }

        if ($hassection && $sortorder == "sec") {
            $query->orderBy('imas_students.section,imas_users.LastName,imas_users.FirstName');

        } else {
            $query->orderBy('imas_users.LastName,imas_users.FirstName');
        }
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;

    }

    public static function getByCourseAndGradesToAllStudents($courseId, $grades, $hassection, $sortorder)
    {
        $query = new Query();
        $query->select(['imas_users.id', 'imas_users.LastName', 'imas_users.FirstName', 'imas_students.section', 'imas_students.locked'])
            ->from('imas_users')
            ->join('INNER JOIN',
                'imas_students',
                'imas_users.id = imas_students.userid'
            )
            ->where(['imas_students.courseid' => $courseId]);
        if ($hassection && $sortorder == "sec") {
            $query->orderBy('imas_students.section,imas_users.LastName,imas_users.FirstName');

        } else {
            $query->orderBy('imas_users.LastName,imas_users.FirstName');
        }
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }

    /*Query To Show Courses available For Students in My classes drop-down*/
    public static function  getMyClassesForStudent($userId)
    {
        $items = [];
        $Students = static::findAll(['userid' => $userId]);
        foreach ($Students as $singleStudent) {
            $items[] = ['label' => $singleStudent->course['name'], 'url' => '../../course/course/index?cid=' . $singleStudent['courseid']];
        }
        return $items;
    }

    public static function getStudentCountUsingCourseIdAndLockedStudent($courseId, $secfilter)
    {
        $query = new Query();
        $query->select(['id'])
            ->from('imas_students')
            ->where(['imas_students.courseid' => $courseId])
            ->andWhere(['locked' => '0']);
        if ($secfilter != AppConstant::NUMERIC_NEGATIVE_ONE) {
            $query->andWhere(['imas_students.section' => $secfilter]);
        }
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;

    }

    public static function getByUserIdUsingAssessmentSessionJoin($courseId, $assessmentId, $secfilter)
    {
        $query = "SELECT ims.userid FROM imas_students AS ims LEFT JOIN imas_assessment_sessions AS ias ON ims.userid=ias.userid AND ias.assessmentid='$assessmentId'";
        $query .= "WHERE ias.id IS NULL AND ims.courseid='$courseId' AND ims.locked=0 ";
        if ($secfilter != AppConstant::NUMERIC_NEGATIVE_ONE) {
            $query .= " AND ims.section='$secfilter' ";
        }
        $data = \Yii::$app->db->createCommand($query)->queryAll();
        return $data;
    }

    public static function deleteByUserId($userId)
    {
        $students = Student::find()->where(['userid' => $userId])->all();
        foreach ($students as $student) {
            $student->delete();
        }
    }

    public static function getFNameAndLNameByJoin($date)
    {
        $query = "SELECT g.name,u.LastName,COUNT(DISTINCT s.id) FROM imas_students AS s JOIN imas_courses AS t ";
        $query .= "ON s.courseid=t.id AND s.lastaccess>$date  JOIN imas_users as u  ";
        $query .= "ON u.id=t.ownerid JOIN imas_groups AS g ON g.id=u.groupid GROUP BY u.id ORDER BY g.name";
        return \Yii::$app->db->createCommand($query)->queryAll();
    }

    public static function getstuDetails($start, $now, $end)
    {
        $query = "SELECT g.name,u.LastName,u.FirstName,c.id,c.name AS cname, COUNT(DISTINCT s.id) FROM imas_students AS s JOIN imas_teachers AS t ";
        $query .= "ON s.courseid=t.courseid AND s.lastaccess > {$start} ";
        if ($end != $now) {
            $query .= "AND s.lastaccess<$end ";
        }
        $query .= "JOIN imas_courses AS c ON t.courseid=c.id ";
        $query .= "JOIN imas_users as u ";
        $query .= "ON u.id=t.userid JOIN imas_groups AS g ON g.id=u.groupid GROUP BY u.id,c.id ORDER BY g.name,u.LastName,u.FirstName,c.name";
        return \Yii::$app->db->createCommand($query)->queryAll();

    }

    public static function deleteByCourseId($courseId)
    {
        $courseData = Student::findOne(['courseid', $courseId]);
        if ($courseData) {
            $courseData->delete();
        }
    }

    public function insertByUserData($userId, $pcid, $teacher)
    {
        $this->userid = $userId;
        $this->courseid = $pcid;
        $this->section = $teacher;
        $this->save();
    }

    public static function updateLatePass($n, $userid, $cid)
    {
        $query = "UPDATE imas_students SET latepass=latepass+$n WHERE userid='$userid' AND courseid='$cid'";
        \Yii::$app->db->createCommand($query)->execute();
    }
    public static function updateLatePassById($userId,$courseId)
    {
        $query = "UPDATE imas_students SET latepass=latepass-1 WHERE userid='$userId' AND courseid='$courseId' AND latepass>0";
        return \Yii::$app->db->createCommand($query)->execute();
    }

    public static function getLatePassById($userId, $courseId)
    {
        return Student::find()->select('latepass')->where(['userid' => $userId, 'courseid' => $courseId])->all();
    }

    public static function getLatePass($userId, $courseId)
    {
        return Student::find()->select('latepass')->where(['courseid' => $courseId, 'userid' => $userId])->one();
    }

    public static function updateStudentDataFromException($n,$userId,$courseId)
    {
        $query = "UPDATE imas_students SET latepass = CASE WHEN latepass>$n THEN latepass-$n ELSE 0 END WHERE userid='{$userId}' AND courseid='$courseId'";
        \Yii::$app->db->createCommand($query)->execute();
    }

    public function insertUIdCId($userId, $pcid)
    {
        $this->userid = $userId;
        $this->courseid = $pcid;
        $this->save();
    }

    public static function getStudentByUserId($userid)
    {
        $query = "SELECT ic.id,ic.name FROM imas_courses AS ic JOIN imas_students AS it ON ic.id=it.courseid WHERE it.userid='$userid' ORDER BY ic.name";
        return \Yii::$app->db->createCommand($query)->queryAll();
    }

    public static function getStudentData($userId, $courseId)
    {
        $query = new Query();
        $query->select(['id','locked','timelimitmult', 'section'])
            ->from('imas_students')
            ->where(['userid' => $userId]);
            $query->andWhere(['courseid' => $courseId]);
        $command = $query->createCommand();
        $data = $command->queryOne();
        return $data;
    }
}

