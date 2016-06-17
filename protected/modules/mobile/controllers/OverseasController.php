<?php

class OverseasController extends MobileController {

    public function actionIndex() {
        //  var_dump($_GET);exit;
        //$url = $this->createUrl('overseas/hospital', $_GET);
       // $this->redirect($url);
        $this->render('index');
    }

    public function actionHospital($id) {        
        $osMgr = new OverseasManager();
        $hospitals = $osMgr->loadHospitals();
        $output = array();

        if (isset($hospitals[$id]) === false) {
            $output['status'] = false;
            $output['error'] = 'Unknown hospital.';
        } else {
            $hospital=$hospitals[$id];
            $this->render('pages/'.$id, array(
                'hospital' => $hospital
            ));
        }
    }

}
