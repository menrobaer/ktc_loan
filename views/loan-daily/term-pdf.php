<?php
/* @var $this yii\web\View */

/** @var \app\components\Utils $utils */
$utils = Yii::$app->utils;
?>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html" charset="Windows-1252">
</head>

<body>
  <table style="width: 100%; border-collapse:collapse;">
    <tr>
      <td style="text-align: center; font-size:12pt;">
        <strong>តារាងកាលបរិច្ឆេទ និងចំនួនទឹកប្រាក់ត្រូវបង់</strong>
      </td>
    </tr>
  </table>
  <br />
  <table style="width: 100%; border-collapse:collapse;">
    <tr>
      <td width="35%" style="font-size:11pt;">លេខកូដអតិថិជន៖ <strong><?= $model->customer_code ?></strong></td>
      <td width="35%" style="font-size:11pt;">ឈ្មោះអតិថិជន៖ <strong><?= $profile->name ?></strong></td>
      <td width="30%" style="font-size:11pt;">លេខទូរស័ព្ទ៖ <strong><?= $profile->phone ?></strong></td>
    </tr>
    <tr>
      <td width="35%" style="font-size:11pt;">កាលបរិច្ឆេទ៖ <strong><?= $utils->maskDateKH($model->date, "d M Y") ?></strong></td>
      <td width="35%" style="font-size:11pt;">ទឹកប្រាក់ចំនួន៖ <strong><?= $utils->DollarFormat($model->loan_amount - $model->deposit_amount) ?></strong></td>
      <td width="30%" style="font-size:11pt;">សេវា៖ <strong><?= $utils->DollarFormat($model->service_charge_amount) ?></strong></td>
    </tr>
    <tr>
      <td colspan="2" width="70%" style="font-size:11pt;">មន្ត្រីឥណទាន៖ <strong><?= "{$model->officer->getFullName()} - {$model->officer->profile->phone}" ?></strong></td>
      <td width="30%" style="font-size:11pt;">ចំនួនថ្ងៃ៖ <strong><?= $model->payment_term_id ?> ថ្ងៃ</strong></td>
    </tr>
  </table>
  <br>
  <table id="table-sheet" style="width: 100%; border-collapse:collapse;" border="1">
    <thead>
      <tr>
        <th width="6%" style="text-align: center;">#</th>
        <th width="30%" style="text-align: left;">កាលបរិច្ឆេទបង់ប្រាក់</th>
        <th width="16%" style="text-align: right;">ទឹកប្រាក់ត្រូវបង់</th>
        <th width="16%" style="text-align: right;">ប្រាក់ដើម​</th>
        <th width="16%" style="text-align: right;">ការប្រាក់</th>
        <th width="16%" style="text-align: right;">សមតុល្យ</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $i = 0;
      foreach ($loanTerms as $key => $term) :
        $i += 1;
      ?>
        <tr>
          <td style="text-align: center;"><?= $i ?></td>
          <td><?= $utils->maskDateKH($term->date, "D d M Y") ?></td>
          <td style="text-align: right;">
            <?= $utils->DollarFormat($term->amount) ?>
          </td>
          <td style="text-align: right;">
            <?= $utils->DollarFormat($term->original_paid) ?>
          </td>
          <td style="text-align: right;">
            <?= $utils->DollarFormat($term->interest_amount) ?>
          </td>
          <td style="text-align: right;">
            <?= $utils->DollarFormat($term->original_balance_amount) ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>

</html>