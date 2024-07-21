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
  ->andWhere(['!=', 'customer.name', ''])
  ->orderBy(['customer.name' => SORT_ASC])
  ->all();
$customerArr = ArrayHelper::map($customerArr, 'id', 'name');
?>
<style>
  .field-customersearch-customer_id,
  .field-customersearch-type_id {
    width: 250px !important;
  }

  .field-customersearch-source {
    width: 160px !important;
  }

  /* Example: make the dropdown menu wider */
  .select2-container .select2-dropdown {
    min-width: 200px;
    /* Adjust as needed */
  }
</style>
<div class="formCustomerSearch">
  <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'options' => ['data-pjax' => true, 'id' => 'formCustomerSearch'],
    'method' => 'get',
  ]); ?>

  <div class="d-flex align-items-end">
    <?= Html::button("+ Create <span class='d-none d-lg-inline'>Customer</span>", [
      'class' => 'btn btn-warning',
      'data' => [
        'bs-toggle' => 'modal',
        'bs-target' => '#modal-customer',
        'title' => 'Create Customer',
        'url' => \yii\helpers\Url::to(['customer/create'])
      ]
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

    $(document).on("change","#customersearch-customer_id, #customersearch-type_id, #customersearch-source", function(){
        $('#formCustomerSearch').trigger('submit');
    });

    $("#customersearch-customer_id").select2();

JS;
$this->registerJs($script);

?>