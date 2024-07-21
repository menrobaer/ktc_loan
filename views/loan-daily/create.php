<?php

use app\models\Customer;
use app\models\Officer;
use app\models\PaymentTermDaily;
use app\widgets\Modal;
use yii\bootstrap5\ActiveForm;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$base_url = Yii::getAlias("@web");

/** @var \app\components\Utils $utils */
$utils = Yii::$app->utils;

/** @var \app\components\Setup $setup */
$setup = Yii::$app->setup;

$model->service_charge_amount = $model->isNewRecord ? $setup->serviceCharge() : $model->service_charge_amount;
$model->interest_rate = number_format($model->isNewRecord ? $setup->interestRate() : $model->interest_rate, 2);

$this->title = $model->isNewRecord ? 'Create new loan' : "Update loan";
$this->params['breadcrumbs'][] = ['url' => ['index'], 'class' => 'text-white', 'label' => $this->title];
$this->params['breadcrumbs'][] = "Lists";
$this->params['pageTitle'] = $this->title;

$customers = Customer::find()
  ->select(['customer.id', 'customer.name'])
  ->orderBy(['customer.name' => SORT_ASC])
  ->all();
$customers = ArrayHelper::map($customers, 'id', 'name');

$officers = $setup->getOfficers('map');

$paymentTerms = PaymentTermDaily::find()
  ->select(['id', 'name'])
  ->orderBy(['id' => SORT_ASC])
  ->all();
$paymentTerms = ArrayHelper::map($paymentTerms, 'id', 'name');

$dollarTemplate = "{label}<div class='input-group'>
  <span class='input-group-text'>$</span>
{input}
</div>{error}{hint}";
?>
<?php
$validationUrl = ['loan/validation'];
if (!$model->isNewRecord) {
  $validationUrl['id'] = $model->id;
}
$form = ActiveForm::begin([
  'id' => 'frm-loan',
  'enableAjaxValidation' => true,
  'enableClientValidation' => true,
  'validationUrl' => $validationUrl
]);
?>
<div class="d-none" hidden>
  <?= $form->field($model, 'interest_rate_amount')->hiddenInput()->label(false) ?>
  <?= $form->field($model, 'installment_amount')->hiddenInput()->label(false) ?>
  <?= $form->field($model, 'profit_amount')->hiddenInput()->label(false) ?>
  <?= $form->field($model, 'grand_total')->hiddenInput()->label(false) ?>
</div>
<div class="row" id="rowFormLoan">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header p-3">
        <h5 class="mb-2"><?= $this->title ?></h5>
      </div>
      <div class="card-body p-3">
        <a class="btn bg-gradient-default" href="<?= Url::to(['index']) ?>"><i class="fas fa-long-arrow-alt-left pe-2"></i>Back to list</a>
        <div class="row">
          <div class="col-lg-6">
            <?= $form->field($model, 'customer_id')->dropDownList($customers, ['prompt' => 'Select a customer', 'class' => 'has-select2']) ?>
          </div>
          <div class="col-lg-3">
            <?= $form->field($model, 'officer_id')->dropDownList($officers, ['class' => 'has-select2', 'prompt' => 'Select a officer']) ?>
          </div>
          <div class="col-lg-3">
            <?= $form->field($model, 'date')->textInput(['class' => 'form-control datepicker flatpickr-input', 'value' => $model->isNewRecord ? date("Y-m-d") : $model->date]) ?>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-3">
            <?= $form->field($model, 'loan_amount_alt')->textInput(['type' => 'text', 'value' => number_format($model->loan_amount, 0)])->label("Loan amount (KHR)") ?>
            <?= $form->field($model, 'loan_amount')->hiddenInput()->label(false) ?>
          </div>
          <div class="col-lg-3">
            <?= $form->field($model, 'payment_term_id')->dropDownList($paymentTerms, ['class' => 'has-select2', 'value' => $model->isNewRecord ? 30 : $model->payment_term_id, 'prompt' => 'Select']) ?>
          </div>
          <div class="col-lg-3">
            <?= $form->field($model, 'is_exclude_weekend')->dropDownList([0 => 'Not Exclude', 1 => 'Exclude Sat & Sun'], ['value' => $model->isNewRecord ? 1 : $model->is_exclude_weekend, 'class' => 'custom-select'])->label("Exclude weekend") ?>
          </div>
          <div class="col-lg-3">
            <?= $form->field($model, 'interest_rate')->textInput(['type' => 'number', 'step' => 0.01])->label("Interest (%)") ?>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-3">
            <?= $form->field($model, 'service_charge_amount_alt')->textInput(['type' => 'text', 'value' => number_format($model->service_charge_amount, 0), 'step' => 0.01])->label("Service charge (KHR)") ?>
            <?= $form->field($model, 'service_charge_amount')->hiddenInput()->label(false) ?>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-6">
            <?= $form->field($model, 'remark')->textArea(['rows' => 5]) ?>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="d-flex flex-row-reverse gap-2">
          <?= Html::a('Cancel', ['index'], ['class' => 'btn bg-gradient-secondary']) ?>
          <?= Html::submitButton('Save changes', ['class' => 'btn bg-gradient-primary']) ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php ActiveForm::end(); ?>

<?php
$this->registerJsVar('isNewRecord', $model->isNewRecord);
$this->registerJsVar('currency', Yii::$app->setup->currency());
$script = <<<JS

  $('.has-select2').select2({
    allowClear: true,
    // placeholder: $(this).attr('prompt'),
    placeholder: "Select an option",
  });

  $("#loan-date").flatpickr({
    altInput: true,
    altFormat: 'F j, Y',
    dateFormat: 'Y-m-d',
    maxDate: 'today',
  });

  $("#loan-payment_day").flatpickr({
    altInput: true,
    altFormat: 'J',
    dateFormat: 'd',
    onOpen: function(selectedDates, dateStr, instance) {
      const monthElements = instance.calendarContainer.querySelectorAll('.flatpickr-month');
      monthElements.forEach(element => {
        element.style.display = 'none';
      });

      const weekDays = instance.calendarContainer.querySelector('.flatpickr-weekdays');
      if (weekDays) {
        weekDays.style.display = 'none';
      }

      const prevArrow = instance.calendarContainer.querySelector('.flatpickr-prev-month');
      if (prevArrow) {
        prevArrow.style.display = 'none';
      }

      const nextArrow = instance.calendarContainer.querySelector('.flatpickr-next-month');
      if (nextArrow) {
        nextArrow.style.display = 'none';
      }
    }
  });

  // $("#loan-loan_amount").on("change",function(){
  //   // var amount = parseFloat($(this).val());
  //   // if (isNaN(amount)) {
  //   //   $('#totalAmountAsText').text('Please enter a valid number.');
  //   // }
  // });

  function formatCurrency(amount) {
    if(currency == '$'){
      return parseFloat(amount).toFixed(2);
    }
    return parseFloat(amount).toFixed(0);
  }
  function thousandSeparator(amount) {
    return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

  function calculateInterest(){
    var totalAmount = toNumber($("#loan-loan_amount").val());
    var depositAmount = toNumber($("#loan-deposit_amount").val());
    totalAmount -= depositAmount;
    var interestRate = toNumber($("#loan-interest_rate").val());
    if(interestRate > 0){
      var paymentTerm = $("#loan-payment_term_id").val();
      var total = paymentTerm * (totalAmount * interestRate / 100);
      return formatCurrency(total);
    }
    return 0;
  }

  function calculateServiceCharge(){
    var totalAmount = toNumber($("#loan-loan_amount").val());
    var depositAmount = toNumber($("#loan-deposit_amount").val());
    totalAmount -= depositAmount;
    var serviceCharge = toNumber($("#loan-service_charge").val());
    if(serviceCharge > 0){
      var total = totalAmount * serviceCharge / 100;
      return formatCurrency(total);
    }
    return 0;
  }

  function calculateProfit(){
    var totalInterest = toNumber(calculateInterest());
    var totalServiceCharge = toNumber(calculateServiceCharge());
    var total = totalInterest + totalServiceCharge;
    return formatCurrency(total);
  }

  function calculateGrandTotal(){
    var totalAmount = toNumber($("#loan-loan_amount").val());
    var depositAmount = toNumber($("#loan-deposit_amount").val());
    totalAmount -= depositAmount;
    var totalProfit = toNumber(calculateProfit());
    var total = totalAmount + totalProfit;
    return formatCurrency(total);
  }

  function calculateMargin(){
    var totalProfit = toNumber(calculateProfit());
    var grandTotal = toNumber(calculateGrandTotal());
    var total = totalProfit / grandTotal * 100;
    return formatCurrency(total);
  }

  function calculateInstallment(){
    var grandTotal = toNumber(calculateGrandTotal());
    var totalServiceCharge = toNumber(calculateServiceCharge());
    var paymentTerm = $("#loan-payment_term_id").val();
    var total = (grandTotal - totalServiceCharge) / paymentTerm;
    return formatCurrency(total);
  }

  $('#loan-loan_amount_alt').on('input', function() {
      var value = $(this).val();
      $("#loan-loan_amount").val(parseFloat(value.replace(/,/g, '')).toFixed());
      var formattedValue = formatNumber(value);
      $(this).val(formattedValue);
  });

  if(!isNewRecord){
    $('#loan-loan_amount_alt').trigger('change');
  }

  function formatNumber(value) {
      var number = value.replace(/,/g, '');
      if (!isNaN(number)) {
          number = parseFloat(number).toLocaleString();
          return number;
      }
      return value;
  }
  $("#loan-loan_amount_alt, #loan-service_charge_amount_alt").on("click", function(){
    $(this).select();
  });

JS;
$this->registerJs($script);
?>