<?php
$this->title = "View Payment Receivable Report";
/** @var \app\components\Utils $utils */
$utils = Yii::$app->utils;

/** @var \app\components\Setup $setup */
$setup = Yii::$app->setup;
?>

<h2 style="text-align: center; margin-bottom:0px; font-family:khmersiemreap;">របាយការណ៍​ទទួលប្រាក់</h2>
<h4 style="text-align: center; font-family:khmersiemreap;"><?= $utils->maskDateKH($selectedDate, "D d M Y") ?></h4>

<?= $officerName != '' ? "<p style='font-family:khmersiemreap;'>មន្ត្រីឥណទាន: <strong>{$officerName}</strong></p>" : "" ?>

<table id="table-sheet" style="width: 100%; border-collapse:collapse;" border="1">
  <thead class="bg-default">
    <tr>
      <th style="text-align: center;" width="15%">ល.រ</th>
      <th style="text-align: left;" width="35%"><?= $selectedOfficer == '' ? 'ឈ្មោះមន្ត្រីឥណទាន' : 'ឈ្មោះអតិថិជន' ?></th>
      <th style="text-align: right;" width="25%">ទឹកប្រាក់</th>
      <th style="text-align: right;" width="25%">បានទូទាត់</th>
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
          <td style="text-align: center;"><?= $key ?></td>
          <td><?= $selectedOfficer == '' ?  $value->officer_name : $value->customer_name ?></td>
          <td style="text-align: right;">
            <?= $utils->DollarFormat($value->amount) ?>
          </td>
          <td style="text-align: right;">
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
      <th style="text-align: right;" colspan="2"><span class="me-3">ទឹកប្រាក់សរុប:</span></th>
      <th style="text-align: right;">
        <?= $utils->DollarFormat(array_sum(array_column($data, 'amount'))) ?>
      </th>
      <th style="text-align: right;">
        <?= $utils->DollarFormat(array_sum(array_column($data, 'paid'))) ?>
      </th>
    </tr>
  </tfoot>
</table>