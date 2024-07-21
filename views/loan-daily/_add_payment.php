<?php

use app\models\Officer;
use app\models\PaymentMethod;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;

/** @var \app\components\Setup $setup */
$setup = Yii::$app->setup;
$officers = $setup->getOfficers('map');

$paymentMethods = PaymentMethod::find()
  ->select(['payment_method.id', 'payment_method.name'])
  ->orderBy(['payment_method.id' => SORT_ASC])
  ->all();
$paymentMethods = ArrayHelper::map($paymentMethods, 'id', 'name');

$form = ActiveForm::begin([
  'id' => $model->formName(),
  'enableAjaxValidation' => false,
  'enableClientValidation' => true,
  'options' => ['enctype' => 'multipart/form-data'],
]); ?>

<?= $form->field($model, 'file')->fileInput(['multiple' => true]) ?>

<?= $form->field($model, 'date')->textInput(['class' => 'form-control datepicker flatpickr-input', 'value' => $model->isNewRecord ? date("Y-m-d") : $model->date]) ?>

<?= $form->field($model, 'officer_id')->dropDownList($officers, ['class' => 'has-select2', 'prompt' => 'Select a officer']) ?>

<?= $form->field($model, 'method_id')->dropDownList($paymentMethods, ['class' => 'has-select2', 'value' => $model->isNewRecord ? 1 : $model->payment_term_id, 'prompt' => 'Select']) ?>

<?= $form->field($model, 'term_date')->textInput(['readonly' => true, 'value' => Yii::$app->utils->date($loanTerm->date)])->label("Payment for date") ?>
<?= $form->field($model, 'amount')->textInput(['readonly' => true, 'type' => 'number', 'step' => 0.01, 'value' => intval($loanTerm->amount)])->label("Payment amount (KHR)") ?>

<div class="d-flex flex-row-reverse gap-2">
  <?= Html::button('Cancel', ['class' => 'btn bg-gradient-secondary', 'id' => 'btn-dismiss-modal']) ?>
  <?= Html::submitButton('Save changes', ['class' => 'btn bg-gradient-primary']) ?>
</div>

<?php ActiveForm::end() ?>

<?php
$script = <<<JS

  $("#loanpayment-date").flatpickr({
    altInput: true,
    altFormat: 'F j, Y',
    dateFormat: 'Y-m-d',
    maxDate: 'today',
  });

  $('.has-select2').select2({
    dropdownParent: $('#modal-add-payment'),
    width: "100%",
    // placeholder: $(this).attr('prompt'),
    placeholder: "Select an option",
  });

  // $("#loanpayment-officer_id").select2('open').trigger("change.select2");
  $("#loanpayment-officer_id").change(function(){
    $("#loanpayment-method_id").select2('open');
  });

  $('#totalAmountAsText').text(convertAmountToWords(parseFloat($("#loanpayment-amount").val()).toFixed(2)));

JS;
$this->registerJs($script);
?>