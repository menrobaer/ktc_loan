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
$this->title = "View loan : {$model->generate_code}";
$this->params['breadcrumbs'][] = ['url' => ['index'], 'class' => 'text-white', 'label' => $this->title];
$this->params['breadcrumbs'][] = "Lists";
$this->params['pageTitle'] = $this->title;
?>
<?= Modal::widget([
  'id' => 'modal-add-payment',
]) ?>
<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <div class="d-flex">
          <a class="btn bg-gradient-default" href="<?= Url::to(['index']) ?>"><i class="fas fa-long-arrow-alt-left pe-2"></i>Back to list</a>
          <div class="ms-auto">
            <div class="d-flex gap-3">
              <?php if ($model->paid_amount == 0) { ?>
                <a class="btn bg-gradient-info" href="<?= Url::to(['update', 'id' => $model->id]) ?>"><i class="far fa-edit pe-2"></i>Edit Loan</a>
              <?php } ?>
              <div class="dropdown">
                <button class="btn bg-gradient-warning dropdown-toggle" type="button" id="dropdownMenuExport" data-bs-toggle="dropdown">
                  File Export <i class="fas fa-caret-down ps-2"></i>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuExport">
                  <li><a class="dropdown-item" href="<?= Url::to(['export-to-word', 'code' => $model->generate_code]) ?>">Contract as Word</a></li>
                  <li><a class="dropdown-item" target="_blank" href="<?= Url::to(['export-to-pdf', 'code' => $model->generate_code]) ?>">Contract as PDF</a></li>
                  <li><a class="dropdown-item" target="_blank" href="<?= Url::to(['view-term', 'code' => $model->generate_code]) ?>">Payment Sheet</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <h3 class="text-center mb-3">View Loan</h3>
        <div class="d-flex justify-content-between">
          <div>
            <div>Customer ID: <?= $model->customer_code ?></div>
            <div>Customer Name: <?= $model->customer->name ?></div>
            <div>Phone: <?= $model->customer->phone_number ?></div>
            <div>Address: <?= $model->customer->address ?></div>
          </div>
          <div>
            <div>Code: <?= $model->generate_code ?></div>
            <div>Issued date: <?= $utils->date($model->date) ?></div>
          </div>
        </div>
        <hr>
        <div class="d-flex mb-2 justify-content-between">
          <div>Loan amount: <strong><?= $utils->DollarFormat($model->loan_amount - $model->deposit_amount) ?></strong></div>
          <div>Payment Term: <strong><?= $model->paymentTerm->name ?></strong></div>
          <div>Interest (<?= $model->interest_rate ?>%): <strong><?= $utils->DollarFormat($model->interest_rate_amount) ?></strong></div>
        </div>
        <div class="d-flex justify-content-between">
          <div>Service charge (<?= $model->service_charge ?>%): <strong><?= $utils->DollarFormat($model->service_charge_amount) ?></strong></div>
          <div>To paid amount: <strong><?= $utils->DollarFormat($model->grand_total) ?></strong></div>
        </div>
        <hr>
        <p class="text-muted"><i class="fas fa-exclamation-circle"></i> Click on <span class="text-primary">[Payment Date]</span> to make payment</p>
        <div class="table-responsive border-radius-lg">
          <table class="table text-sm table-striped table-bordered">
            <thead class="bg-default">
              <tr>
                <th class="text-center text-white">#</th>
                <th scope="col" class="pe-2 text-start ps-2 text-white">Payment Date</th>
                <th scope="col" class="pe-2 text-end ps-2 text-white">Value (USD)</th>
                <th scope="col" class="pe-2 text-end ps-2 text-white">Balance (USD)</th>
                <th scope="col" class="pe-2 text-start ps-2 text-white">Penalty</th>
                <th scope="col" class="pe-2 text-start ps-2 text-white">Note</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $i = 0;
              foreach ($loanTerms as $key => $term) :
                $i += 1;
              ?>
                <tr <?= $term->status == 1 ? "class='table-warning'" : "" ?>>
                  <td class="text-center"><?= $model->service_charge_amount > 0 && $i == 1 ? "{$i}+{$model->service_charge}%" : $i ?></td>
                  <td scope="col" class="pe-2 text-start ps-2">
                    <?php
                    if ($term->status == 0) :
                    ?>
                      <a href="#" data-bs-toggle="modal" data-bs-target="#modal-add-payment" data-title="Enter payment" data-url="<?= \yii\helpers\Url::to(['loan/add-payment', 'code' => $model->generate_code, 'termID' => $term->id]) ?>" ]><span data-toggle="tooltip" title="Click to payment"><?= $utils->date($term->date, "d M Y") ?></span></a>
                    <?php else : ?>
                      <?= $utils->date($term->date, "d M Y") ?>
                    <?php endif; ?>
                  </td>
                  <td scope="col" class="pe-2 text-end ps-2">
                    <div class="d-flex justify-content-between">
                      <div>$</div>
                      <div><?= $term->amount ?></div>
                    </div>
                  </td>
                  <td scope="col" class="pe-2 text-end ps-2">
                    <div class="d-flex justify-content-between">
                      <div>$</div>
                      <div><?= $term->original_balance_amount ?></div>
                    </div>
                  </td>
                  <td scope="col" class="pe-2 text-start ps-2"></td>
                  <td scope="col" class="pe-2 text-start ps-2"></td>
                </tr>
              <?php
              endforeach;
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h6>Payments</h6>
        <div class="progress progress-sm rounded-0 mb-1">
          <?php
          $progress = $utils->calculateProgress($model->paid_amount, $model->grand_total);
          ?>
          <div class="progress-bar bg-success" style="width: <?= $progress ?>%" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <p class="text-muted text-weight-bolder small"> <?= $utils->DollarFormat($model->original_amount - $model->original_balance_amount) ?> of <?= $utils->DollarFormat($model->original_amount) ?> </p>
        <?php
        if ($model->original_balance_amount > 0  && 1 === 2) {
          echo Html::tag('div', "<span><i class='fas fa-chevron-right'></i> Enter a Payment</span>", [
            'class' => 'text-primary',
            'style' => 'cursor:pointer',
            'data' => [
              'bs-toggle' => 'modal',
              'bs-target' => '#modal-add-payment',
              'title' => 'Enter payment',
              'url' => \yii\helpers\Url::to(['loan/add-payment', 'code' => $model->generate_code])
            ]
          ]);
        }
        ?>
      </div>
    </div>
    <hr class="my-2">
    <div class="card">
      <div class="card-body">
        <h6>Loan activity</h6>
        <div class="timeline timeline-one-side" data-timeline-axis-style="dotted">
          <?php
          foreach ($activities as $key => $activity) :
          ?>
            <div class="timeline-block mb-3">
              <span class="timeline-step">
                <i class="fas fa-check-circle text-success text-gradient"></i>
              </span>
              <div class="timeline-content">
                <h6 class="text-dark text-sm font-weight-bold mb-0"><?= $activity->getTypeAsText() ?></h6>
                <p class="text-secondary font-weight-bold text-xs mt-1 mb-0"><?= "{$utils->dateTime($activity->created_at)} | {$activity->getUser()}" ?></p>
                <?= $activity->getAttachment() ?>
              </div>
            </div>
          <?php
          endforeach;
          ?>

        </div>
      </div>
    </div>
  </div>
</div>

<?php
$script = <<<JS

  $('#modal-add-payment').on('shown.bs.modal', function () {
      $('#loanpayment-officer_id').select2({
        placeholder: "Select an option",
        dropdownParent: $('#modal-add-payment')
      }).select2('open');
  });

JS;
$this->registerJs($script);

?>