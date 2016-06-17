<?php

class ExpertteamController extends MobileController {

    public function actionIndex() {
        $this->render("index");
    }

    public function actionView($id) {
        $expteamMgr = new ExpertTeamManager();
        $imodel = $expteamMgr->loadIExpertTeamById($id);
        
        $this->render('view', array(
            'model'=>$imodel,          
        ));
    }

    public function actionDetail($code) {
        $viewFile = 'details/' . $code;
        $this->renderPartial($viewFile);
    }

}
