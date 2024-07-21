<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "loan_activity".
 *
 * @property int $id
 * @property int|null $loan_id
 * @property int|null $payment_id
 * @property string|null $type
 * @property string|null $created_at
 * @property int|null $created_by
 */
class LoanActivity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loan_activity';
    }
    const CREATE = 'create', UPDATE = 'update', PAYMENT = 'payment', FINAL_PAYMENT = 'final_payment', CANCEL = 'cancel', RESET = 'reset', DELETE = 'delete', DUPLICATE = 'duplicate';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['loan_id', 'payment_id', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['type'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'loan_id' => 'Loan ID',
            'payment_id' => 'Payment ID',
            'type' => 'Type',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    public function getUser()
    {
        $user = User::findOne($this->created_by);
        if (in_array($this->type, [
            self::CREATE, self::UPDATE, self::CANCEL, self::RESET, self::DUPLICATE, self::DELETE,
            self::PAYMENT, self::FINAL_PAYMENT
        ]) && $this->getOfficer()) {
            return $this->getOfficer();
        }
        return !empty($user) ? "{$user->first_name} {$user->last_name}" : "";
    }

    public function getOfficer()
    {
        $officerName = '';
        if (!empty($this->loan_id)) {
            $loan = Loan::findOne($this->loan_id);
            $officer = User::findOne($loan->officer_id);
            if (!empty($officer)) {
                $officerName = $officer->getFullName();
            }
        }
        if (!empty($this->payment_id)) {
            $loanPayment = LoanPayment::findOne($this->payment_id);
            if (!empty($loanPayment)) {
                $officer = User::findOne($loanPayment->officer_id);
                if (!empty($officer)) {
                    $officerName = $officer->getFullName();
                }
            }
        }

        return $officerName;
    }


    public function getTypeAsText()
    {
        switch ($this->type) {
            case self::CREATE:
                return 'Loan created';
                break;
            case self::UPDATE:
                return 'Loan updated';
                break;
            case self::CANCEL:
                return 'Loan voided';
                break;
            case self::RESET:
                return 'Loan reset';
                break;
            case self::DUPLICATE:
                return 'Loan duplicated';
                break;
            case self::DELETE:
                return 'Loan deleted';
                break;
            case self::PAYMENT:
                $via = !empty($this->payment->method) ? ' via ' . $this->payment->method->name : '';
                return 'Loan payment' . $via;
                break;

            case self::FINAL_PAYMENT:
                $via = !empty($this->payment->method) ? ' via ' . $this->payment->method->name : '';
                return 'Loan paid-in-full payment' . $via;
                break;
        }
    }

    public function getAttachment()
    {
        if (in_array($this->type, [self::PAYMENT, self::FINAL_PAYMENT]) && !empty($this->payment) && file_exists($this->payment->file_path)) {
            return "<a data-bs-toggle='tooltip' data-title='click to download payment slip' href='{$this->payment->getFilePath()}' download='" . $this->payment->file_name . "' class='badge badge-sm bg-gradient-success'>Download attachment</a>";
        }
        return '';
    }

    public function getPayment()
    {
        return $this->hasOne(LoanPayment::class, ['id' => 'payment_id']);
    }

    public static function create($data)
    {
        $type = isset($data['type']) ? $data['type'] : null;
        $loan_id = isset($data['loan_id']) ? $data['loan_id'] : null;
        $payment_id = isset($data['payment_id']) ? $data['payment_id'] : null;

        $activity = new self();
        $activity->loan_id = $loan_id;
        $activity->payment_id = $payment_id;
        $activity->type = $type;
        $activity->created_at = date("Y-m-d H:i:s");
        $activity->created_by = Yii::$app->user->identity->id;
        $activity->save();
    }
}
