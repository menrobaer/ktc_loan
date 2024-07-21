<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

/**
 * UserSearch represents the model behind the search form of `app\modules\admin\models\User`.
 */
class UserSearch extends User
{
  /**
   * {@inheritdoc}
   */
  public $globalSearch, $role;
  public function rules()
  {
    return [
      [['id', 'status'], 'integer'],
      [['email', 'username', 'first_name', 'last_name', 'phone_number', 'password_hash', 'password_reset_token', 'auth_key', 'verification_token', 'created_at'], 'safe'],
      [['globalSearch', 'role'], 'safe'],
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
    $query = User::find()->andWhere(['NOT IN', 'id', 2])->andWhere(['NOT IN', 'status', [User::STATUS_DELETE]]);

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

    $query->andFilterWhere([
      'OR',
      ['like', 'email', $this->globalSearch],
      ['like', 'username', $this->globalSearch],
      ['like', 'first_name', $this->globalSearch],
      ['like', 'last_name', $this->globalSearch],
    ]);

    return $dataProvider;
  }
}
