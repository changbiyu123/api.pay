<?php

class HuizhenController extends MobileController {

    public $page_list;
    public $default_page = 'buyun';
    public $resource_url;

    public function actionIndex() {
        $this->render('index');
    }

    /**
     * 
     * @param string $id Faculty.code
     */
    public function actionByfaculty($id = null) {

        $facultyMgr = new FacultyManager();
        $faculty = $facultyMgr->loadFacultyByCode($id);

        $ifaculty = $facultyMgr->loadIFaculty($faculty->getId());

        $this->render('byFaculty', array(
            'ifaculty' => $ifaculty
        ));
    }

    // TODO: remove this method. not used anymore. @QP - 2015-06-12
    public function actionBydisease($id = null) {
        $this->redirect(array('byfaculty', 'id' => $id));
        
        
        $pageData = $this->loadPageData();
        if (isset($pageData[$id])) {
            
        } else {
            $this->redirect(array('bydisease', 'id' => $this->default_page));
        }
        $dData = $pageData[$id];
        $hIdList = $dData['hospital'];
        $hospitalList = Hospital::model()->getAllByIds($hIdList);
        $dIdList = $dData['doctor'];
        $doctorList = Doctor::model()->getAllByIds($dIdList);

        $data = array(
            'disease' => $dData['disease'],
            'hospital' => $hospitalList,
            'doctor' => $doctorList
        );
        $this->render('byDisease', array(
            'data' => $data
        ));
    }

    /*
      public function actionBydisease($id=null) {
      $list = $this->getPageList();

      if (isset($list[$id])) {
      $view = $id;
      $folder = $id;
      } else {
      $this->redirect(array('bydisease', 'id' => $this->default_page));
      }
      $this->resource_url = $this->resource_url . $folder . '/';
      $dataFile = 'data/' . $id . '.php';
      //require_once($this->module->viewPath);
      $this->render('byDisease', array(
      'dataFile' => $dataFile
      ));
      }
     */
    /*
      public function getPageList() {
      if ($this->page_list === null) {
      $this->page_list = array('fuchan' => '妇产', 'zhengxing' => '整形美容', 'shen' => '肾脏', 'fei' => '肺部', 'weichang' => '胃肠', 'gandan' => '肝胆', 'xinxueguan' => '心血管', 'buyun' => '不孕不育', 'guke' => '骨科', 'zhongliu' => '肿瘤');
      }
      return $this->page_list;
      }
     * 
     */

    // TODO: remove this method. not used anymore. @QP - 2015-06-12
    public function loadPageData() {
        return array(
            'fuchan' => array(
                'disease' => array(
                    'name' => '妇产',
                    'list' => array('子宫内膜炎', '子宫肌瘤', '宫颈炎', '乳腺炎', '妊娠'),
                    'icon' => 'm-icon-baby',
                ),
                'hospital' => array(3, 37, 2),
                'doctor' => array(30, 6)
            ),
            'zhengxing' => array(
                'disease' => array(
                    'name' => '整形美容',
                    'sub_name' => '特色项目',
                    'list' => array('美白针', '双眼皮手术', '丰胸', '生物除皱', '瘦脸针', '微整形'),
                    'icon' => 'm-icon-cosmetic',
                ),
                'hospital' => array(4, 45, 2),
                'doctor' => array(28, 29)
            ),
            'shen' => array(
                'disease' => array(
                    'name' => '肾脏',
                    'list' => array('肾炎', '肾囊肿', '小儿肾病', '肾衰竭', '糖尿病肾病', '肾结石', '高血压肾病'),
                    'icon' => '',
                ),
                'hospital' => array(1, 10, 32),
                'doctor' => array(33, 8)
            ),
            'fei' => array(
                'disease' => array(
                    'name' => '肺部',
                    'list' => array('慢性支气管炎', '肺气肿', '哮喘', '肺炎', '支气管扩张症', '肺结核', '肺脓肿'),
                    'icon' => '',
                ),
                'hospital' => array(4, 10, 44),
                'doctor' => array(32, 20)
            ),
            'weichang' => array(
                'disease' => array(
                    'name' => '胃肠',
                    'list' => array('急性阑尾炎', '肠瘘', '肠炎', '肠梗阻', '结肠炎', '十二指肠溃疡', '直肠癌'),
                    'icon' => '',
                ),
                'hospital' => array(1, 7, 32),
                'doctor' => array(24, 5)
            ),
            'gandan' => array(
                'disease' => array(
                    'name' => '肝胆',
                    'list' => array('肝硬化', '病毒性肝炎', '脂肪肝', '红斑狼疮肝炎', '胆囊炎', '肝内胆管结石', '多发性肝囊肿'),
                    'icon' => 'm-icon-liver',
                ),
                'hospital' => array(40, 10, 42),
                'doctor' => array(19, 26)
            ),
            'xinxueguan' => array(
                'disease' => array(
                    'name' => '心血管',
                    'list' => array('冠心病', '心绞痛', '心肌梗塞', '高血压', '心律失常'),
                    'icon' => 'm-icon-cardio',
                ),
                'hospital' => array(1, 4, 32),
                'doctor' => array(31, 11)
            ),
            'buyun' => array(
                'disease' => array(
                    'name' => '不孕不育',
                    'list' => array('前列腺炎症', '排卵功能障碍'),
                    'icon' => 'm-icon-fertility',
                ),
                'hospital' => array(37, 10, 32),
                'doctor' => array(9, 6),
            ),
            'guke' => array(
                'disease' => array(
                    'name' => '骨科',
                    'list' => array('半月板损伤', '断指再植', '肩周炎', '骨髓炎', '平底足', '骨折', '慢性腰背痛'),
                    'icon' => 'm-icon-bone',
                ),
                'hospital' => array(37, 4, 42),
                'doctor' => array(27, 17)
            ),
            'zhongliu' => array(
                'disease' => array(
                    'name' => '肿瘤',
                    'list' => array('子宫肌瘤', '乳腺癌', '前列腺癌', '肺癌', '胃癌', '甲状腺癌', '直肠癌', '胰腺癌', '淋巴瘤'),
                    'icon' => 'm-icon-tumor',
                ),
                'hospital' => array(10, 44, 32),
                'doctor' => array(20, 15)
            ),
        );
    }

}
