<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customer_detail".
 *
 * @property int|null $id
 * @property int|null $customer_id
 * @property string|null $name
 * @property string|null $gender
 * @property string|null $nationality
 * @property string|null $identity_number
 * @property string|null $date_of_birth
 * @property string|null $marital_status
 * @property string|null $current_address
 * @property string|null $phone
 * @property string|null $company_name
 * @property string|null $company_phone
 * @property string|null $company_address
 * @property string|null $position
 * @property float|null $income
 * @property string|null $payroll_day
 * @property string|null $guarantor_name
 * @property string|null $guarantor_gender
 * @property string|null $guarantor_nationality
 * @property string|null $guarantor_identity_number
 * @property string|null $guarantor_date_of_birth
 * @property string|null $guarantor_current_address
 * @property string|null $guarantor_phone
 * @property string|null $guarantor_relationship
 */
class CustomerDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'gender', 'nationality', 'identity_number', 'date_of_birth', 'marital_status', 'current_address', 'phone'], 'required'],
            [['customer_id'], 'integer'],
            [['date_of_birth', 'guarantor_date_of_birth'], 'safe'],
            [['income'], 'number'],
            [['name', 'guarantor_name'], 'string', 'max' => 100],
            [['gender', 'nationality', 'marital_status', 'guarantor_gender', 'guarantor_nationality'], 'string', 'max' => 10],
            [['identity_number', 'phone', 'company_phone', 'guarantor_identity_number'], 'string', 'max' => 20],
            [['current_address', 'company_address', 'guarantor_current_address'], 'string', 'max' => 255],
            [['company_name', 'position', 'guarantor_phone', 'guarantor_relationship'], 'string', 'max' => 50],
            [['payroll_day'], 'string', 'max' => 2],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'name' => 'Name',
            'gender' => 'Gender',
            'nationality' => 'Nationality',
            'identity_number' => 'Identity Number',
            'date_of_birth' => 'Date Of Birth',
            'marital_status' => 'Marital Status',
            'current_address' => 'Current Address',
            'phone' => 'Phone Number',
            'company_name' => 'Company Name',
            'company_phone' => 'Phone Number',
            'company_address' => 'Address',
            'position' => 'Position',
            'income' => 'Income',
            'payroll_day' => 'Payroll Day',
            'guarantor_name' => 'Guarantor Name',
            'guarantor_gender' => 'Gender',
            'guarantor_nationality' => 'Nationality',
            'guarantor_identity_number' => 'Identity Number',
            'guarantor_date_of_birth' => 'Date Of Birth',
            'guarantor_current_address' => 'Current Address',
            'guarantor_phone' => 'Phone',
            'guarantor_relationship' => 'Relationship',
        ];
    }
    public function getGenderInKH($gender)
    {
        if ($gender == 'male') {
            return 'ប្រុស';
        }
        if ($gender == 'female') {
            return 'ស្រី';
        }
        if ($gender == 'other') {
            return 'មិនបញ្ជាក់';
        }
        return 'មិនបញ្ជាក់';
    }

    public function getMaritalStatusInKH($status)
    {
        if ($status == 'single') {
            return 'នៅលីវ';
        }
        if ($status == 'married') {
            return 'មានគ្រួសារ';
        }
        if ($status == 'divoice') {
            return 'បែកបាក់';
        }
        return 'នៅលីវ';
    }
}
