<?php

class HospitalController extends MobileController {

    private $_model;

    public function actionIndex() {
        $values = $_GET;
        $limit = 10;
        if (isset($values['limit']) === false) {
            $values['limit'] = $limit;
        }
        $apiService = new ApiViewHospitalSearch($values);
        $output = $apiService->loadApiViewData();
        $this->render('index', array(
            'data' => $output
        ));
    }

    public function actionView($id) {
        //$this->show_footer=false;
        $apiService = new ApiViewHospital($id);
        $output = $apiService->loadApiViewData();

        $this->render('view', array(
            'data' => $output
        ));
    }

    public function actionDept($id) {        
        $queryOptions = $this->parseQueryOptions(Yii::app()->request);
        $apiService = new ApiViewHospitalDept($id, $queryOptions);
        $output = $apiService->loadApiViewData();

        $this->render('dept', array(
            'data' => $output
        ));
    }

    public function actionFacility() {
        $this->render('facility');
    }

    public function getHospitalList() {
        return array(1 => 'shrjyy', 2 => '', 3 => '', 4 => '', 5 => '', 6 => '', 7 => '', 8 => '', 9 => '', 10 => '');
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Hospital the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        if ($this->_model === null) {
            $this->_model = Hospital::model()->getById($id);
            if ($this->_model === null)
                throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $this->_model;
    }

    private function parseQueryOptions($request) {
        $options = array();
        $options['limit'] = $request->getParam('limit', null);
        $options['offset'] = $request->getParam('offset', null);
        $options['order'] = $request->getParam('order', null);

        return $options;
    }

}
