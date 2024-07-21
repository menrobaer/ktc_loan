<?php

use app\models\Customer;
use app\models\Officer;
use app\models\PaymentTerm;
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

$model->service_charge = number_format($model->isNewRecord ? $setup->serviceCharge() : $model->service_charge, 2);
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

$paymentTerms = PaymentTerm::find()
  ->select(['payment_term.id', 'payment_term.name'])
  ->orderBy(['payment_term.id' => SORT_ASC])
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
  <?= $form->field($model, 'service_charge_amount')->hiddenInput()->label(false) ?>
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
          <div class="col-lg-12">
            <?= $form->field($model, 'item_name')->textInput()->label("Item name") ?>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-3">
            <?= $form->field($model, 'loan_amount')->textInput(['type' => 'number', 'step' => 0.01])->label("Loan amount (USD)") ?>
          </div>
          <div class="col-lg-3">
            <?= $form->field($model, 'payment_term_id')->dropDownList($paymentTerms, ['class' => 'has-select2', 'value' => $model->isNewRecord ? 1 : $model->payment_term_id, 'prompt' => 'Select']) ?>
          </div>
          <div class="col-lg-3">
            <?= $form->field($model, 'service_charge')->textInput(['type' => 'number', 'step' => 0.01])->label("Service Charge (%)") ?>
          </div>
          <div class="col-lg-3">
            <?= $form->field($model, 'interest_rate')->textInput(['type' => 'number', 'step' => 0.01])->label("Interest (%)") ?>
          </div>
        </div>
        <div class="row my-3">
          <div class="col-lg-12">
            <div class="border-dashed border-1 border-secondary border-radius-md p-3" id="totalAmountAsText"></div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-3">
            <?= $form->field($model, 'deposit_amount')->textInput(['type' => 'number', 'readonly' => $model->isNewRecord ? false : true, 'step' => 0.01])->label("Deposit amount (USD)") ?>
          </div>
          <div class="col-lg-3">
            <?= $form->field($model, 'payment_day')->textInput(['class' => 'form-control datepicker flatpickr-input']) ?>
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
  <div class="col-lg-4">
    <div class="card shadow-lg border-warning border-1">
      <div class="card-header pb-0 p-3">
        <h6 class="mb-0">Summary</h6>
      </div>
      <div class="card-body p-3">
        <ul class="list-group">
          <li class="list-group-item align-items-baseline border-0 d-flex justify-content-between px-3 mb-2 border-radius-lg">
            <div>Loan amount:</div>
            <div class="fw-semibold text-dark" id="label-loan-amount"><?= $utils->DollarFormat($model->loan_amount - $model->deposit_amount) ?></div>
          </li>
          <li class="list-group-item align-items-baseline border-0 d-flex justify-content-between px-3 mb-2 border-radius-lg">
            <div>Interest rate (<span id="label-interest-rate"><?= $model->interest_rate ?></span>%):</div>
            <div class="fw-semibold text-dark" id="label-interest-rate-amount"><?= $utils->DollarFormat($model->interest_rate_amount) ?></div>
          </li>
          <li class="list-group-item align-items-baseline border-0 d-flex justify-content-between px-3 mb-2 border-radius-lg">
            <div>Service charge (<span id="label-service-charge"><?= $model->service_charge ?></span>%):</div>
            <div class="fw-semibold text-dark" id="label-service-charge-amount"><?= $utils->DollarFormat($model->service_charge_amount) ?></div>
          </li>
          <li class="list-group-item align-items-baseline border-0 d-flex justify-content-between px-3 mb-2 border-radius-lg">
            <div>Profit (<span id="label-profit-margin"><?= $model->isNewRecord ? 0 : number_format((($model->interest_rate_amount + $model->service_charge_amount) / $model->grand_total) * 100, 2) ?></span>%):</div>
            <div class="fw-semibold text-dark" id="label-profit-amount"><?= $utils->DollarFormat($model->profit_amount) ?></div>
          </li>
          <li class="bg-light list-group-item align-items-baseline border-0 d-flex justify-content-between px-3 mb-2 border-radius-lg">
            <div>Total amount to pay:</div>
            <div class="fw-semibold text-dark" id="label-grand-total"><?= $utils->DollarFormat($model->grand_total) ?></div>
          </li>
          <li class="list-group-item align-items-baseline border-0 d-flex justify-content-between px-3 mb-2 border-radius-lg">
            <div>Installment payment:
            </div>
            <div class="fw-semibold text-dark" id="label-installment-payment"><?= $utils->DollarFormat($model->installment_amount) ?></div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<?php ActiveForm::end(); ?>

<?php
$this->registerJsVar('isNewRecord', $model->isNewRecord);
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
    altFormat: 'F j, Y',
    dateFormat: 'Y-m-d',
  });

  // $("#loan-payment_day").flatpickr({
  //   altInput: true,
  //   altFormat: 'J',
  //   dateFormat: 'd',
  //   onOpen: function(selectedDates, dateStr, instance) {
  //     const monthElements = instance.calendarContainer.querySelectorAll('.flatpickr-month');
  //     monthElements.forEach(element => {
  //       element.style.display = 'none';
  //     });

  //     const weekDays = instance.calendarContainer.querySelector('.flatpickr-weekdays');
  //     if (weekDays) {
  //       weekDays.style.display = 'none';
  //     }

  //     const prevArrow = instance.calendarContainer.querySelector('.flatpickr-prev-month');
  //     if (prevArrow) {
  //       prevArrow.style.display = 'none';
  //     }

  //     const nextArrow = instance.calendarContainer.querySelector('.flatpickr-next-month');
  //     if (nextArrow) {
  //       nextArrow.style.display = 'none';
  //     }
  //   }
  // });

  $("#loan-loan_amount").on("change",function(){
    var amount = parseFloat($(this).val());
    if (isNaN(amount)) {
      $('#totalAmountAsText').text('Please enter a valid number.');
    } else {
      var amountInWords = convertAmountToWords(amount);
      $('#totalAmountAsText').text(amountInWords);
    }
  });

  $(document).on("change","#loan-loan_amount, #loan-deposit_amount, #loan-payment_term_id, #loan-service_charge, #loan-interest_rate", function(){
    var loanAmount = toNumber($("#loan-loan_amount").val());
    var depositAmount = toNumber($("#loan-deposit_amount").val());
    loanAmount -= depositAmount;
    var serviceCharge = toNumber($("#loan-service_charge").val());
    var interestRate = toNumber($("#loan-interest_rate").val());

    $("#label-loan-amount").text(`$ \${parseFloat(loanAmount).toFixed(2)}`);
    $("#label-service-charge").text(parseFloat(serviceCharge).toFixed(2));
    $("#label-service-charge-amount").text(`$ \${parseFloat(calculateServiceCharge()).toFixed(2)}`);
    $("#label-interest-rate").text(parseFloat(interestRate).toFixed(2));
    $("#label-interest-rate-amount").text(`$ \${parseFloat(calculateInterest()).toFixed(2)}`);
    $("#label-profit-margin").text(`$ \${parseFloat(calculateMargin()).toFixed(2)}`);
    $("#label-profit-amount").text(`$ \${parseFloat(calculateProfit()).toFixed(2)}`);
    $("#label-grand-total").text(`$ \${parseFloat(calculateGrandTotal()).toFixed(2)}`);
    $("#label-installment-payment").text(`$ \${parseFloat(calculateInstallment()).toFixed(2)}`);

    $("#loan-interest_rate_amount").val(calculateInterest());
    $("#loan-service_charge_amount").val(calculateServiceCharge());
    $("#loan-installment_amount").val(calculateInstallment());
    $("#loan-profit_amount").val(calculateProfit());
    $("#loan-grand_total").val(calculateGrandTotal());
  });


  if(!isNewRecord){
    $('#totalAmountAsText').text(convertAmountToWords(parseFloat("$model->loan_amount")));
  }else{

    $("#loan-deposit_amount").on("change",function(){
      var maxDepositAmount = toNumber($("#loan-loan_amount").val());
      var amount = parseFloat($(this).val());
      if(amount >= maxDepositAmount){
        $(this).val(parseFloat(maxDepositAmount * (80 / 100)).toFixed(2));
        $(this).select();
      }
    });
  }

  function calculateInterest(){
    var totalAmount = toNumber($("#loan-loan_amount").val());
    var depositAmount = toNumber($("#loan-deposit_amount").val());
    totalAmount -= depositAmount;
    var interestRate = toNumber($("#loan-interest_rate").val());
    if(interestRate > 0){
      var paymentTerm = $("#loan-payment_term_id").val();
      var total = paymentTerm * (totalAmount * interestRate / 100);
      return parseFloat(total).toFixed(2);
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
      return parseFloat(total).toFixed(2);
    }
    return 0;
  }

  function calculateProfit(){
    var totalInterest = toNumber(calculateInterest());
    var totalServiceCharge = toNumber(calculateServiceCharge());
    var total = totalInterest + totalServiceCharge;
    return parseFloat(total).toFixed(2);
  }

  function calculateGrandTotal(){
    var totalAmount = toNumber($("#loan-loan_amount").val());
    var depositAmount = toNumber($("#loan-deposit_amount").val());
    totalAmount -= depositAmount;
    var totalProfit = toNumber(calculateProfit());
    var total = totalAmount + totalProfit;
    return parseFloat(total).toFixed(2);
  }

  function calculateMargin(){
    var totalProfit = toNumber(calculateProfit());
    var grandTotal = toNumber(calculateGrandTotal());
    var total = totalProfit / grandTotal * 100;
    return parseFloat(total).toFixed(2);
  }

  function calculateInstallment(){
    var grandTotal = toNumber(calculateGrandTotal());
    var totalServiceCharge = toNumber(calculateServiceCharge());
    var paymentTerm = $("#loan-payment_term_id").val();
    var total = (grandTotal - totalServiceCharge) / paymentTerm;
    return parseFloat(total).toFixed(2);
  }

JS;
$this->registerJs($script);
?>