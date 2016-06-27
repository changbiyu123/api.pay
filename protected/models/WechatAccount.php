<?php

/**
 * This is the model class for table "wechat_account".
 *
 * The followings are the available columns in table 'wechat_account':
 * @property integer $id
 * @property string $weixinpub_id
 * @property string $weixinpub_name
 * @property string $weixinpub_token
 * @property string $encoding_key
 * @property string $app_id
 * @property string $app_secret
 * @property string $mch_id
 * @property string $api_key
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class WechatAccount extends DB2ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wechat_account';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('weixinpub_id', 'required'),
			array('weixinpub_id', 'length', 'max'=>20),
			array('weixinpub_name, app_id, mch_id', 'length', 'max'=>50),
			array('weixinpub_token, encoding_key', 'length', 'max'=>200),
			array('app_secret, api_key', 'length', 'max'=>100),
			array('date_created, date_updated, date_deleted', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, weixinpub_id, weixinpub_name, weixinpub_token, encoding_key, app_id, app_secret, mch_id, api_key, date_created, date_updated, date_deleted', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'weixinpub_id' => 'Weixinpub',
			'weixinpub_name' => 'Weixinpub Name',
			'weixinpub_token' => 'Weixinpub Token',
			'encoding_key' => 'Encoding Key',
			'app_id' => 'App',
			'app_secret' => 'App Secret',
			'mch_id' => 'Mch',
			'api_key' => 'Api Key',
			'date_created' => 'Date Created',
			'date_updated' => 'Date Updated',
			'date_deleted' => 'Date Deleted',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('weixinpub_id',$this->weixinpub_id,true);
		$criteria->compare('weixinpub_name',$this->weixinpub_name,true);
		$criteria->compare('weixinpub_token',$this->weixinpub_token,true);
		$criteria->compare('encoding_key',$this->encoding_key,true);
		$criteria->compare('app_id',$this->app_id,true);
		$criteria->compare('app_secret',$this->app_secret,true);
		$criteria->compare('mch_id',$this->mch_id,true);
		$criteria->compare('api_key',$this->api_key,true);
		$criteria->compare('date_created',$this->date_created,true);
		$criteria->compare('date_updated',$this->date_updated,true);
		$criteria->compare('date_deleted',$this->date_deleted,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WechatAccount the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        
        
        /********************************↓↓↓自定义方法↓↓↓********************************/
        public function getByPubId($weixinpub_id) {
            return $this->getByAttributes(array('weixinpub_id'=>$weixinpub_id));
        }
        
        public function getWeixinpubId(){
            return $this->weixinpub_id;
        }

        public function getAppId(){
            return $this->app_id;
        }

        public function getAppSecret(){
            return $this->app_secret;
        }
        
        public function getMchId(){
            return $this->mch_id;
        }
        
        public function getApiKey(){
            return $this->api_key;
        }
        /********************************↑↑↑自定义方法↑↑↑********************************/
        
}
