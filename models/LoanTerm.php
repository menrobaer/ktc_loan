<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "loan_term".
 *
 * @property int $id
 * @property int|null $loan_id
 * @property string|null $date
 * @property float|null $amount
 * @property float|null $original_balance_amount
 * @property float|null $service_charge
 * @property int|null $status
 */
class LoanTerm extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loan_term';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['loan_id', 'status'], 'integer'],
            [['date'], 'safe'],
            [['amount', 'original_paid', 'interest_amount', 'original_balance_amount', 'service_charge'], 'number'],
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
            'date' => 'Date',
            'amount' => 'Amount',
            'original_balance_amount' => 'Original Balance Amount',
            'status' => 'Status',
        ];
    }
}
