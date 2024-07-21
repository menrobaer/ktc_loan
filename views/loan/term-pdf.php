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
      <td width="30%" style="font-size:11pt;">ឈ្មោះអតិថិជន៖ <strong><?= $profile->name ?></strong></td>
      <td width="30%" style="font-size:11pt;">លេខទូរស័ព្ទ៖ <strong><?= $profile->phone ?></strong></td>
      <td width="40%" style="font-size:11pt;">កាលបរិច្ឆេទ៖ <strong><?= $utils->maskDateKH($model->date, "d M Y") ?></strong></td>
    </tr>
    <tr>
      <td width="50%" style="font-size:11pt;">ផលិផល៖ <strong><?= $model->item_name  ?></strong></td>
      <td width="25%" style="font-size:11pt;">តំលៃ៖ <strong><?= $utils->DollarFormat($model->loan_amount - $model->deposit_amount) ?></strong></td>
      <td width="25%" style="font-size:11pt;">សេវា(<?= $model->service_charge ?>%)៖ <strong><?= $utils->DollarFormat($model->service_charge_amount) ?></strong></td>
    </tr>
    <tr>
      <td width="30%" style="font-size:11pt;">ការប្រាក់៖ <strong><?= $model->interest_rate  ?>%</strong></td>
      <td width="30%" style="font-size:11pt;">ការប្រាក់សរុប៖ <strong><?= $utils->DollarFormat($model->interest_rate_amount) ?></strong></td>
      <td width="40%" style="font-size:11pt;">ចំនួនខែ៖ <strong><?= $model->payment_term_id ?> ខែ</strong></td>
    </tr>
    <tr>
      <td width="25%" style="font-size:11pt;">ទឹកប្រាក់សរុបដែលត្រូវបង់៖ <strong><?= $utils->DollarFormat($model->grand_total) ?></strong></td>
    </tr>
  </table>
  <br>
  <table id="table-sheet" style="width: 100%; border-collapse:collapse;" border="1">
    <thead>
      <tr>
        <th width="5%" style="text-align: center;">#</th>
        <th width="25%" style="text-align: left;">កាលបរិច្ឆេទបង់ប្រាក់</th>
        <th width="20%" style="text-align: right;">ទឹកប្រាក់ត្រូវបង់</th>
        <th width="20%" style="text-align: right;">ប្រាក់ដើម​នៅ​សល់</th>
        <th width="20%">ប្រាក់ពិន័យ 1-3%</th>
        <th width="10%">ផ្សេងៗ</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $i = 0;
      foreach ($loanTerms as $key => $term) :
        $i += 1;
      ?>
        <tr>
          <td style="text-align: center;"><?= $model->service_charge_amount > 0 && $i == 1 ? "{$i}+{$model->service_charge}%" : $i ?></td>
          <td><?= $utils->maskDateKH($term->date, "d M Y") ?></td>
          <td style="text-align: right;">
            <?= $utils->DollarFormat($term->amount) ?>
          </td>
          <td style="text-align: right;">
            <?= $utils->DollarFormat($term->original_balance_amount) ?>
          </td>
          <td></td>
          <td></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>

</html>