<?php

namespace app\components;

use app\models\Company;
use app\models\User;
use yii\helpers\ArrayHelper;

class Setup extends \yii\web\Request
{
  public function getCompany()
  {
    return Company::findOne(1);
  }

  public function getUploadFolderName()
  {
    return "cdn/" . trim($this->company->upload_folder);
  }

  public function isLoanMonthly()
  {
    return !empty($this->company->type_id) ? ($this->company->type_id == 1 ? true : false) : true;
  }

  public function currency()
  {
    if ($this->company->currency == 'USD') {
      return '$';
    }
    if ($this->company->currency == 'KHR') {
      return '៛';
    }
    return '$';
  }

  public function interestRate()
  {
    return !empty($this->company) ? $this->company->interest_rate : 2;
  }
  public function serviceCharge()
  {
    return !empty($this->company) ? $this->company->service_charge : 1;
  }

  /**
   * @return data as an array
   */
  public function getGenders()
  {
    return ['male' => 'Male', 'female' => 'Female', 'other' => 'Other',];
  }

  /**
   * @return data as an array
   */
  public function getMaritalStatus()
  {
    return ['single' => 'Single', 'married' => 'Married', 'divoice' => 'Divoice'];
  }

  /**
   * @param string $type with following values `array` | `object` | `map`
   */
  public function getOfficers($type = 'array')
  {
    $model = User::find()->andWhere(['NOT IN', 'id', 2])->orderBy(['first_name' => SORT_ASC])->all();
    if ($type === 'array') {
      return ArrayHelper::toArray($model);
    } else if ($type === 'object') {
      return $model;
    } else if ($type === 'map') {
      return ArrayHelper::map($model, 'id', function ($model) {
        return $model->getFullName();
      });
    }
  }
}
