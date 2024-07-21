<?php

use app\assets\Select2Asset;
use app\models\Customer;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

Select2Asset::register($this);

$inputFloatLabel = '<div class="form-label-group">{input} {label} {error}{hint}</div>';
$customerArr = Customer::find()
  ->select(['customer.id', 'customer.name'])
  ->innerJoinWith('loans')
  ->andWhere(['!=', 'customer.name', ''])
  ->orderBy(['customer.name' => SORT_ASC])
  ->all();
$customerArr = ArrayHelper::map($customerArr, 'id', 'name');
?>
<style>
  .field-loansearch-customer_id,
  .field-loansearch-type_id {
    width: 250px !important;
  }

  .field-loansearch-source {
    width: 160px !important;
  }

  /* Example: make the dropdown menu wider */
  .select2-container .select2-dropdown {
    min-width: 200px;
    /* Adjust as needed */
  }
</style>
<div class="formLoanSearch">
  <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'options' => ['data-pjax' => true, 'id' => 'formLoanSearch'],
    'method' => 'get',
  ]); ?>

  <div class="d-flex align-items-end">
    <?= Html::a("+ Create <span class='d-none d-lg-inline'>Loan</span>", ['create'], [
      'class' => 'btn btn-warning',
    ]) ?>
    <div class="ms-3">
      <?= $form->field($model, 'customer_id')->dropDownList($customerArr, ['prompt' => 'All'])->label('Customer Name');
      ?>
    </div>
  </div>
  <?php ActiveForm::end(); ?>

</div>
<?php
$script = <<<JS

    $(document).on("change","#loansearch-customer_id, #loansearch-type_id, #loansearch-source", function(){
        $('#formLoanSearch').trigger('submit');
    });

    $("#loansearch-customer_id").select2();

JS;
$this->registerJs($script);

?>