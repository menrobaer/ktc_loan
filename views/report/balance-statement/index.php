<?php

use app\widgets\Modal;
use yii\bootstrap5\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$base_url = Yii::getAlias("@web");

/** @var \app\components\Utils $utils */
$utils = Yii::$app->utils;

$this->title = 'Balance Statement';
$this->params['breadcrumbs'][] = ['url' => ['index'], 'class' => 'text-white', 'label' => $this->title];
$this->params['breadcrumbs'][] = "Report";
$this->params['pageTitle'] = $this->title;
?>
<div class="<?= Yii::$app->controller->action->id; ?>">
  <div class="row">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h4 class="text-center">Balance Statement Report</h4>
          <h6 class="text-center"><?= $utils->dateRange($fromDate, $toDate) ?></h6>
          <hr>
          <div class="table-responsive border-radius-lg">
            <table class="table table-striped">
              <thead class="bg-default">
                <tr>
                  <th class="text-white" width="15%">Nº</th>
                  <th class="text-white" width="60%">Customer</th>
                  <th class="text-white text-end" width="25%">Balance</th>
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
                      <td><?= $value->name ?></td>
                      <td class="text-end">
                        <?= $utils->DollarFormat($value->balance_amount) ?>
                      </td>
                    </tr>
                <?php
                  endforeach;
                endif;
                ?>
              </tbody>
              <tfoot>
                <tr>
                  <th class="text-end" colspan="2"><span class="me-3">Total Balance:</span></th>
                  <th class="text-end">
                    <?= $utils->DollarFormat(array_sum(array_column($data, 'balance_amount'))) ?>
                  </th>
                </tr>
                <tr>
                  <th class="text-end" colspan="2"><span class="me-3">Total Cash:</span></th>
                  <th class="text-end">
                    <?= $utils->DollarFormat(array_sum(array_column($data, 'installment_amount'))) ?>
                  </th>
                </tr>
                <tr>
                  <th class="text-end" colspan="2"><span class="me-3">Total Profit:</span></th>
                  <th class="text-end">
                    <?= $utils->DollarFormat($totalProfit) ?>
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
          <label for="dateRange">As of Date</label>
          <input type="text" value="<?= $fromDate . ' to ', $toDate ?>" id="dateRange" name="dateRange" class="form-control datepicker flatpickr-input mb-3" />

          <hr class="border-0">
          <button type="submit" id="submitSearch" hidden class="btn btn-warning">Apply Change</button>
          <?php ActiveForm::end(); ?>

          <?php // Html::a('<i class="far fa-file-pdf pe-2"></i>View as PDF', ['view-payment-receivable', 'selectedDate' => $selectedDate, 'selectedOfficer' => $selectedOfficer], ['target' => '_blank', 'class' => 'btn bg-gradient-success']) 
          ?>
        </div>
      </div>
    </div>
  </div>

</div>

<?php
$script = <<<JS

flatpickr("#dateRange", {
  altInput: true,
  altFormat: "d M Y",
  dateFormat: "Y-m-d",
  maxDate: 'today',
  mode: "range",
  onChange: function(selectedDates, dateStr, instance) {
    if (selectedDates.length === 2) {
      $('#submitSearch').trigger('submit');
    }
  }
});

JS;
$this->registerJs($script);
?>