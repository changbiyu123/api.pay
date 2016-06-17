<?php

class DoctorController extends MobileController {

    private $model;

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
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
                'actions' => array('register', 'view', 'login', 'createPatient', 'profile', 'createPatientMR', 'createBooking'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array(''),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionView($id) {
        $doctorMgr = new DoctorManager();
        $idoctor = $doctorMgr->loadIDoctor($id);
        $this->render('view', array(
            'idoctor' => $idoctor
        ));
    }

    public function actionRegister() {
        $form = new DoctorForm("register");
        $form->initModel();

        $this->performAjaxValidation($form);

        $this->registerDoctor($form);

        $this->render('register', array(
            'model' => $form
        ));
    }

    public function actionProfile() {
        $doctorId = $this->getCurrentUserId();

        $this->render('profile');
    }

    public function actionLogin() {
        $this->render("login");
    }

    public function actionCreatePatient() {
        $doctorId = $this->getCurrentUserId();

        $this->render("createPatient");
    }

    public function actionCreatePatientMR($patient = null) {

        $this->render('createPatientMR');
    }

    public function actionCreateBooking($mrid = null) {
        $this->render('createBooking');
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

}
