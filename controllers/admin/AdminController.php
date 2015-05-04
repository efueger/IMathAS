<?php

namespace app\controllers\admin;

use app\controllers\AppController;
use app\controllers\PermissionViolationException;
use app\models\_base\BaseImasDiags;
use app\models\forms\CourseSettingForm;
use Yii;
use app\models\forms\AddNewUserForm;
use app\components\AppUtility;
use app\models\User;
use app\components\AppConstant;
use app\models\forms\AdminDiagnosticForm;

class AdminController extends AppController
{
    public function actionIndex()
    {
        $this->guestUserHandler();
        $sortBy = 'FirstName';
        $order = AppConstant::ASCENDING;
        $users = User::findAllUser($sortBy, $order);

        $sortBy = 'name';
        $order = AppConstant::ASCENDING;
        $courseData = CourseSettingForm::findCourseData($sortBy, $order);

        $this->includeCSS(['../css/dashboard.css']);

        return $this->renderWithData('index', array('users' => $users, 'courseData' => $courseData));
    }

    public function actionAddNewUser()
    {
        $this->guestUserHandler();
        $model = new AddNewUserForm();
        if ($model->load(Yii::$app->request->post())){

            $params = $this->getBodyParams();
            $params = $params['AddNewUserForm'];
            $params['SID'] = $params['username'];
            $params['hideonpostswidget'] = AppConstant::ZERO_VALUE;
            $params['password'] = AppUtility::passwordHash($params['password']);

            $user = new User();
            $user->attributes = $params;
            $user->save();

            $this->setSuccessFlash(AppConstant::ADD_NEW_USER);
        }
        return $this->renderWithData('addNewUser', ['model' => $model,]);
    }

    public function actionAdminDiagnostic()
    {
            $this->guestUserHandler();
            $model = new AdminDiagnosticForm();

            if ($model->load(Yii::$app->request->post()))
            {
                $params = $this->getBodyParams();
                $user = $this->getAuthenticatedUser();

                $params = $params['AdminDiagnosticForm'];
                $params['ownerid'] = $user->SID;
                $params['name'] = $params['DiagnosticName'];
                $params['term'] = $params['TermDesignator'];
                $diag = new BaseImasDiags();
                $diag->attributes = $params;
                $diag->save();
            }
            return $this->renderWithData('adminDiagnostic',['model'=>$model]);
    }
}