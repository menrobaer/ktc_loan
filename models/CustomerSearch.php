<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Customer;

/**
 * CustomerSearch represents the model behind the search form of `app\models\Customer`.
 */
class CustomerSearch extends Customer
{
  /**
   * {@inheritdoc}
   */
  public $globalSearch, $customer_id, $source;
  public function rules()
  {
    return [
      [['id', 'customer_id'], 'integer'],
      [['name', 'phone_number', 'address', 'created_at'], 'safe'],
      [['globalSearch'], 'safe']
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
    $query = Customer::find();

    // add conditions that should always apply here

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'sort' => ['defaultOrder' => ['name' => SORT_ASC, 'created_at' => SORT_DESC]]
    ]);

    $this->load($params);

    if (!$this->validate()) {
      // uncomment the following line if you do not want to return any records when validation fails
      // $query->where('0=1');
      return $dataProvider;
    }

    $query->andFilterWhere(['id' => $this->customer_id]);

    return $dataProvider;
  }
}
