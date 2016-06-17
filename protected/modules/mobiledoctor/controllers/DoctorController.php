<?php

class DoctorController extends MobiledoctorController {

    public $defaultAction = 'view';
    private $model; // Doctor model
    private $patient;   // PatientInfo model
    private $patientMR; // PatientMR model

    public function filterUserDoctorProfileContext($filterChain) {
        $user = $this->loadUser();
        $user->userDoctorProfile = $user->getUserDoctorProfile();
        if (isset($user->userDoctorProfile) === false) {
            $redirectUrl = $this->createUrl('profile');
            $currentUrl = $this->getCurrentRequestUrl();
            $redirectUrl.='?returnUrl=' . $currentUrl;
            $this->redirect($redirectUrl);
        }
        $filterChain->run();
    }

    public function filterPatientContext($filterChain) {
        $patientId = null;
        if (isset($_GET['id'])) {
            $patientId = $_GET['id'];
        } else if (isset($_POST['patient']['id'])) {
            $patientId = $_POST['patient']['id'];
        }

        $this->loadPatientInfoById($patientId);

        //complete the running of other filters and execute the requested action.
        $filterChain->run();
    }

    /**
     * @NOTE call this method after filterUserDoctorContext.
     * @param type $filterChain
     */
    public function filterPatientCreatorContext($filterChain) {
        $patientId = null;
        if (isset($_GET['pid'])) {
            $patientId = $_GET['pid'];
        } elseif (isset($_GET['id'])) {
            $patientId = $_GET['id'];
        } else if (isset($_POST['patient']['id'])) {
            $patientId = $_POST['patient']['id'];
        }
        $creator = $this->loadUser();

        $this->loadPatientInfoByIdAndCreatorId($patientId, $creator->getId());
        $filterChain->run();
    }

    /**
     * @NOTE call this method after filterUserDoctorContext.
     * @param type $filterChain
     */
    public function filterPatientMRCreatorContext($filterChain) {
        $mrid = null;
        if (isset($_GET['mrid'])) {
            $mrid = $_GET['mrid'];
        } elseif (isset($_POST['patientbooking']['mrid'])) {
            $mrid = $_POST['patientbooking']['mrid'];
        }
        $user = $this->loadUser();
        $this->loadPatientMRByIdAndCreatorId($mrid, $user->getId());
        $filterChain->run();
    }

    /**
     * 修改医生信息
     * @param type $filterChain
     */
    public function filterUserDoctorVerified($filterChain) {
        $user = $this->loadUser();
        $doctorProfile = $user->getUserDoctorProfile();
        if (isset($doctorProfile)) {
            if ($doctorProfile->isVerified()) {
                $output = array('status' => 'no', 'error' => '您已通过实名认证,信息不可以再修改。');
                $this->renderJsonOutput($output);
            }
        }
        $filterChain->run();
    }

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST requestf           
            'userDoctorContext + profile ajaxProfile createPatient ajaxCreatePatient createPatientMR createBooking account',
            'patientContext + createPatientMR',
            'patientCreatorContext + createBooking',
            'userDoctorProfileContext + contract uploadCert',
            'userDoctorVerified + delectDoctorCert ajaxUploadCert ajaxUploadCert ajaxProfile'
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('register', 'ajaxRegister', 'mobileLogin'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('logout', 'view', 'profile', 'ajaxProfile', 'ajaxUploadCert', 'doctorInfo', 'doctorCerts', 'account', 'delectDoctorCert', 'uploadCert', 'updateDoctor', 'toSuccess', 'contract', 'ajaxContract'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionAccount() {
        //$user = $this->loadUser();
        $this->render('account');
    }

    //医生信息查询
    public function actionDoctorInfo() {
        $userId = $this->getCurrentUserId();
        $apisvc = new ApiViewDoctorInfo($userId);
        $output = $apisvc->loadApiViewData();

        $this->render('doctorInfo', array(
            'data' => $output
        ));
    }

    //异步加载医生证明
    public function actionDoctorCerts($id) {
        $apisvc = new ApiViewDoctorFiles($id);
        $output = $apisvc->loadApiViewData();
        $this->renderJsonOutput($output);
    }

    //异步删除医生证明图片
    public function actionDelectDoctorCert($id) {
        $userId = $this->getCurrentUserId();
        $userMgr = new UserManager();
        $output = $userMgr->delectDoctorCertByIdAndUserId($id, $userId);
        $this->renderJsonOutput($output);
    }

    //修改密码
    public function actionChangePassword() {
        $user = $this->getCurrentUser();
        $form = new UserPasswordForm('new');
        $form->initModel($user);
        $this->performAjaxValidation($form);
        if (isset($_POST['UserPasswordForm'])) {
            $form->attributes = $_POST['UserPasswordForm'];
            $userMgr = new UserManager();
            $success = $userMgr->doChangePassword($form);
            if ($this->isAjaxRequest()) {
                if ($success) {
                    //do anything here
                    echo CJSON::encode(array(
                        'status' => 'true'
                    ));
                    Yii::app()->end();
                } else {
                    $error = CActiveForm::validate($form);
                    if ($error != '[]') {
                        echo $error;
                    }
                    Yii::app()->end();
                }
            } else {
                if ($success) {
                    // $this->redirect(array('user/account'));
                    $this->setFlashMessage('user.password', '密码修改成功！');
                }
            }
        }
        $this->render('changePassword', array(
            'model' => $form
        ));
    }

    //个人中心
    public function actionView() {
        // var_dump(Yii::app()->user->id);exit;
        $user = $this->loadUser();  // User model
        $profile = $user->getUserDoctorProfile();   // UserDoctorProfile model
        $data = new stdClass();
        $data->id = $user->getId();
        $data->mobile = $user->getMobile();
        if (isset($profile)) {
            $data->name = $profile->getName();
            //是否是签约医生
            $data->verified = $profile->isVerified();
        } else {
            $data->name = $user->getMobile();
            $data->verified = false;
        }

        $this->render('view', array(
            'user' => $data
        ));
    }

    //进入医生申请签约页面
    public function actionContract() {
        $user = $this->loadUser();
        $doctorProfile = $user->getUserDoctorProfile();
        $returnUrl = $this->getReturnUrl("#success");
        $form = new DoctorContractForm();
        $form->initModel($doctorProfile);
        $this->render('contract', array(
            'model' => $form,
            'returnUrl' => $returnUrl,
        ));
    }

    public function actionAjaxContract() {
        //需要发送电邮的数据
        $data = new stdClass();
        $user = $this->loadUser();
        $doctorProfile = $user->getUserDoctorProfile();
        $data->oldPreferredPatient = $doctorProfile->preferred_patient;
        $output = array('status' => 'no');
        $form = new DoctorContractForm();
        $form->initModel($doctorProfile);
        $data->scenario = $form->scenario;
        if (isset($_POST['DoctorContractForm'])) {
            $values = $_POST['DoctorContractForm'];
            $form->setAttributes($values);
            if ($form->validate()) {
                $doctorProfile->setAttributes($form->attributes);
                if ($doctorProfile->save(true, array('preferred_patient', 'date_contracted', 'date_updated'))) {
                    $data->dateUpdated = date('Y-m-d H:i:s');
                    $data->doctorProfile = $doctorProfile;
                    //判断信息是修改还是保存 发送电邮
                    $emailMgr = new EmailManager();
                    $emailMgr->sendEmailDoctorUpateContract($data);
                    $output['status'] = 'ok';
                    $output['salesOrder']['id'] = $doctorProfile->getId();
                } else {
                    $output['errors'] = $doctorProfile->getErrors();
                }
            } else {
                $output['errors'] = $form->getErrors();
            }
        } else {
            $output['error'] = 'invalid request';
        }
        $this->renderJsonOutput($output);
    }

    public function actionAjaxUploadCert() {
        $output = array('status' => 'no');
        if (isset($_POST['doctor'])) {
            $values = $_POST['doctor'];
            $userMgr = new UserManager();
            if (isset($values['id']) === false) {
                $this->renderJsonOutput($values['id']);
                $output['status'] = 'no';
                $output['error'] = 'invalid parameters';
                $this->renderJsonOutput($output);
            }
            $userId = $this->getCurrentUserId();
            $ret = $userMgr->createUserDoctorCert($userId);
            if (isset($ret['error'])) {
                $output['status'] = 'no';
                $output['error'] = $ret['error'];
                $output['file'] = '';
            } else {
                // create file output.
                $fileModel = $ret['filemodel'];
                $data = new stdClass();
                $data->id = $fileModel->getId();
                $data->userId = $fileModel->getUserId();
                $data->fileUrl = $fileModel->getAbsFileUrl();
                $data->tnUrl = $fileModel->getAbsThumbnailUrl();
                $data->deleteUrl = $this->createUrl('doctor/deleteCert', array('id' => $fileModel->getId()));
                $output['status'] = 'ok';
                $output['file'] = $data;
            }
        } else {
            $output['error'] = 'invalid parameters.';
        }
        // android 插件
        if (isset($_POST['plugin'])) {
            echo CJSON::encode($output);
            Yii::app()->end(200, true); //结束 返回200
        } else {
            $this->renderJsonOutput($output);
        }
    }

    //上传成功页面跳转
    public function actionToSuccess() {
        $this->render('_success');
    }

    /**
     * 医生上传认证全部成功 发送电邮提醒
     */
    public function actionSendEmailForCert() {
        $user = $this->loadUser();
        $doctorProfile = $user->getUserDoctorProfile();
        $emailMgr = new EmailManager();
        
    }

    public function actionAjaxProfile() {
        $output = array('status' => 'no');
        if (isset($_POST['doctor'])) {
            $values = $_POST['doctor'];
            $form = new UserDoctorProfileForm();
            $form->setAttributes($values, true);
            $form->initModel();
            if ($form->validate() === false) {
                $output['status'] = 'no';
                $output['errors'] = $form->getErrors();
                $this->renderJsonOutput($output);
            }
            $regionMgr = new RegionManager();
            $user = $this->loadUser();
            $userId = $user->getId();
            $doctorProfile = $user->getUserDoctorProfile();
            if (is_null($doctorProfile)) {
                $doctorProfile = new UserDoctorProfile();
                $doctorProfile->setMobile($user->username);
            }
            $attributes = $form->getSafeAttributes();
            $doctorProfile->setAttributes($attributes, true);
            $doctorProfile->user_id = $userId;
            // UserDoctorProfile.state_name.
            $state = $regionMgr->loadRegionStateById($doctorProfile->state_id);
            if (isset($state)) {
                $doctorProfile->state_name = $state->getName();
            }
            // UserDoctorProflie.city_name;
            $city = $regionMgr->loadRegionCityById($doctorProfile->city_id);
            if (isset($city)) {
                $doctorProfile->city_name = $city->getName();
            }
            if ($doctorProfile->save()) {
                //信息保存成功 电邮提示
                $emailMgr = new EmailManager();
                $emailMgr->sendEmailDoctorUpdateInfo($doctorProfile);
                $output['status'] = 'ok';
                $output['doctor']['id'] = $doctorProfile->getUserId();
                $output['doctor']['profileId'] = $doctorProfile->getId();
            } else {
                $output['status'] = 'no';
                $output['errors'] = $doctorProfile->getErrors();
            }
        }
        $this->renderJsonOutput($output);
    }

    public function actionProfile() {
        $user = $this->loadUser();
        $doctorProfile = $user->getUserDoctorProfile();
        $form = new UserDoctorProfileForm();
        $form->initModel($doctorProfile);
        $form->terms = 1;
        $returnUrl = $this->getReturnUrl("#success");

        $this->render('profile', array(
            'model' => $form,
            'returnUrl' => $returnUrl
        ));
    }

    /**
     * @DELETE
     */
    public function actionCreatePatient() {
        $this->redirect(array('patient/create'));
    }

    public function actionLogout() {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->user->loginUrl);
    }

    /**
     * 手机用户登录
     */
    public function actionMobileLogin() {
        $user = $this->getCurrentUser();
        //已登陆 跳转至主页
        if (isset($user)) {
            $this->redirect(array('view'));
        }
        $form = new UserDoctorMobileLoginForm();
        $form->role = StatCode::USER_ROLE_DOCTOR;
        if (isset($_POST['UserDoctorMobileLoginForm'])) {
            $values = $_POST['UserDoctorMobileLoginForm'];
            $form->setAttributes($values, true);
            $userMgr = new UserManager();
            $isSuccess = $userMgr->mobileLogin($form);
            //登录成功 页面跳转
            if ($isSuccess) {
                $this->redirect(array('view'));
            }
        }
        //失败 则返回登录页面
        $this->render("mobileLogin", array(
            'model' => $form
        ));
    }

    /**
     * 医生补全图片
     */
    public function actionUploadCert() {
        $id = $this->getCurrentUserId();
        $viewFile = 'uploadCert';
        if ($this->isUserAgentIOS()) {
            $viewFile .= 'Ios';
        } else {
            $viewFile .= 'Android';
        }
        $this->render($viewFile, array(
            'output' => array('id' => $id)
        ));
    }

    /**
     * 主页进入修改医生信息页面
     */
    public function actionUpdateDoctor() {
        $user = $this->loadUser();
        $doctorProfile = $user->getUserDoctorProfile();
        $form = new UserDoctorProfileForm();
        $form->initModel($doctorProfile);
        $form->terms = 1;
        $this->render('updateDoctor', array(
            'model' => $form,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Doctor the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        if ($this->model === null) {
            $this->model = Doctor::model()->getById($id);
            if ($this->model === null)
                throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $this->model;
    }

    protected function registerDoctor(DoctorForm $form) {
        if (isset($_POST['DoctorForm'])) {
            $values = $_POST['DoctorForm'];
            $form->setAttributes($values);
            $form->hp_dept_name = $form->faculty;
            //$form->hospital_id = null;
            $doctorMgr = new DoctorManager();
            //if ($doctorMgr->createDoctor($form, false)) {   // do not check verify_code.
            if ($doctorMgr->createDoctor($form)) {
                // Send email to inform admin.
                $doctorId = $form->getId();
                $with = array('doctorCerts', 'doctorHospital', 'doctorHpDept', 'doctorCity');
                $idoctor = $doctorMgr->loadIDoctor($doctorId, $with);

                if (isset($idoctor)) {
                    $emailMgr = new EmailManager();
                    $emailMgr->sendEmailDoctorRegister($idoctor);
                }
// store successful message id in session.
                $this->setFlashMessage("doctor.success", "success");
                $this->refresh(true);     // terminate and refresh the current page.
            } else {
                
            }
        }
    }

    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'user-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    private function createDoctorTestData() {
        $data = array(
            'fullname' => '小明',
            'hospital_name' => '北京医院',
            'hp_dept_name' => '肿瘤科',
            'state_id' => '1',
            'city_id' => '1',
            'medical_title' => '1',
            'academic_title' => '1',
            'terms' => 1,
        );
        return $data;
    }

}
