<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Loan;

/**
 * LoanSearch represents the model behind the search form of `app\models\Loan`.
 */
class LoanSearch extends Loan
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'customer_id', 'created_by', 'status'], 'integer'],
            [['generate_code', 'code', 'date', 'payment_day', 'created_at', 'remark'], 'safe'],
            [['discount', 'loan_amount', 'paid_amount', 'balance_amount', 'service_charge', 'service_charge_amount', 'exchange_rate'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Loan::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'date' => $this->date,
            'payment_day' => $this->payment_day,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'discount' => $this->discount,
            'loan_amount' => $this->loan_amount,
            'paid_amount' => $this->paid_amount,
            'balance_amount' => $this->balance_amount,
            'status' => $this->status,
            'service_charge' => $this->service_charge,
            'service_charge_amount' => $this->service_charge_amount,
            'exchange_rate' => $this->exchange_rate,
        ]);

        $query->andFilterWhere(['like', 'generate_code', $this->generate_code])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
