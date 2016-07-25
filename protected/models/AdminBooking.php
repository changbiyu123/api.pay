<?php

/**
 * This is the model class for table "admin_booking".
 *
 * The followings are the available columns in table 'admin_booking':
 * @property integer $id
 * @property integer $booking_id
 * @property integer $booking_type
 * @property string $ref_no
 * @property integer $patient_id
 * @property string $patient_name
 * @property string $patient_mobile
 * @property string $patient_age
 * @property integer $patient_gender
 * @property string $patient_identity
 * @property integer $state_id
 * @property integer $city_id
 * @property string $patient_state
 * @property string $patient_city
 * @property string $patient_address
 * @property string $disease_name
 * @property string $disease_detail
 * @property string $booking_detail
 * @property string $expected_time_start
 * @property string $expected_time_end
 * @property integer $expected_hospital_id
 * @property string $expected_hospital_name
 * @property integer $expected_hp_dept_id
 * @property string $expected_hp_dept_name
 * @property integer $expected_doctor_id
 * @property string $expected_doctor_name
 * @property string $expected_doctor_mobile
 * @property integer $creator_doctor_id
 * @property string $creator_doctor_name
 * @property string $creator_hospital_name
 * @property string $creator_dept_name
 * @property integer $final_doctor_id
 * @property string $final_doctor_name
 * @property string $final_doctor_mobile
 * @property integer $final_hospital_id
 * @property string $final_hospital_name
 * @property string $final_time
 * @property integer $operation_finished
 * @property integer $travel_type
 * @property integer $disease_confirm
 * @property string $customer_request
 * @property integer $customer_intention
 * @property integer $customer_type
 * @property string $customer_diversion
 * @property string $customer_agent
 * @property string $business_partner
 * @property integer $is_commonweal
 * @property integer $booking_service_id
 * @property integer $is_buy_insurance
 * @property integer $is_deal
 * @property integer $is_calculate
 * @property integer $is_approval
 * @property string $contact_name
 * @property integer $booking_status
 * @property integer $work_schedule
 * @property integer $order_status
 * @property string $order_amount
 * @property string $total_amount
 * @property string $deposit_total
 * @property string $deposit_paid
 * @property string $service_total
 * @property string $service_paid
 * @property integer $admin_user_id
 * @property string $admin_user_name
 * @property integer $bd_user_id
 * @property string $bd_user_name
 * @property string $cs_explain
 * @property integer $doctor_accept
 * @property string $doctor_opinion
 * @property integer $doctor_user_id
 * @property string $doctor_user_name
 * @property string $date_related
 * @property integer $is_operated
 * @property string $remark
 * @property integer $display_order
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 *
 * The followings are the available model relations:
 * @property AdminBookingFile[] $adminBookingFiles
 * @property AdminTaskBkJoin[] $adminTaskBkJoins
 * @property SalesOrder[] $salesOrders
 */
class AdminBooking extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'admin_booking';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date_created', 'required'),
			array('booking_id, booking_type, patient_id, patient_gender, state_id, city_id, expected_hospital_id, expected_hp_dept_id, expected_doctor_id, creator_doctor_id, final_doctor_id, final_hospital_id, operation_finished, travel_type, disease_confirm, customer_intention, customer_type, is_commonweal, booking_service_id, is_buy_insurance, is_deal, is_calculate, is_approval, booking_status, work_schedule, order_status, admin_user_id, bd_user_id, doctor_accept, doctor_user_id, is_operated, display_order', 'numerical', 'integerOnly'=>true),
			array('ref_no, patient_name, expected_doctor_mobile, final_doctor_mobile, order_amount, bd_user_name', 'length', 'max'=>20),
			array('patient_mobile, expected_hospital_name, expected_hp_dept_name, expected_doctor_name, creator_doctor_name, creator_hospital_name, creator_dept_name, final_doctor_name, customer_agent, business_partner, contact_name, admin_user_name, doctor_user_name', 'length', 'max'=>50),
			array('patient_age, customer_request, customer_diversion', 'length', 'max'=>11),
			array('patient_identity', 'length', 'max'=>18),
			array('patient_state, patient_city, total_amount, deposit_total, deposit_paid, service_total, service_paid', 'length', 'max'=>10),
			array('patient_address, booking_detail', 'length', 'max'=>200),
			array('disease_name, final_hospital_name', 'length', 'max'=>100),
			array('disease_detail', 'length', 'max'=>1000),
			array('cs_explain, doctor_opinion', 'length', 'max'=>500),
			array('remark', 'length', 'max'=>2000),
			array('expected_time_start, expected_time_end, final_time, date_related, date_updated, date_deleted', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, booking_id, booking_type, ref_no, patient_id, patient_name, patient_mobile, patient_age, patient_gender, patient_identity, state_id, city_id, patient_state, patient_city, patient_address, disease_name, disease_detail, booking_detail, expected_time_start, expected_time_end, expected_hospital_id, expected_hospital_name, expected_hp_dept_id, expected_hp_dept_name, expected_doctor_id, expected_doctor_name, expected_doctor_mobile, creator_doctor_id, creator_doctor_name, creator_hospital_name, creator_dept_name, final_doctor_id, final_doctor_name, final_doctor_mobile, final_hospital_id, final_hospital_name, final_time, operation_finished, travel_type, disease_confirm, customer_request, customer_intention, customer_type, customer_diversion, customer_agent, business_partner, is_commonweal, booking_service_id, is_buy_insurance, is_deal, is_calculate, is_approval, contact_name, booking_status, work_schedule, order_status, order_amount, total_amount, deposit_total, deposit_paid, service_total, service_paid, admin_user_id, admin_user_name, bd_user_id, bd_user_name, cs_explain, doctor_accept, doctor_opinion, doctor_user_id, doctor_user_name, date_related, is_operated, remark, display_order, date_created, date_updated, date_deleted', 'safe', 'on'=>'search'),
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
			'adminBookingFiles' => array(self::HAS_MANY, 'AdminBookingFile', 'admin_booking_id'),
			'adminTaskBkJoins' => array(self::HAS_MANY, 'AdminTaskBkJoin', 'admin_booking_id'),
			'salesOrders' => array(self::HAS_MANY, 'SalesOrder', 'admin_booking_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'booking_id' => 'Booking',
			'booking_type' => 'Booking Type',
			'ref_no' => 'Ref No',
			'patient_id' => 'Patient',
			'patient_name' => 'Patient Name',
			'patient_mobile' => 'Patient Mobile',
			'patient_age' => 'Patient Age',
			'patient_gender' => 'Patient Gender',
			'patient_identity' => 'Patient Identity',
			'state_id' => 'State',
			'city_id' => 'City',
			'patient_state' => 'Patient State',
			'patient_city' => 'Patient City',
			'patient_address' => 'Patient Address',
			'disease_name' => 'Disease Name',
			'disease_detail' => 'Disease Detail',
			'booking_detail' => 'Booking Detail',
			'expected_time_start' => 'Expected Time Start',
			'expected_time_end' => 'Expected Time End',
			'expected_hospital_id' => 'Expected Hospital',
			'expected_hospital_name' => 'Expected Hospital Name',
			'expected_hp_dept_id' => 'Expected Hp Dept',
			'expected_hp_dept_name' => 'Expected Hp Dept Name',
			'expected_doctor_id' => 'Expected Doctor',
			'expected_doctor_name' => 'Expected Doctor Name',
			'expected_doctor_mobile' => 'Expected Doctor Mobile',
			'creator_doctor_id' => 'Creator Doctor',
			'creator_doctor_name' => 'Creator Doctor Name',
			'creator_hospital_name' => 'Creator Hospital Name',
			'creator_dept_name' => 'Creator Dept Name',
			'final_doctor_id' => 'Final Doctor',
			'final_doctor_name' => 'Final Doctor Name',
			'final_doctor_mobile' => 'Final Doctor Mobile',
			'final_hospital_id' => 'Final Hospital',
			'final_hospital_name' => 'Final Hospital Name',
			'final_time' => 'Final Time',
			'operation_finished' => 'Operation Finished',
			'travel_type' => 'Travel Type',
			'disease_confirm' => 'Disease Confirm',
			'customer_request' => 'Customer Request',
			'customer_intention' => 'Customer Intention',
			'customer_type' => 'Customer Type',
			'customer_diversion' => 'Customer Diversion',
			'customer_agent' => 'Customer Agent',
			'business_partner' => 'Business Partner',
			'is_commonweal' => 'Is Commonweal',
			'booking_service_id' => 'Booking Service',
			'is_buy_insurance' => 'Is Buy Insurance',
			'is_deal' => 'Is Deal',
			'is_calculate' => 'Is Calculate',
			'is_approval' => 'Is Approval',
			'contact_name' => 'Contact Name',
			'booking_status' => 'Booking Status',
			'work_schedule' => 'Work Schedule',
			'order_status' => 'Order Status',
			'order_amount' => 'Order Amount',
			'total_amount' => 'Total Amount',
			'deposit_total' => 'Deposit Total',
			'deposit_paid' => 'Deposit Paid',
			'service_total' => 'Service Total',
			'service_paid' => 'Service Paid',
			'admin_user_id' => 'Admin User',
			'admin_user_name' => 'Admin User Name',
			'bd_user_id' => 'Bd User',
			'bd_user_name' => 'Bd User Name',
			'cs_explain' => 'Cs Explain',
			'doctor_accept' => 'Doctor Accept',
			'doctor_opinion' => 'Doctor Opinion',
			'doctor_user_id' => 'Doctor User',
			'doctor_user_name' => 'Doctor User Name',
			'date_related' => 'Date Related',
			'is_operated' => 'Is Operated',
			'remark' => 'Remark',
			'display_order' => 'Display Order',
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
		$criteria->compare('booking_id',$this->booking_id);
		$criteria->compare('booking_type',$this->booking_type);
		$criteria->compare('ref_no',$this->ref_no,true);
		$criteria->compare('patient_id',$this->patient_id);
		$criteria->compare('patient_name',$this->patient_name,true);
		$criteria->compare('patient_mobile',$this->patient_mobile,true);
		$criteria->compare('patient_age',$this->patient_age,true);
		$criteria->compare('patient_gender',$this->patient_gender);
		$criteria->compare('patient_identity',$this->patient_identity,true);
		$criteria->compare('state_id',$this->state_id);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('patient_state',$this->patient_state,true);
		$criteria->compare('patient_city',$this->patient_city,true);
		$criteria->compare('patient_address',$this->patient_address,true);
		$criteria->compare('disease_name',$this->disease_name,true);
		$criteria->compare('disease_detail',$this->disease_detail,true);
		$criteria->compare('booking_detail',$this->booking_detail,true);
		$criteria->compare('expected_time_start',$this->expected_time_start,true);
		$criteria->compare('expected_time_end',$this->expected_time_end,true);
		$criteria->compare('expected_hospital_id',$this->expected_hospital_id);
		$criteria->compare('expected_hospital_name',$this->expected_hospital_name,true);
		$criteria->compare('expected_hp_dept_id',$this->expected_hp_dept_id);
		$criteria->compare('expected_hp_dept_name',$this->expected_hp_dept_name,true);
		$criteria->compare('expected_doctor_id',$this->expected_doctor_id);
		$criteria->compare('expected_doctor_name',$this->expected_doctor_name,true);
		$criteria->compare('expected_doctor_mobile',$this->expected_doctor_mobile,true);
		$criteria->compare('creator_doctor_id',$this->creator_doctor_id);
		$criteria->compare('creator_doctor_name',$this->creator_doctor_name,true);
		$criteria->compare('creator_hospital_name',$this->creator_hospital_name,true);
		$criteria->compare('creator_dept_name',$this->creator_dept_name,true);
		$criteria->compare('final_doctor_id',$this->final_doctor_id);
		$criteria->compare('final_doctor_name',$this->final_doctor_name,true);
		$criteria->compare('final_doctor_mobile',$this->final_doctor_mobile,true);
		$criteria->compare('final_hospital_id',$this->final_hospital_id);
		$criteria->compare('final_hospital_name',$this->final_hospital_name,true);
		$criteria->compare('final_time',$this->final_time,true);
		$criteria->compare('operation_finished',$this->operation_finished);
		$criteria->compare('travel_type',$this->travel_type);
		$criteria->compare('disease_confirm',$this->disease_confirm);
		$criteria->compare('customer_request',$this->customer_request,true);
		$criteria->compare('customer_intention',$this->customer_intention);
		$criteria->compare('customer_type',$this->customer_type);
		$criteria->compare('customer_diversion',$this->customer_diversion,true);
		$criteria->compare('customer_agent',$this->customer_agent,true);
		$criteria->compare('business_partner',$this->business_partner,true);
		$criteria->compare('is_commonweal',$this->is_commonweal);
		$criteria->compare('booking_service_id',$this->booking_service_id);
		$criteria->compare('is_buy_insurance',$this->is_buy_insurance);
		$criteria->compare('is_deal',$this->is_deal);
		$criteria->compare('is_calculate',$this->is_calculate);
		$criteria->compare('is_approval',$this->is_approval);
		$criteria->compare('contact_name',$this->contact_name,true);
		$criteria->compare('booking_status',$this->booking_status);
		$criteria->compare('work_schedule',$this->work_schedule);
		$criteria->compare('order_status',$this->order_status);
		$criteria->compare('order_amount',$this->order_amount,true);
		$criteria->compare('total_amount',$this->total_amount,true);
		$criteria->compare('deposit_total',$this->deposit_total,true);
		$criteria->compare('deposit_paid',$this->deposit_paid,true);
		$criteria->compare('service_total',$this->service_total,true);
		$criteria->compare('service_paid',$this->service_paid,true);
		$criteria->compare('admin_user_id',$this->admin_user_id);
		$criteria->compare('admin_user_name',$this->admin_user_name,true);
		$criteria->compare('bd_user_id',$this->bd_user_id);
		$criteria->compare('bd_user_name',$this->bd_user_name,true);
		$criteria->compare('cs_explain',$this->cs_explain,true);
		$criteria->compare('doctor_accept',$this->doctor_accept);
		$criteria->compare('doctor_opinion',$this->doctor_opinion,true);
		$criteria->compare('doctor_user_id',$this->doctor_user_id);
		$criteria->compare('doctor_user_name',$this->doctor_user_name,true);
		$criteria->compare('date_related',$this->date_related,true);
		$criteria->compare('is_operated',$this->is_operated);
		$criteria->compare('remark',$this->remark,true);
		$criteria->compare('display_order',$this->display_order);
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
	 * @return AdminBooking the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
