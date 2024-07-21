<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_profile".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $gender
 * @property string|null $marital_status
 */
class UserProfile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['phone'], 'string', 'max' => 50],
            [['address'], 'string', 'max' => 255],
            [['gender', 'marital_status'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'phone' => 'Phone',
            'address' => 'Address',
            'gender' => 'Gender',
            'marital_status' => 'Marital Status',
        ];
    }
}
