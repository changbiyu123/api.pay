<?php

class IExpertTeam extends EViewModel {
//    public $detailUrl;
//    public $teamPageUrl;

    /**
     * @Related models:
     * public $teamLeader;  // IExpertLeader
     * public $members;     // array(IExpertMember)
     * public $hospital;    //Hospital's name
     * public $cityName;
     * public $faculty;
     */
    public function initModel($model, $attributes = null) {
        parent::initModel($model, $attributes);        

        // html static page for each expert team.        
        $this->teamPageUrl = Yii::app()->createAbsoluteUrl('expertteam/view', array('id' => $this->id));
        // html5 page for additional expert team details.
        $this->teamDetailUrl = Yii::app()->createAbsoluteUrl('/mobile/expertteam/detail', array('code' => $this->code));
        // used for app to load expert team data.
        $this->teamUrl = Yii::app()->createAbsoluteUrl('api/view', array('model' => 'expertteam', 'id' => $this->id));
        $this->imageUrl = $model->getAppImageUrl();
        $this->disTags=array();
        $disTagsStr = explode(",", $model->dis_tags);
        foreach ($disTagsStr as $dtStr) {
            if ($dtStr != "") {
                $this->disTags[] = $dtStr;
            }
        }
        /*
        if(is_null($this->disTags)){
            $this->disTags=array();
        }
         * 
         */
    }

    public function attributesMapping() {
        return array(
            'id' => 'id',
            'name' => 'name',
            'code' => 'code',
            'teamId' => 'id', // @REMOVE
            'teamName' => 'name', //@REMOVE
            'teamCode' => 'code', //@REMOVE
            'leaderId' => 'leader_id',
            'slogan' => 'slogan',
            'desc' => 'description',
            'imageUrl' => 'app_image_url', // App 团队logo.
            'introImageUrl' => 'banner_url'
        );
    }

    public function addRelatedModel(ExpertTeam $model, $with = null) {
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
                    case "expteamLeader":
                        $this->setIExpertLeader($relatedModel);
                        break;
                    case "expteamMembers":
                        $this->addIExpertMemebers($relatedModel);
                        break;
                    case "expteamHospital":
                        // $this->setIHospital($relatedModel);  // for use in future.                        
                        $this->setHospitalName($relatedModel);  // for api 2.0, IExpertTeam.hospital = 医院名称.
                        break;
                    case "expteamHpDept":
                        //$this->setIHospitalDepartment($relatedModel);
                        $this->setHpDeptName($relatedModel);
                        break;
                    case "expteamFaculty":
                        //$this->setIFaculty($relatedModel);     // for use in future.                                      
                        $this->setFacultyName($relatedModel);  // for api 2.0, IExpertTeam.facutly = IExpertTeam.faculty = 科室名称.
                        break;
                    case "expteamCity":
                        $this->setILocationCity($relatedModel);
                        break;
                    default:
                        break;
                }
            }
        }

        // Remove teamLeader from members if exist.
        // @Note: check the actual attribute name for teamLeader and members in setIExpertLeader() & addIExpertMemebers().
        /*
          if (isset($this->teamLeader) && isset($this->members) && arrayNotEmpty($this->members)) {
          foreach ($this->members as $key => $member) {
          if ($this->teamLeader->id == $member->id) {
          //   unset($member);
          unset($this->members[$key]);
          $this->members = array_values($this->members);  // reset array keys.
          break;
          }
          }
          }
         */
    }

    public function setIExpertLeader($model, $attrName = 'teamLeader') {
        if ($model instanceof Doctor) {
            $imodel = new IExpertLeader();
            $imodel->initModel($model);
            $this->{$attrName} = $imodel;
        } else {
            $this->{$attrName} = $model;
        }
    }

    public function addIExpertMemebers($models, $attrName = 'members') {
        if (arrayNotEmpty($models)) {
            foreach ($models as $model) {
                if (isset($this->teamLeader) === false && $this->leaderId == $model->id) {
                    $imodel = new IExpertLeader();
                    $imodel->initModel($model);
                    $this->setIExpertLeader($model);
                } else {
                    $imodel = new IExpertMember();
                    $imodel->initModel($model);
                    $this->addIExpertMember($imodel, $attrName);
                }
            }
        }
    }

    public function addIExpertMember(IExpertMember $imodel, $attrName = 'members') {
        $this->{$attrName}[] = $imodel;
    }

    public function setHospitalName($model, $attrName = 'hospital') {
        $this->{$attrName} = $model->getName(); // for api 2.0.
        $this->hpName = $model->getName();       // for use in future.
    }

    public function setIHospital($model, $attrName = 'hospital') {
        if ($model instanceof Hospital) {
            $imodel = new IHospital();
            $imodel->initModel($model, array('id', 'name', 'class', 'type'));

            $this->{$attrName} = $imodel;
        } else {
            $this->{$attrName} = $model;
        }
    }

    public function setIHospitalDepartment($model, $attrName = 'hpDept') {

        if ($model instanceof HospitalDepartment) {
            $imodel = new IHospitalDepartment();
            $imodel->initModel($model);
            $this->{$attrName} = $imodel;
        } else {
            $this->{$attrName} = $model;
        }
    }

    public function setHpDeptName($model, $attrName = 'hpDeptName') {
        $this->{$attrName} = $model->getName();
    }

    public function setFacultyName($model, $attrName = 'faculty') {
        $this->{$attrName} = $model->getName(); // for api 2.0
        $this->facultyName = $model->getName();    // for use in future.
    }

    public function setIFaculty($model, $attrName = 'faculty') {
        if ($model instanceof Faculty) {
            $imodel = new IFaculty();
            $imodel->initModel($model);
            $this->{$attrName} = $imodel;
        } else {
            $this->{$attrName} = $model;
        }
    }

    public function setILocationCity($model, $attrName = 'city') {
        if ($model instanceof RegionCity) {
            $imodel = new ILocationCity();
            $imodel->initModel($model);
            $this->{$attrName} = $imodel;
        } else {
            $this->{$attrName} = $model;
        }
    }

}
