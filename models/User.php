<?php

namespace app\models;

use app\models\UserRole;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\base\InvalidArgumentException;
use yii\helpers\ArrayHelper;

class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_INACTIVE = 2;
    const STATUS_ACTIVE = 1;
    const STATUS_DELETE = 10;
    const USER_TYPE = 1;
    const POS_SERVICE = 2;
    const ALL_FUNCTION = 1;

    const PF_SYSTEM = 1;
    const PF_DEALER = 2;
    const PF_MOBILE_APP = 10;

    // public $created_at, $updated_at;

    private static $_instance = NULL;

    private static $is_got_actions = null;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public $password, $current_password, $new_password, $confirm_password, $file;
    public $imageFile;
    public function rules()
    {
        return [
            [['first_name', 'last_name'], 'required'],
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETE]],
            [['email', 'username'], 'unique'],
            [['email', 'username', 'role_id'], 'required', 'on' => ['update', 'profile', 'create']],
            [['role_id', 'status', 'type_id'], 'integer'],
            [['email', 'password_hash', 'password_reset_token', 'auth_key', 'username'], 'string'],
            [['created_at', 'updated_at', 'file'], 'safe'],

            [['first_name', 'last_name'], 'string'],

            [['password', 'new_password', 'confirm_password', 'current_password'], 'string', 'min' => 6],
            [['current_password'], 'required', 'on' => ['security']],
            ['current_password', function ($attribute, $params, $validator) {
                $user = self::findOne(Yii::$app->user->identity->id);
                if (empty($this->current_password) || !$user->validatePassword($this->current_password)) {
                    $this->addError($attribute, 'Incorrect old password.');
                }
            }],
            ['new_password', 'required', 'when' => function ($model) {
                return $model->current_password != '';
            }, 'whenClient' => "function (attribute, value) {
                return $('#user-current_password').val() != '';
            }"],
            ['confirm_password', 'compare', 'compareAttribute' => 'new_password', 'skipOnEmpty' => false, 'message' => "Passwords don't match.", 'on' => ['security']],
            [['image_url'], 'string', 'max' => 100],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif', 'maxSize' => 1024 * 1024 * 1],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_id' => 'Role',
            'current_password' => 'Current Password',
            'new_password' => 'New Password',
            'confirm_password' => 'Confirm Password',
            'email' => 'Email',
            'created_at' => 'Created Date',
            'updated_at' => 'Updated Date',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'status' => 'Status',
        ];
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
                $this->status = self::STATUS_ACTIVE;
                $this->created_at = date('Y-m-d H:i:s');
                $this->type_id = 1;
                $this->platform_id = 1;
            } else {
                $this->updated_at = date('Y-m-d H:i:s');
            }
            return true;
        } else {
            return false;
        }
    }

    public function uploadImage()
    {
        if ($this->validate() && $this->imageFile) {
            $filePath = Yii::$app->setup->getUploadFolderName() . '/uploads/user/profiles';
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
        if (!$this->image_url || !file_exists($this->image_url)) {
            return '';
        }
        return Yii::getAlias('@web') . '/' . $this->image_url;
    }

    public function getFullName()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE, 'platform_id' => self::PF_SYSTEM]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {

        function checkEmail($email)
        {
            $find1 = strpos($email, '@');
            $find2 = strpos($email, '.');
            return ($find1 !== false && $find2 !== false && $find2 > $find1);
        }

        if (checkEmail($username)) {
            return static::findOne(['email' => $username, 'status' => self::STATUS_ACTIVE, 'platform_id' => self::PF_SYSTEM]);
        } else {
            return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE, 'platform_id' => self::PF_SYSTEM]);
        }
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . md5(time());
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }


    public function getName()
    {
        return $this->username;
    }

    public function getRole()
    {
        return $this->hasOne(UserRole::class, ['id' => 'role_id']);
    }

    public function getProfile()
    {
        return $this->hasOne(UserProfile::class, ['user_id' => 'id']);
    }
}
