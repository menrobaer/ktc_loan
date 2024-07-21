<?php

namespace app\models;

use Codeception\Attribute\When;
use Yii;

/**
 * This is the model class for table "loan".
 *
 * @property int $id
 * @property string|null $generate_code
 * @property string|null $code
 * @property string|null $customer_code
 * @property string|null $item_name
 * @property int|null $customer_id
 * @property string|null $date
 * @property string|null $payment_day
 * @property string|null $created_at
 * @property int|null $created_by
 * @property float|null $discount
 * @property float|null $loan_amount
 * @property float|null $deposit_amount
 * @property float|null $grand_total
 * @property float|null $original_amount
 * @property float|null $original_balance_amount
 * @property float|null $original_installment_amount
 * @property float|null $paid_amount
 * @property float|null $balance_amount
 * @property float|null $interest_rate
 * @property float|null $interest_rate_amount
 * @property int|null $payment_term_id
 * @property int|null $status
 * @property string|null $remark
 * @property float|null $service_charge
 * @property float|null $service_charge_amount
 * @property float|null $exchange_rate
 * @property float|null $installment_amount
 * @property float|null $profit_amount
 * @property float|null $is_exclude_weekend
 */
class Loan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loan';
    }
    const UNPAID = 2, CANCELLED = 0, PAID = 1, PARTIAL_PAID = 3, DELETED = 10, IN_PROCESS = 4;
    /**
     * {@inheritdoc}
     */
    public $loan_amount_alt, $service_charge_amount_alt;
    public function rules()
    {
        return [
            [['loan_amount_alt', 'service_charge_amount_alt'], 'safe'],
            [['item_name', 'payment_day'], 'required', 'on' => 'ktc'],
            [['date', 'customer_id', 'officer_id'], 'required'],
            [['loan_amount'], 'number', 'min' => 1],
            [['customer_id', 'officer_id', 'created_by', 'status', 'payment_term_id'], 'integer'],
            [['date', 'payment_day', 'created_at'], 'safe'],
            [['discount', 'paid_amount', 'balance_amount', 'service_charge', 'service_charge_amount', 'exchange_rate', 'interest_rate', 'interest_rate_amount', 'installment_amount', 'profit_amount', 'grand_total', 'original_balance_amount', 'deposit_amount'], 'number'],
            [['generate_code', 'code', 'customer_code'], 'string', 'max' => 20],
            [['item_name'], 'string', 'max' => 100],
            [['remark'], 'string', 'max' => 255],
            [['is_exclude_weekend'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'generate_code' => 'Generate Code',
            'code' => 'Code',
            'customer_id' => 'Customer',
            'item_name' => 'Item',
            'officer_id' => 'Officer',
            'date' => 'Issued Date',
            'payment_day' => 'Payment Day',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'discount' => 'Discount',
            'original_balance_amount' => 'Original Balance',
            'loan_amount' => 'Loan amount',
            'paid_amount' => 'Paid Amount',
            'balance_amount' => 'Balance Amount',
            'status' => 'Status',
            'remark' => 'Remark',
            'service_charge' => 'Service Charge',
            'service_charge_amount' => 'Service Charge Amount',
            'exchange_rate' => 'Exchange Rate',
            'payment_term_id' => 'Payment Term',
            'interest_rate' => 'Interest Rate',
        ];
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    public function getPaymentTerm()
    {
        if (Yii::$app->setup->isLoanMonthly()) {
            return $this->hasOne(PaymentTerm::class, ['id' => 'payment_term_id']);
        } else {
            return $this->hasOne(PaymentTermDaily::class, ['id' => 'payment_term_id']);
        }
    }

    public function getIsUsed()
    {
        return true;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            /** @var \app\components\Utils $utils */
            $utils = Yii::$app->utils;
            $this->deposit_amount = floatval($this->deposit_amount);
            if ($this->isNewRecord) {
                $latestIncrement = Yii::$app->db->createCommand("SELECT 
                count(id)
                FROM loan
                WHERE MONTH(CURDATE()) = MONTH(created_at) AND YEAR(CURDATE()) = YEAR(created_at)")
                    ->queryScalar();
                $code = sprintf("%04d", (int)$latestIncrement + 1);
                $this->code = $code;

                $length = 8;
                $result = false;
                $generate_code = '';
                do {
                    $generate_code = substr(str_shuffle(str_repeat($x = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
                    $has = self::findOne(['generate_code' => $generate_code]);
                    if (empty($has)) $result = true;
                } while (!$result);
                $this->generate_code = $generate_code;
                $this->status = self::UNPAID;
                $this->created_at = date('Y-m-d H:i:s');
                $this->created_by = Yii::$app->user->identity->id;

                $latestIncrement = self::find()
                    ->where(['date' => $this->date])
                    ->count();
                $latestIncrement = sprintf("%02d", (int)$latestIncrement + 1);
                $this->customer_code = $utils->numberToAA($this->countLoanByCustomer() + 1) . date_format(date_create($this->date), "dm") . $latestIncrement;
            }
            if ($this->grand_total > 0) {
                $this->balance_amount = $this->grand_total - $this->paid_amount;
            }
            if ($this->paid_amount == 0) {
                $this->original_amount = $this->loan_amount - $this->deposit_amount;
                $this->original_balance_amount = $this->original_amount;
                $this->original_installment_amount = round($this->original_amount / $this->payment_term_id, 2);
            }
            return true;
        } else {
            return false;
        }
    }

    public function getOfficer()
    {
        return $this->hasOne(User::class, ['id' => 'officer_id']);
    }

    public function countLoanByCustomer()
    {
        return self::find()->where(['customer_id' => $this->customer_id])->count();
    }

    public function getStatusTemp()
    {
        switch ($this->status) {
            case self::PAID:
                return 'PAID';
                break;
            case self::CANCELLED:
                return '<del> VOIDED </del>';
                break;
            case self::PARTIAL_PAID:
                return '<span class="badge badge badge-info"> PARTIAL PAID </span>';
                break;
            case self::UNPAID:
                return '<span class="badge badge badge-warning"><span class="oi oi-media-record pulse mr-1"></span> UNPAID </span>';
                break;
        }
    }

    public function getFirstPayDate()
    {
        $loanTerm = LoanTerm::find()->where(['loan_id' => $this->id])->orderBy(['date' => SORT_ASC])->one();
        return $loanTerm->date;
    }

    public function getLastPayDate()
    {
        $loanTerm = LoanTerm::find()->where(['loan_id' => $this->id])->orderBy(['date' => SORT_DESC])->one();
        return $loanTerm->date;
    }
}
