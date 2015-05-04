<?php

namespace app\controllers\course;

use app\components\AppConstant;
use app\components\AppUtility;
use app\models\AppModel;
use app\models\Blocks;
use app\models\Course;
use app\models\Assessments;
use app\models\forms\CourseSettingForm;
use app\models\forms\Links;
use app\models\Forums;
use app\models\GbScheme;
use app\models\Teacher;
use app\models\InlineText;
use app\models\Wiki;
use app\models\User;
use Yii;
use app\controllers\AppController;
use app\models\forms\DeleteCourseForm;
use yii\db\Exception;
use yii\helpers\Html;


class CourseController extends AppController
{

    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        $this->guestUserHandler();
        $cid = Yii::$app->request->get('cid');
        $assessment = Assessments::getById($cid);
        $course = Course::getById($cid);
        $forum = Forums::getByCourseId($cid);
        $wiki = Wiki::getByCourseId($cid);
        $link = Links::getByCourseId($cid);
        $block = Blocks::getById($cid);
        $inline = InlineText::getByCourseId($cid);
        //AppUtility::dump($inline);

        return $this->render('index', ['assessments' => $assessment, 'course' => $course, 'forums' => $forum, 'wiki' => $wiki, 'links' => $link, 'blocks' => $block, 'inlineText' => $inline]);
    }


    public function actionAddNewCourse()
    {
        $this->guestUserHandler();
        $model = new CourseSettingForm();

        if ($model->load(Yii::$app->request->post()))
        {
            $bodyParams = $this->getBodyParams();
            $user = $this->getAuthenticatedUser();
            $course = new Course();
            $courseId = $course->create($user, $bodyParams);
            if($courseId){
                $teacher = new Teacher();
                $teacherId = $teacher->create($user->id, $courseId);
                $gbScheme = new GbScheme();
                $gbSchemeId = $gbScheme->create($courseId);
                if($teacherId && $gbSchemeId){
                    $this->setSuccessFlash('Course added successfully. Course id: '.$courseId.' and Enrollment key: '.$bodyParams['CourseSettingForm']['enrollmentKey']);
                    $model = new CourseSettingForm();
                }
                else{
                    $this->setErrorFlash(AppConstant::SOMETHING_WENT_WRONG);
                }
            }else{
                $this->setErrorFlash(AppConstant::SOMETHING_WENT_WRONG);
            }
        }
        $this->includeCSS(["../css/courseSetting.css"]);
        $this->includeJS(["../js/courseSetting.js"]);
        return $this->renderWithData('addNewCourse', ['model' => $model]);
    }

    public function actionCourseSetting()
    {
        $this->guestUserHandler();
        $cid = Yii::$app->request->get('cid');
        $user = $this->getAuthenticatedUser();
        $course = Course::getById($cid);
        if($course)
        {
            $model = new CourseSettingForm();

            if ($model->load(Yii::$app->request->post()))
            {
                $bodyParams = $this->getBodyParams();
                $params = $bodyParams['CourseSettingForm'];
                $courseSetting['name'] = $params['courseName'];
                $courseSetting['enrollkey'] = $params['enrollmentKey'];
                $availables = isset($params['available']) ? $params['available'] : AppConstant::AVAILABLE_NOT_CHECKED_VALUE;
                $courseSetting['available'] = AppUtility::makeAvailable($availables);
                $courseSetting['copyrights'] = $params['copyCourse'];
                $courseSetting['msgset'] = $params['messageSystem'];
                $toolsets = isset($params['navigationLink']) ? $params['navigationLink'] : AppConstant::NAVIGATION_NOT_CHECKED_VALUE;
                $courseSetting['toolset']  = AppUtility::makeToolset($toolsets);
                $courseSetting['deflatepass']= $params['latePasses'];
                $courseSetting['theme']= $params['theme'];
                $courseSetting['deftime'] = AppUtility::calculateTimeDefference($bodyParams['start_time'],$bodyParams['end_time']);
                $courseSetting['end_time'] = $bodyParams['end_time'];
                $courseSetting = AppUtility::removeEmptyAttributes($courseSetting);
                $course->attributes = $courseSetting;
                $course->save();
            }
            $selectionList = AppUtility::prepareSelectedItemOfCourseSetting($course);
            $this->includeCSS(["../css/courseSetting.css"]);
            return $this->renderWithData('courseSetting', ['model' => $model, 'course' => $course, 'selectionList' => $selectionList]);

        }else{
            return $this->redirect(AppUtility::getURLFromHome('admin', 'admin/index'));
        }
    }

    public function actionDeleteCourse()
    {
        $model = new DeleteCourseForm();
        $cid = Yii::$app->request->get('cid');
        $course = Course::getById($cid);
        if($course)
        {
            $status = Course::deleteCourse($course->id);
            if($status){
                $this->setSuccessFlash('Deleted successfully.');
            }else{
                $this->setErrorFlash(AppConstant::SOMETHING_WENT_WRONG);
            }
        }else{
            $this->setErrorFlash(AppConstant::SOMETHING_WENT_WRONG);
        }
        $this->redirect(AppUtility::getURLFromHome('admin', 'admin/index'));
    }
    public function actionTransferCourse()
    {
        $this->guestUserHandler();
        $cid = Yii::$app->request->get('cid');
        $sortBy = 'FirstName';
        $order = AppConstant::ASCENDING;
        $users = User::findAllUsers($sortBy, $order);
        $course = Course::getById($cid);
        $this->includeCSS(['../css/dashboard.css']);
        return $this->renderWithData('transferCourse', array('users' => $users, 'course' => $course));
    }
    public function actionUpdateOwner()
    {
        if (Yii::$app->request->post())
        {
            $params = $this->getBodyParams();


            if(Yii::$app->user->identity->rights == 75)
            {

            }

            elseif(Yii::$app->user->identity->rights > 75)
            {
                $course = Course::getByIdandOwnerId($params['cid'], $params['oldOwner']);
                if($course)
                {
                    $course->ownerid = $params['newOwner'];
                    $course->save();

                    $teacher = Teacher::getByUserId($params['oldOwner'],$params['cid']);
                    if($teacher)
                    {
                        $teacher->delete();
                    }

                    $newTeacher = new Teacher();
                    $newTeacher->create($params['oldOwner'], $params['cid']);
                }
            }
            elseif(Yii::$app->user->identity->rights > 40)
            {
                if($params['oldOwner'] == Yii::$app->user->identity->id)
                {
                    $course = Course::getByIdandOwnerId($params['cid'], $params['oldOwner']);
                    if($course)
                    {
                        $course->ownerid = $params['newOwner'];
                        $course->save();
                    }
                }
            }
        }

        return json_encode(array('status' => '0'));
    }

    public function actionAddRemoveCourse()
    {
        $this->guestUserHandler();
        $cid = Yii::$app->request->get('cid');
        return $this->renderWithData('addRemoveCourse', ['cid' => $cid]);
    }

    public function actionGetTeachers()
    {
        $cid = Yii::$app->request->get('cid');

        $sortBy = 'FirstName';
        $order = AppConstant::ASCENDING;
//        $users = User::findAllTeachers($sortBy, $order);
        $course = Course::getById($cid);

        return json_encode($course->teachersAsArray);

    }
}