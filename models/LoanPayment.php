<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "loan_payment".
 *
 * @property int $id
 * @property string|null $file_path
 * @property string|null $file_name
 * @property string|null $generate_code
 * @property int|null $officer_id
 * @property int|null $loan_id
 * @property int|null $loan_term_id
 * @property int|null $method_id
 * @property string|null $date
 * @property float|null $amount
 * @property string|null $created_at
 * @property int|null $created_by
 */
class LoanPayment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loan_payment';
    }

    /**
     * {@inheritdoc}
     */
    public $file, $term_date;
    public function rules()
    {
        return [
            [['term_date'], 'safe'],
            [['method_id', 'officer_id', 'date'], 'required'],
            [['loan_id', 'loan_term_id', 'method_id', 'created_by', 'officer_id'], 'integer'],
            [['amount'], 'number'],
            [['created_at', 'date'], 'safe'],
            [['file_path', 'file_name'], 'string', 'max' => 255],
            [['generate_code'], 'string', 'max' => 20],
            [
                ['file'], 'file', 'skipOnEmpty' => true,
                'extensions' => array_merge(
                    $this->msOfficeEXT(),
                    $this->imageEXT(),
                    ['csv']
                ),
                'maxFiles' => 5,
                'maxSize' => 1024 * 1024 * 5
            ]
        ];
    }

    public function msOfficeEXT()
    {
        return ['ppt', 'pptx', 'xlsx', 'xls', 'doc', 'docx', 'pdf', 'txt'];
    }
    public function imageEXT()
    {
        return ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'file_path' => 'File Path',
            'file_name' => 'File Name',
            'officer_id' => 'Officer',
            'loan_id' => 'Loan',
            'loan_term_id' => 'Loan Term',
            'method_id' => 'Method',
            'date' => 'Date',
            'amount' => 'Amount',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $length = 8;
                $result = false;
                $generate_code = '';
                do {
                    $generate_code = substr(str_shuffle(str_repeat($x = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
                    $has = self::findOne(['generate_code' => $generate_code]);
                    if (empty($has)) $result = true;
                } while (!$result);
                $this->generate_code = $generate_code;
                $this->created_at = date('Y-m-d H:i:s');
                $this->created_by = Yii::$app->user->identity->id;
            }
            return true;
        } else {
            return false;
        }
    }

    public function getMethod()
    {
        return $this->hasOne(PaymentMethod::class, ['id' => 'method_id']);
    }

    public function getFilePath()
    {
        return Yii::getAlias('@web') . '/' . $this->file_path;
    }

    public function uploadFile()
    {
        if ($this->validate() && $this->file) {
            $filePath = Yii::$app->setup->getUploadFolderName() . '/uploads/loan/payments';
            $directory = Yii::getAlias("@webroot/{$filePath}");
            // Create directory if it doesn't exist
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            $filename = $this->file->baseName . '_' . floor(microtime(true) * 1000);

            $path = $directory . '/' . $filename . '.' . $this->file->extension;
            $this->file_path = $filePath . '/' . $filename . '.' . $this->file->extension;
            $this->file_name = $this->file->baseName . '.' . $this->file->extension;
            if ($this->save() && $this->file->saveAs($path)) {
                return true;
            }
        }
        return false;
    }
}
