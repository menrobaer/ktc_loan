<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "company".
 *
 * @property int $id
 * @property string|null $profile_url
 * @property float|null $service_charge
 * @property float|null $interest_rate
 * @property int|null $type_id
 * @property string|null $currency
 * @property string|null $name
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $website
 * @property string|null $header_url
 * @property string|null $bank_account
 * @property string|null $term_condition
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property string|null $upload_folder
 */
class Company extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * {@inheritdoc}
     */
    public $imageFile;
    public $headerFile;
    public function rules()
    {
        return [
            [['name', 'phone'], 'required'],
            [['type_id', 'updated_by'], 'integer'],
            [['service_charge', 'interest_rate'], 'number'],
            [['term_condition'], 'string'],
            [['updated_at'], 'safe'],
            [['currency'], 'string', 'max' => 5],
            [['profile_url', 'header_url'], 'string', 'max' => 100],
            [['upload_folder'], 'string', 'max' => 20],
            [['name', 'phone', 'email'], 'string', 'max' => 50],
            [['address', 'website', 'bank_account'], 'string', 'max' => 255],
            [['imageFile', 'headerFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif', 'maxSize' => 1024 * 1024 * 1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'currency' => 'Currency',
            'id' => 'ID',
            'profile_url' => 'Profile Url',
            'service_charge' => 'Service Charge',
            'interest_rate' => 'Interest Rate (%)',
            'type_id' => 'Bussiness Type',
            'name' => 'Bussiness Name',
            'address' => 'Address',
            'phone' => 'Phone',
            'email' => 'Email',
            'website' => 'Website',
            'header_url' => 'Image Url',
            'bank_account' => 'Bank Account',
            'term_condition' => 'Term Condition',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public function uploadImage()
    {
        if ($this->validate() && $this->imageFile) {
            $filePath = Yii::$app->setup->getUploadFolderName() . '/uploads/company/profiles';
            $directory = Yii::getAlias("@webroot/{$filePath}");
            // Create directory if it doesn't exist
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            $path = $directory . '/' . $this->imageFile->baseName . '.' . $this->imageFile->extension;
            if ($this->imageFile->saveAs($path)) {
                return $filePath . '/' . $this->imageFile->baseName . '.' . $this->imageFile->extension;
            }
        }
        return false;
    }

    public function getAvatar()
    {
        if (!$this->getImagePath()) return Yii::getAlias("@web/img/system_logo.jpg");
        return $this->getImagePath();
    }

    public function getImagePath()
    {
        if (!$this->profile_url || !file_exists($this->profile_url)) {
            return '';
        }
        return Yii::getAlias('@web') . '/' . $this->profile_url;
    }

    public function uploadHeader()
    {
        if ($this->validate() && $this->headerFile) {
            $filePath = 'uploads/company/headers';
            $directory = Yii::getAlias("@webroot/{$filePath}");
            // Create directory if it doesn't exist
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            $path = $directory . '/' . $this->headerFile->baseName . '.' . $this->headerFile->extension;
            if ($this->headerFile->saveAs($path)) {
                return $filePath . '/' . $this->headerFile->baseName . '.' . $this->headerFile->extension;
            }
        }
        return false;
    }

    public function getHeaderPath()
    {
        if (!$this->header_url || !file_exists($this->header_url)) {
            return '';
        }
        return Yii::getAlias('@web') . '/' . $this->header_url;
    }
}
