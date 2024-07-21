<?php

use app\widgets\Modal;
use yii\bootstrap5\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$base_url = Yii::getAlias("@web");

/** @var \app\components\Utils $utils */
$utils = Yii::$app->utils;

/** @var \app\components\Setup $setup */
$setup = Yii::$app->setup;
$officers = $setup->getOfficers('map');

$this->title = 'Payment Receivable';
$this->params['breadcrumbs'][] = ['url' => ['index'], 'class' => 'text-white', 'label' => $this->title];
$this->params['breadcrumbs'][] = "Report";
$this->params['pageTitle'] = $this->title;
?>
<div class="<?= Yii::$app->controller->action->id; ?>">
  <div class="row">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h4 class="text-center">Payment Receivable Report</h4>
          <h6 class="text-center"><?= $utils->date($selectedDate, "d F Y") ?></h6>
          <hr>
          <div class="table-responsive border-radius-lg">
            <table class="table table-striped">
              <thead class="bg-default">
                <tr>
                  <th class="text-white" width="15%">Nº</th>
                  <th class="text-white" width="35%"><?= Yii::$app->request->get('selectedOfficer') == '' ? 'Officer name' : 'Customer name' ?></th>
                  <th class="text-white text-end" width="25%">Amount</th>
                  <th class="text-white text-end" width="25%">Received amount</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if (!empty($data)) :
                  foreach ($data as $key => $value) :
                    $key += 1;
                    $value = (object) $value;
                ?>
                    <tr>
                      <td><?= $key ?></td>
                      <td><?= Yii::$app->request->get('selectedOfficer') == '' ?  $value->officer_name : $value->customer_name ?></td>
                      <td class="text-end">
                        <?= $utils->DollarFormat($value->amount) ?>
                      </td>
                      <td class="text-end">
                        <?= $utils->DollarFormat($value->paid) ?>
                      </td>
                    </tr>
                <?php
                  endforeach;
                endif;
                ?>
              </tbody>
              <tfoot>
                <tr>
                  <th class="text-end" colspan="2"><span class="me-3">Total amount:</span></th>
                  <th class="text-end">
                    <?= $utils->DollarFormat(array_sum(array_column($data, 'amount'))) ?>
                  </th>
                  <th class="text-end">
                    <?= $utils->DollarFormat(array_sum(array_column($data, 'paid'))) ?>
                  </th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="card">
        <div class="card-body">
          <?php
          $form = ActiveForm::begin([
            'action' => [Yii::$app->controller->action->id],
            'method' => 'get',
            'options' => ['autocomplete' => 'off'],
          ]);
          ?>
          <label for="selectedDate">As of Day</label>
          <input type="text" value="<?= $selectedDate ?>" id="selectedDate" name="selectedDate" class="form-control datepicker flatpickr-input mb-3" />

          <label for="selectedOfficer">Officer</label>
          <?= Html::dropDownList('selectedOfficer', Yii::$app->request->get('selectedOfficer'), $officers, ['id' => 'selectedOfficer', 'prompt' => 'All', 'class' => 'form-control custom-select']) ?>
          <hr class="border-0">
          <button type="submit" id="submitSearch" hidden class="btn btn-warning">Apply Change</button>
          <?php ActiveForm::end(); ?>

          <?= Html::a('<i class="far fa-file-pdf pe-2"></i>View as PDF', ['view-payment-receivable', 'selectedDate' => $selectedDate, 'selectedOfficer' => $selectedOfficer], ['target' => '_blank', 'class' => 'btn bg-gradient-success']) ?>
        </div>
      </div>
    </div>
  </div>

</div>

<?php
$script = <<<JS

  $("#selectedDate").flatpickr({
    altInput: true,
    altFormat: 'F j, Y',
    dateFormat: 'Y-m-d',
    // maxDate: 'today',
    onChange: function(selectedDates, dateStr, instance) {
      $('#submitSearch').trigger('submit');
    }
  });

  $("#selectedOfficer").on("change", function(){
    $('#submitSearch').trigger('submit');
  });

JS;
$this->registerJs($script);
?>