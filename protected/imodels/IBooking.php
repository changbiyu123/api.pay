<?php

class IBooking extends EViewModel {

    public $bookingTypeText;
    public $bookingTarget;
    public $statusText;
    public $apptDate;
    public $createDate;

//Manager 按需生成 related models:
//    public $owner;
//    public $faculty;    // model IFaculty.
//    public $doctor;     // model IDoctor.
//    public $expertTeam;     // model IExpertTeam.
//    public $hospital;   // model IHospital.
//    public $dept; // model IHospitalDepartment.
//    public $files;  // model IFile.
//    public $countFiles; // count number of BookingFiles.

    public function initModel($model, $attributes = null) {
        parent::initModel($model, $attributes);

        $this->bookingTypeText = $model->getBookingTypeText();
        $this->statusText = $model->getStatusText();
        $this->apptDate = $model->getApptDate();
        $this->createDate = $model->getDateCreated('Y年m月d日');
    }

    public function attributesMapping() {
        return array(
            'id' => 'id',
            'refNo' => 'ref_no',
            'userId' => 'user_id',
            'mobile' => 'mobile',
            'bookingType' => 'bk_type',
//            'facultyId' => 'faculty_id',
            'doctorId' => 'doctor_id',
            'expteamId' => 'expteam_id',
            'hospitalId' => 'hospital_id',
            'hpDeptId' => 'hp_dept_id',
            'contactName' => 'contact_name', //@REMOVE it.
            'cName' => 'contact_name',
            'status' => 'bk_status',
            'apptDateStr' => 'remark',
            'patientCondition' => 'disease_detail',
        );
    }

    public function addRelatedModel(Booking $model, $with) {
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
                    case 'owner':
                        $this->setIOwner($relatedModel);
                        break;
                    case 'facultyBooked':
                        $this->setIFacultyBooked($relatedModel);
                        break;
                    case 'doctorBooked':
                        $this->setIDoctorBooked($relatedModel);
                        break;
                    case 'expertTeamBooked':
                        $this->setIExpertTeamBooked($relatedModel);
                        break;
                    case 'hospitalBooked':
                        $this->setIHospitalBooked($relatedModel);
                        break;
                    case 'hospitalDeptBooked':
                        $tempHp = $model->hospitalBooked;
                        $this->setIHospitalDeptBooked($relatedModel, $tempHp);
                        break;
                    case 'bookingFiles':
                        $this->addIBookingFiles($relatedModel);
                        break;
                    default:
                        break;
                }
            }
        }
    }

    public function setIOwner($model) {
        if ($model instanceof User) {
            $imodel = new IUser();
            $imodel->initModel($model);
            $this->owner = $imodel;
        } else {
            $this->owner = $model;
        }
    }

    // 科室
    public function setIFacultyBooked($model, $attrName = "faculty") {
        if ($model instanceof Faculty) {
            $imodel = new IFaculty();
            $imodel->initModel($model);
            $this->{$attrName} = $imodel;
        } else {
            $this->{$attrName} = $model;
        }
        $this->bookingTarget = $model->getName();
    }

    // 医生
    public function setIDoctorBooked($model, $attrName = "doctor") {
        if ($model instanceof Doctor) {
            $imodel = new IDoctor();
            $imodel->initModel($model);
            $this->{$attrName} = $imodel;
        } else {
            $this->{$attrName} = $model;
        }
        $this->bookingTarget = $model->getName();
    }

    // 专家团队
    public function setIExpertTeamBooked($model, $attrName = "expertTeam") {
        if ($model instanceof ExpertTeam) {
            $imodel = new IExpertTeam();
            $imodel->initModel($model);
            $this->{$attrName} = $imodel;
        } else {
            $this->{$attrName} = $model;
        }
        $this->bookingTarget = $model->getName();
    }

    // 医院
    public function setIHospitalBooked($model, $attrName = "hospital") {
        if ($model instanceof Hospital) {
            $imodel = new IHospital();
            $imodel->initModel($model);
            $this->{$attrName} = $imodel;
        } else {
            $this->{$attrName} = $model;
        }
        $this->bookingTarget = $model->getName();
    }

    // 医院科室
    public function setIHospitalDeptBooked($modelHp, $modelDept, $attrName = "dept") {
        if ($modelDept instanceof HospitalDepartment) {
            $imodelDept = new IHospitalDepartment();
            $imodelDept->initModel($modelDept);
            $this->{$attrName} = $imodelDept;
        } else {
            $this->{$attrName} = $modelDept;
        }

//        if ($modelHp instanceof Hospital) {
//            $imodelHp = new IHospital();
//            $imodelHp->initModel($modelHp);
//            $this->hospital = $imodelHp;
//        } else {
//            $this->hospital = $modelHp;
//        }

        $this->bookingTarget = $modelHp->getName() . " - " . $modelDept->getName();
    }

    /**
     * 
     * @param array $bookingFiles BookingFile.
     */
    public function addIBookingFiles($bookingFiles, $attrName = "files") {
        if (arrayNotEmpty($bookingFiles)) {
            foreach ($bookingFiles as $bFile) {
                $ibookingFile = new IBookingFile();
                $ibookingFile->initModel($bFile);
                $this->addIBookingFile($ibookingFile, $attrName);
            }
        }
    }

    /**
     * 
     * @param IBookingFile $ibookingFile
     */
    public function addIBookingFile(IBookingFile $ibookingFile, $attrName = "files") {
        $this->{$attrName}[] = $ibookingFile;
    }

    /*     * ****** Accessors ******* */

    public function getFiles() {
        return isset($this->files) ? $this->files : null;
    }

    public function getFacultyName() {
        if (isset($this->faculty)) {
            return $this->faculty->getName();
        } else {
            return "";
        }
    }

    public function getDoctorName() {
        if (isset($this->doctor)) {
            return $this->doctor->getName();
        } else {
            return "";
        }
    }

    public function getExpertTeamName() {
        if (isset($this->expertTeam)) {
            return $this->expertTeam->teamName;
        } else {
            return "";
        }
    }

    public function getHospitalName() {
        if (isset($this->hospital)) {
            return $this->hospital->getName();
        } else {
            return "";
        }
    }

    public function getDeptName() {
        if (isset($this->dept)) {
            return $this->dept->getName();
        } else {
            return "";
        }
    }

    public function getFilesCount() {
        return $this->countFiles;
    }

    public function setCountFiles($v) {
        $this->countFiles = $v;
    }

    public function getRefNumber() {
        return $this->refNo;
    }

    public function getContactName() {
        return $this->contactName;
    }

    public function getMobile() {
        return $this->mobile;
    }

    public function getPatientCondition() {
        return $this->patientCondition;
    }

    public function getApptDate() {
        return $this->createDate;
    }

    public function getBookingType() {
        return $this->bookingType;
    }

    public function getBookingTypeText() {
        return $this->bookingTypeText;
    }

    public function getCreateDate() {
        return $this->createDate;
    }

}
