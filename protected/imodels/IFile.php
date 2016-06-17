<?php

abstract class IFile extends EViewModel {

    public $id;
    public $uid;
    public $url;
    public $urlTN;
    public $size;
    public $fileName;
    public $fileExt;
    public $mimeType;
    public $createDate;

    public function initModel($model, $attributes = null) {
        parent::initModel($model, $attributes);

        $this->url = $model->getAbsFileUrl();
        $this->urlTN = $model->getAbsThumbnailUrl();
        $this->createDate = $model->getDateCreated();
    }

    /**
     * corresponds to EFileModel.
     * @return array.
     */
    public function attributesMapping() {
        return array(
            'id' => 'id',
            'uid' => 'uid',
            'fileSize' => 'file_size',
            'fileName' => 'file_name',
            'fileExt' => 'file_ext',
            'mimeType' => 'mime_type',
        );
    }

}
