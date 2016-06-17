<?php

class IFaculty extends EViewModel {

    //   public $id;
    //   public $name;
    //   public $code;
    //   public $desc;
    public $diseases;

    /**
     * Related models:
     * public $hospitals;  // array of IHospital.
     * public $doctors;    // array of IDoctor.
     * public $expertTeams;   // array of IExpertTeam.
     */
    public function initModel($model, $attributes = null) {
        parent::initModel($model, $attributes);

        $this->diseases = $model->getDiseaseList();
    }

    public function attributesMapping() {
        return array(
            'id' => 'id',
            'name' => 'name',
            'code' => 'code',
            'desc' => 'description'
        );
    }

    public function addRelatedModel(Faculty $model, $with) {
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
                    /*
                    case 'facultyHospitals';
                        $this->addIHospitals($relatedModel);
                        break;
                     * 
                     */
                    case 'facultyExpertTeams':
                        $this->addIExpertTeams($relatedModel);
                        break;
                    case 'facultyDoctors':
                        $this->addIDoctors($relatedModel);
                        break;
                    default:
                        break;
                }
            }
        }
    }

    public function addIDoctors($models, $attrName = 'doctors') {
        if (arrayNotEmpty($models)) {
            foreach ($models as $model) {
                $imodel = new IDoctor();
                $imodel->initModel($model);
                $this->addIDoctor($imodel, $attrName);
            }
        }
    }

    public function addIDoctor(IDoctor $idoctor, $attrName = 'doctors') {
        $this->{$attrName}[] = $idoctor;
    }

    public function addIExpertTeams($models, $attrName = 'expertTeams') {        
        if (arrayNotEmpty($models)) {
            foreach ($models as $model) {
                $imodel = new IExpertTeam();
                $imodel->initModel($model);                
                $this->addIExpertTeam($imodel, $attrName);
            }
        }
    }

    public function addIExpertTeam(IExpertTeam $iexpTeam, $attrName = 'expertTeams') {
        $this->{$attrName}[] = $iexpTeam;
    }

    public function getHospitals() {
        return $this->hospitals;
    }

    public function getDoctors() {
        return $this->doctors;
    }

    public function getExpertTeams() {
        return $this->expertTeams;
    }

    public function getDiseaseList() {
        return $this->diseases;
    }

    public function getId() {
        return $this->id;
    }

    public function getCode() {
        return $this->code;
    }

    public function getName() {
        return $this->name;
    }

    public function getDescription($ntext = false) {
        return $this->getTextAttribute($this->desc, $ntext);
    }

}
