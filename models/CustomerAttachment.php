<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customer_attachment".
 *
 * @property int $id
 * @property int|null $customer_id
 * @property string|null $path
 * @property string|null $filename
 * @property string|null $type
 * @property int|null $size
 * @property string|null $extension
 * @property string|null $created_at
 * @property int|null $created_by
 */
class CustomerAttachment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer_attachment';
    }

    /**
     * {@inheritdoc}
     */
    public $files;
    public function rules()
    {
        return [
            [['customer_id', 'size', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['path', 'filename'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 100],
            [['extension'], 'string', 'max' => 20],
            [
                ['files'], 'file', 'skipOnEmpty' => false,
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
            'customer_id' => 'Customer ID',
            'path' => 'Path',
            'filename' => 'Filename',
            'type' => 'Type',
            'size' => 'Size',
            'extension' => 'Extension',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }


    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
                $this->created_at = date('Y-m-d H:i:s');
                $this->created_by = Yii::$app->user->identity->id;
            }
            return true;
        } else {
            return false;
        }
    }

    public function formatSize()
    {
        return $this->convert_filesize($this->size);
    }

    public function convert_filesize($bytes, $decimals = 2)
    {
        $size = array('B', 'KB', 'MB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . " " . @$size[$factor];
    }

    public function getFilePath()
    {
        return Yii::getAlias('@web') . '/' . $this->path;
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function upload()
    {
        if ($this->validate()) {
            $filePath = Yii::$app->setup->getUploadFolderName() . '/uploads/customers/attachments';
            $directory = Yii::getAlias("@webroot/{$filePath}");
            // Create directory if it doesn't exist
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
            foreach ($this->files as $file) {
                $filename = $file->baseName . '_' . floor(microtime(true) * 1000);
                $path = $directory . '/' . $filename . '.' . $file->extension;
                $model = new self();
                $model->customer_id = $this->customer_id;
                $model->path = $filePath . '/' . $filename . '.' . $file->extension;
                $model->filename = $file->baseName;
                $model->extension = $file->extension;
                $model->size = $file->size;
                $model->type = $file->type;
                $model->save();
                $file->saveAs($path);
            }
            return true;
        }
        return false;
    }
}
