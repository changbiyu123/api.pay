<?php

/**
 * This is the model class for table "booking_file".
 *
 * The followings are the available columns in table 'booking_file':
 * @property integer $id
 * @property string $uid
 * @property integer $admin_booking_id
 * @property integer $user_id
 * @property string $file_ext
 * @property string $mime_type
 * @property string $file_name
 * @property string $file_url
 * @property integer $file_size
 * @property string $thumbnail_name
 * @property string $thumbnail_url
 * @property string $base_url
 * @property integer $display_order
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 *
 * The followings are the available model relations:
 * @property Booking $booking
 */
class AdminBookingFile extends EFileModel {

    public $file_upload_field = 'AdminBookingFiles'; // $_FILE['upload'].

    /**
     * @return string the associated database table name
     */

    public function tableName() {
        return 'admin_booking_file';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('uid, admin_booking_id, file_ext, file_name, file_url', 'required'),
            array('admin_booking_id, user_id, file_size, display_order', 'numerical', 'integerOnly' => true),
            array('uid', 'length', 'max' => 32),
            array('file_ext, report_type', 'length', 'max' => 10),
            array('mime_type', 'length', 'max' => 20),
            array('file_name, thumbnail_name', 'length', 'max' => 40),
            array('file_url, thumbnail_url, base_url', 'length', 'max' => 255),
            array('date_created, date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, uid, admin_booking_id, user_id, file_ext, mime_type, file_name, file_url, file_size, thumbnail_name, thumbnail_url, display_order, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'adminbfBooking' => array(self::BELONGS_TO, 'adminBooking', 'admin_booking_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'uid' => 'Uid',
            'admin_booking_id' => 'Booking ID',
            'user_id' => 'User',
            'file_ext' => 'File Ext',
            'mime_type' => 'Mime Type',
            'file_name' => 'File Name',
            'file_url' => 'File Url',
            'file_size' => 'File Size',
            'thumbnail_name' => 'Thumbnail Name',
            'thumbnail_url' => 'Thumbnail Url',
            'display_order' => 'Display Order',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AdminBookingFile the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function initModel($bookingId, $userId, $file, $reportType) {
        $this->setBookingId($bookingId);
        $this->setUserId($userId);
        $this->setFileAttributes($file);
        $this->setReportType($reportType);
    }

    public function saveModel() {
        if ($this->validate()) {    // validates model attributes before saving file.
            try {
                $fileSysDir = $this->getFileSystemUploadPath();
                createDirectory($fileSysDir);
                //Thumbnail.
                $thumbImage = Yii::app()->image->load($this->file->getTempName());
                // $image->resize(400, 100)->rotate(-45)->quality(75)->sharpen(20);
                $thumbImage->resize($this->thumbnail_width, $this->thumbnail_height);
                if ($thumbImage->save($fileSysDir . '/' . $this->getThumbnailName()) === false) {
                    throw new CException('Error saving file thumbnail.');
                }
                if ($this->file->saveAs($fileSysDir . '/' . $this->getFileName()) === false) {
                    throw new CException('Error saving file.');
                }

                // validation is done before hand, so skip validation when saving into db.
                return $this->save(false);
            } catch (CException $e) {
                $this->addError('file', $e->getMessage());
                return false;
            }
        } else {
            return false;
        }
    }

    //Overwrites parent::getFileUploadRootPath().
    public function getFileUploadRootPath() {
        return Yii::app()->params['bookingFilePath'];
    }

    public function getFileSystemUploadPath($folderName = null) {
        return parent::getFileSystemUploadPath($folderName);
    }

    public function getFileUploadPath($folderName = null) {
        return parent::getFileUploadPath($folderName);
    }

    public function deleteModel($absolute = true) {
        return parent::deleteModel($absolute);
    }

    /*     * ****** Query Methods ******* */

    public function getAllByBookingId($bid) {
        $criteria = new CDbCriteria(array('order' => 't.display_order'));
        $criteria->addCondition('t.date_deleted is NULL');
        $criteria->compare('admin_booking_id', $bid);
        return $this->findAll($criteria);
    }

    /*     * ****** Accessors ****** */

    public function getBooking() {
        return $this->booking;
    }

    public function getBookingId() {
        return $this->admin_booking_id;
    }

    public function setBookingId($v) {
        $this->admin_booking_id = $v;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($v) {
        $this->user_id = $v;
    }

    public function setReportType($v) {
        $this->report_type = $v;
    }

}
