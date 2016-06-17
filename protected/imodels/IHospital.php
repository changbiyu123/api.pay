<?php

class IHospital extends EViewModel {

    public $classText;
    public $typeText;
    public $urlImage;

    //public $city;   // ICity    
    //public $facultyDesc;    //FacultyHospitalJoin.description.
    //public $departments;    // Array IHospitalDepartment.

    public function initModel($model, $attributes = null) {
        parent::initModel($model, $attributes);

        $this->classText = $model->getClass();
        $this->typeText = $model->getType();
        $this->class = $this->classText;  //@REMOVE this line - 2015-08-26.
        $this->type = $this->typeText;    //@REMOVE this line - 2015-08-26.
        $this->imageUrl = $model->getAbsUrlAvatar();
        $this->urlImage = $model->imageUrl;  //@TODO: remove it 2015-08-25.
    }

    public function attributesMapping() {
        return array(
            'id' => 'id',
            'name' => 'name',
            'class' => 'class',
            'type' => 'type',
            'desc' => 'description',
            'phone' => 'phone',
            'address' => 'address',
            'urlWebsite' => 'website',
            'imageUrl' => 'image_url',
        );
    }

    public function addRelatedModel(Hospital $model, $with) {
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
                    case "hospitalCountry":
                        $this->setILocationCountry($relatedModel);
                        break;
                    case "hospitalState":
                        $this->setILocationState($relatedModel);
                        break;
                    case "hospitalCity":
                        $this->setILocationCity($relatedModel); // for api 2.0, IExpertTeam.hospital = 医院名称.
                        break;
                    case "hospitalDoctors":
                        $this->addIDoctors($relatedModel);
                        break;
                    case "hospitalDepartments":
                        $this->addIHospitalDepartments($relatedModel);
                        break;
                    default:
                        break;
                }
            }
        }
    }

    public function setILocationCountry($model, $attrName = 'country') {
        if ($model instanceof RegionCountry) {
            $imodel = new ILocationCountry();
            $imodel->initModel($model, array('id', 'name'));
            $this->{$attrName} = $imodel;
        } else {
            $this->{$attrName} = $model;
        }
    }

    public function setILocationState($model, $attrName = 'state') {
        if ($model instanceof RegionState) {
            $imodel = new ILocationState();
            $imodel->initModel($model, array('id', 'name'));

            $this->{$attrName} = $imodel;
        } else {
            $this->{$attrName} = $model;
        }
    }

    public function setILocationCity($model, $attrName = 'city') {
        if ($model instanceof RegionCity) {
            $imodel = new ILocationCity();
            $imodel->initModel($model, array('id', 'name'));

            $this->{$attrName} = $imodel;
        } else {
            $this->{$attrName} = $model;
        }
    }

    public function addIDoctors($models, $attrName = 'doctors') {
        if (arrayNotEmpty($models)) {
            foreach ($models as $model) {
                $imodel = new IHospitalDepartment();
                $imodel->initModel($model, array('id', 'name', 'desc'));
                $this->addIDoctor($imodel, $attrName);
            }
        }
    }

    /**
     * 
     * @param IDoctor $imodel
     */
    public function addIDoctor(IDoctor $imodel, $attrname = 'doctors') {
        $this->{$attrname}[] = $imodel;
    }

    /**
     * 
     * @param array $models HospitalDepartment.
     */
    public function addIHospitalDepartments($models, $attrName = "departments") {
        if (isset($this->{$attrName}) === false) {
            $this->{$attrName} = array();
        }
        if (arrayNotEmpty($models)) {
            foreach ($models as $model) {
                $imodel = new IHospitalDepartment();
                $imodel->initModel($model, array('id', 'name', 'group'));
                $this->addIHospitalDepartment($imodel, $attrName);
            }
        }
    }

    /**
     * 
     * @param IHospitalDepartment $imodel
     */
    public function addIHospitalDepartment(IHospitalDepartment $imodel, $attrname = 'departments') {
        $this->{$attrname}[$imodel->group][] = $imodel;
    }

    public function getDepartments($flatten = false) {
        if ($flatten) {
            if (isset($this->departments) && arrayNotEmpty($this->departments)) {
                $output = array();
                foreach ($this->departments as $group) {
                    foreach ($group as $dept) {
                        $output[] = $dept;
                    }
                }
                return $output;
            } else {
                return null;
            }
        } else {
            return $this->departments;
        }
    }

    public function getCity() {
        if (isset($this->city))
            return $this->city;
        else
            return null;
    }

    public function getDescription($ntext = false) {
        return $this->getTextAttribute($this->desc, $ntext);
    }

    /*
      public function getFacultyDesc($ntext = false) {
      return $this->getTextAttribute($this->facultyDesc, $ntext);
      }
     * 
     */

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getClass() {
        return $this->class;
    }

    public function getClassText() {
        return $this->classText;
    }

    public function getType() {
        return $this->type;
    }

    public function getTypeText() {
        return $this->typeText;
    }

    public function getUrlImage() {
        return $this->urlImage;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function getAddress() {
        return $this->address;
    }

    public function getUrlWebsite() {
        return $this->urlWebsite;
    }

}
