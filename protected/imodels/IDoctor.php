<?php

class IDoctor extends EViewModel {

    public $hospital;   //  Doctor->hospital->name    
    public $mTitle; // medical title
    public $aTitle; // academic title   
    public $imageUrl;   //expertTeam pages , should be deleted, @2015-08-12    

    public function initModel($model, $attributes = null) {
        parent::initModel($model, $attributes);

        $this->hospital = $model->getHospitalName();
        $this->mTitle = $model->getMedicalTitle();
        $this->aTitle = $model->getAcademicTitle();
        $this->imageUrl = $model->getAbsUrlAvatar(true); //@REMOVE @2015-08-12
        //    $this->docName = $model->getName(); //  expertTeam pages, should be deleted, @2015-08-12
        $this->urlImage = $this->imageUrl;
        //$this->desc = $model->description;
    }

    public function attributesMapping() {
        return array(
            'id' => 'id',
            'name' => 'name',
            'docName' => 'name', //expertTeam pages , should be deleted, @2015-08-12
            'mobile' => 'mobile',
            'hid' => 'hospital_id',
            'desc' => 'description',
            // 'specialty' => 'specialty',
            'hpName' => 'hospital_name',
            'hpDeptName' => 'hp_dept_name',
            'hFaculty' => 'faculty'   //@REMOVE
        );
    }

    public function addRelatedModel(Doctor $model, $with) {
        if (arrayNotEmpty($with)) {
            foreach ($with as $key => $relatedAttr) {
                if (is_array($relatedAttr)) {
                    //$relatedAttr can be an array stating further model relations: 
                    //array('relatedAttr'=>array('with'=>'some relations'))
                    $relatedAttr = $key;
                }
                $relatedModel = $model->{$relatedAttr};
                if (is_null($relatedModel)) {
                    continue;
                }
                switch ($relatedAttr) {
                    case "doctorAvatar":
                        $this->setIDoctorAvatar($relatedModel);
                        break;
                    case "doctorCerts":
                        $this->addIDoctorCerts($relatedModel);
                        break;
                    case "doctorHospital":
                        //$this->setIHospital($relatedModel);   // for use in future.                        
                        $this->setHospitalName($relatedModel); // for api 2.0, IExpertTeam.hospital = 医院名称.
                        break;
                    case "doctorHpDept":
                        $this->setIHospitalDepartment($relatedModel);
                        break;
                    case "doctorState":
                        $this->setILocationState($relatedModel);
                        break;
                    case "doctorCity":
                        $this->setILocationCity($relatedModel);
                        break;
                    default:
                        break;
                }
            }
        }
    }

    public function setIDoctorAvatar($model, $attrName = "avatar") {
        if ($model instanceof DoctorAvatar) {
            $imodel = new IDoctorAvatar();
            $imodel->initModel($model);
            $this->{$attrName} = $imodel;
        } else {
            $this->{$attrName} = $model;
        }
    }

    public function setHospitalName($model, $attrName = 'hospital') {
        $this->{$attrName} = $model->getName(); // for api 2.0.
        $this->hpName = $model->getName();       // for use in future.
    }

    public function setIHospital($model, $attrName = "hospital") {
        if ($model instanceof Hospital) {
            $imodel = new IHospital();
            $imodel->initModel($model);
            $this->{$attrName} = $imodel;
        } else {
            $this->{$attrName} = $model;
        }
    }

    public function setIHospitalDepartment($model, $attrName = "hpDept") {
        if ($model instanceof HospitalDepartment) {
            $imodel = new IHospitalDepartment();
            $imodel->initModel($model);
            $this->{$attrName} = $imodel;
        } else {
            $this->{$attrName} = $model;
        }
    }

    public function setILocationState($model, $attrName = "province") {
        if ($model instanceof RegionState) {
            $imodel = new ILocationState();
            $imodel->initModel($model);
            $this->{$attrName} = $imodel;
        } else {
            $this->{$attrName} = $model;
        }
    }

    public function setILocationCity($model, $attrName = "city") {
        if ($model instanceof RegionCity) {
            $imodel = new ILocationCity();
            $imodel->initModel($model);
            $this->{$attrName} = $imodel;
        } else {
            $this->{$attrName} = $model;
        }
    }

    /**
     * Cccepts an array of DoctorFile models.
     * Convert it to IDoctorFile.
     * Assign the resulting array to $this->certs.
     * @param array $doctorCerts array of DoctorFile.
     * @param string $attrName
     */
    public function addIDoctorCerts($doctorCerts, $attrName = 'certs') {
        if (arrayNotEmpty($doctorCerts)) {
            foreach ($doctorCerts as $cert) {
                $idoctorCert = new IDoctorCert();
                $idoctorCert->initModel($cert);
                $this->addCert($idoctorCert, $attrName);
            }
        }
    }

    /**
     * 
     * @param IBookingCert $ibookingCert
     */
    public function addCert(IDoctorCert $idoctorCert, $attrName = "certs") {
        $this->{$attrName}[] = $idoctorCert;
    }

    public function getDescritpion($ntext = false) {
        return $this->getTextAttribute($this->desc, $ntext);
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getMobile() {
        return $this->mobile;
    }

    public function getUrlImage() {
        return $this->urlImage;
    }

    public function getHospitalId() {
        return $this->hid;
    }

    public function getHospitalName() {
        return $this->hospital;
    }

    public function getHospitalFaculty() {
        return $this->hFaculty;
    }

    public function getMedicalTitle() {
        return $this->mTitle;
    }

    public function getAcademicTitle() {
        return $this->aTitle;
    }

    public function getDescription() {
        return $this->desc;
    }

    public function getCityName() {
        if (isset($this->city) && isset($this->city->name)) {
            return $this->city->name;
        } else {
            return "";
        }
    }

    public function getDoctorCerts() {
        return isset($this->certs) ? $this->certs : null;
    }

}
