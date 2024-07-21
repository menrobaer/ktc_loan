<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customer".
 *
 * @property int $id
 * @property string|null $profile_url
 * @property string|null $name
 * @property string|null $phone_number
 * @property string|null $address
 * @property int|null $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property int|null $sort
 */
class Customer extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'customer';
  }

  /**
   * {@inheritdoc}
   */

  /**
   * @var UploadedFile
   */
  public $imageFile;
  public function rules()
  {
    return [
      [['name'], 'required', 'on' => 'default'],
      [['status', 'created_by', 'sort'], 'integer'],
      [['created_at'], 'safe'],
      [['name'], 'string', 'max' => 50],
      [['phone_number'], 'string', 'max' => 20],
      [['address'], 'string', 'max' => 255],
      [['profile_url'], 'string', 'max' => 100],
      [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif', 'maxSize' => 1024 * 1024 * 1],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'profile_url' => 'Profile',
      'id' => 'ID',
      'name' => 'Name',
      'phone_number' => 'Phone Number',
      'address' => 'Address',
      'status' => 'Status',
      'created_at' => 'Created At',
      'created_by' => 'Created By',
    ];
  }

  public function uploadImage()
  {
    if ($this->validate() && $this->imageFile) {
      $filePath = Yii::$app->setup->getUploadFolderName() . '/uploads/customers/profiles';
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
    if (!$this->getImagePath()) return Yii::getAlias("@web/img/avatar.webp");
    return $this->getImagePath();
  }

  public function getImagePath()
  {
    if (!$this->profile_url || !file_exists($this->profile_url)) {
      return '';
    }
    return Yii::getAlias('@web') . '/' . $this->profile_url;
  }

  public function getLoans()
  {
    return $this->hasMany(Loan::class, ['customer_id' => 'id']);
  }


  public function getIsUsed()
  {
    return Loan::find()->where(['customer_id' => $this->id])->count() > 0 ? true : false;
  }

  public function beforeSave($insert)
  {
    if (parent::beforeSave($insert)) {

      if ($this->isNewRecord) {
        $this->status = 1;
        $this->created_at = date('Y-m-d H:i:s');
        $this->created_by = Yii::$app->user->identity->id;
      }
      return true;
    } else {
      return false;
    }
  }
}
