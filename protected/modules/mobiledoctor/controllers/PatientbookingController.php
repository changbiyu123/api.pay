<?php

class PatientbookingController extends MobiledoctorController {

    private $model; // PatientBooking model.
    private $patient;   // PatientInfo model.

    public function filterPatientBookingContext($filterChain) {
        $bookingId = null;
        if (isset($_GET['id'])) {
            $bookingId = $_GET['id'];
        } elseif (isset($_POST['booking']['id'])) {
            $bookingId = $_POST['booking']['id'];
        }
        $this->loadModel($bookingId);
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
        } else if (isset($_POST['booking']['patient_id'])) {
            $patientId = $_POST['booking']['patient_id'];
        }

        $creator = $this->loadUser();

        $this->loadPatientInfoByIdAndCreatorId($patientId, $creator->getId());
        $filterChain->run();
    }

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
            'userDoctorContext + create',
            'patientCreatorContext + create'
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
                'actions' => array(''),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('view', 'create', 'ajaxCreate', 'update', 'list', 'doctorPatientBookingList', 'doctorPatientBooking'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionView($id) {
        //    echo 'View Patient Booking - ' . $id;
        //    $booking = $this->model;
        $userId = $this->getCurrentUserId();
        $apiSvc = new ApiViewPatientBooking($id, $userId);
        $output = $apiSvc->loadApiViewData();
        $this->render('view', array(
            'data' => $output
        ));
    }

    //查询创建者的签约信息
    public function actionList($page = 1) {
        //$this->headerUTF8();
        //是否手机app
        $json = false;
        $userId = $this->getCurrentUserId();
        $pagesize = 100;
        //service层
        $apisvc = new ApiViewDoctorPatientBookingList($userId, $pagesize, $page);
        //调用父类方法将数据返回
        $output = $apisvc->loadApiViewData();
        $dataCount = $apisvc->loadCount();
        if ($json) {
            $this->renderJsonOutput($output);
        } else {
            $this->render('bookinglist', array(
                'data' => $output, 'dataCount' => $dataCount
            ));
        }
    }

    //查询预约该医生的预约列表
    public function actionDoctorPatientBookingList($page = 1) {
        $pagesize = 100;
        $doctorId = $this->getCurrentUserId();
        $apisvc = new ApiViewPatientBookingListForDoctor($doctorId, $pagesize, $page);
        //调用父类方法将数据返回
        $output = $apisvc->loadApiViewData();
        $dataCount = $apisvc->loadCount();
        $this->render('doctorPatientBookingList', array(
            'data' => $output, 'dataCount' => $dataCount
        ));
    }

    //查询该医生的预约详情
    public function actionDoctorPatientBooking($id) {
        $doctorId = $this->getCurrentUserId();
        $apiSvc = new ApiViewPatientBookingForDoctor($id, $doctorId);
        $output = $apiSvc->loadApiViewData();
        $this->render('doctorPatientBookingView', array(
            'data' => $output
        ));
    }

    public function actionCreate() {
        $patient = $this->patient;

        //$userAgent=Yii::app()->request->getQuery('userAgent', 'weixin');
        $form = new PatientBookingForm();
        $form->initModel();
        //判断数据来源
        if ($this->isUserAgentWeixin()) {
            $form->user_agent = StatCode::USER_AGENT_WEBSITE_WEXIN;
        } else {
            $form->user_agent = StatCode::USER_AGENT_WEBSITE_MOBILEWEB;
        }
        $form->setPatientId($patient->getId());
        $this->render('create', array(
            'model' => $form
        ));
    }

    public function actionAjaxCreate() {
        $output = array('status' => 'no');
        if (isset($_POST['booking'])) {
            $values = $_POST['booking'];
            $patientId = isset($values['patient_id']) ? $values['patient_id'] : null;
            $userId = $this->getCurrentUserId();
            $form = new PatientBookingForm();
            $form->setAttributes($values, true);
            $form->setPatientId($patientId);
            $form->setCreatorId($userId);
            $form->setStatusNew();
            try {
                if ($form->validate() === false) {
                    $output['errors'] = $form->getErrors();
                    throw new CException('error saving data.');
                }
                $patientBooking = new PatientBooking();
                $patientBooking->setAttributes($form->attributes, true);
                if ($patientBooking->save() === false) {
                    $output['errors'] = $patientBooking->getErrors();
                    throw new CException('error saving data.');
                }
                //预约单保存成功  生成一张支付单
                $orderMgr = new OrderManager();
                $salesOrder = $orderMgr->createSalesOrder($patientBooking);
                if ($salesOrder->hasErrors() === false) {
                    $output['status'] = 'ok';
                    $output['salesOrderRefNo'] = $salesOrder->getRefNo();
                    $output['booking']['id'] = $patientBooking->getId();
                    $output['booking']['patientId'] = $patientBooking->getPatientId();
                    //发送提示短信
                    $this->sendSmsToCreator($patientBooking);
                } else {
                    $output['errors'] = $salesOrder->getErrors();
                    throw new CException('error saving data.');
                }
            } catch (CException $cex) {
                $output['status'] = 'no';
                //$output['error'] = 'invalid request';
            }
        }
        $this->renderJsonOutput($output);
    }

    //保存支付信息
    public function initSalesOrder(PatientBooking $book) {
        $model = new stdClass();
        $model->refNo = $book->getRefNo();
        $model->id = $book->getId();
        $model->bk_type = StatCode::TRANS_TYPE_PB;
        //$model->bkType = 'PatientBooking';

        $model->user_id = $book->creator_id;
        if ($book->getTravelType(false) == StatCode::BK_TRAVELTYPE_PATIENT_GO) {
            $model->subject = '预约金';
            $model->order_type = SalesOrder::ORDER_TYPE_DEPOSIT;
        } else {
            $model->subject = '服务费';
            $model->order_type = SalesOrder::ORDER_TYPE_SERVICE;
        }
        $model->description = '预约号:' . $book->getRefNo() . '。' . $book->getTravelType(true) . '所支付的' . $model->subject . '!';
        $model->amount = SalesOrder::ORDER_AMOUNT_DEPOSIT;

        $orderMgr = new OrderManager();
        $order = $orderMgr->initSalesOrder($model);
        return $order;
    }

    public function sendSmsToCreator($patientBooking) {
        $user = $this->getCurrentUser();
        $mobile = $user->getUsername();
        $smsMgr = new SmsManager();
        $data = new stdClass();
        $data->refno = $patientBooking->getRefNo();
        $doctor = $patientBooking->getDoctor();
        if (isset($doctor)) {
            $name = $doctor->name;
        } else {
            $name = '';
        }
        $data->expertBooked = $name;
        //发送提示的信息
        $smsMgr->sendSmsBookingSubmit($mobile, $data);
    }

    public function loadModel($id) {
        if (is_null($this->model)) {
            $this->model = PatientBooking::model()->getById($id);
            if (is_null($this->patient)) {
                throw new CHttpException(404, 'The requested page does not exist.');
            }
        }
        return $this->model;
    }

    private function loadPatientInfoByIdAndCreatorId($id, $creatorId) {
        if (is_null($this->patient)) {
            $this->patient = PatientInfo::model()->getByIdAndCreatorId($id, $creatorId);
            if (is_null($this->patient)) {
                throw new CHttpException(404, 'The requested page does not exist.');
            }
        }
        return $this->patient;
    }

}
