<?php

class OrderController extends MobiledoctorController {

    public function actionView($refNo) {
        $apiSvc = new ApiViewSalesOrder($refNo);
        $output = $apiSvc->loadApiViewData();
        $returnUrl = $this->getReturnUrl("order/view");
        $this->render('view', array(
            'data' => $output,
            'returnUrl' => $returnUrl,
        ));
    }

}
