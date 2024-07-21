<?php
/* @var $this yii\web\View */

/** @var \app\components\Utils $utils */
$utils = Yii::$app->utils;
?>

<table style="width: 100%; border-collapse:collapse;">
  <tr>
    <td style="text-align: center; font-family: Khmer OS Muol; font-size:12pt;">
      ព្រះរាជាណាចក្រកម្ពុជា
    </td>
  </tr>
  <tr>
    <td style="text-align: center; font-family: Khmer OS Muol; font-size:12pt;">
      ជាតិ សាសនា ព្រះមហាក្សត្រ
    </td>
  </tr>
  <tr>
    <td style="text-align: center; font-family: Khmer OS Muol; font-size:12pt;">
      –––––––––––––––––––––––––––––––––––––––
    </td>
  </tr>
  <tr>
    <td style="text-align: center; font-family: Khmer OS Muol; font-size:11pt;">
      ពាក្យស្នើសុំបង់រំលស់
    </td>
  </tr>
</table>
<br />
<table style="width: 100%; border-collapse:collapse;">
  <tr>
    <td colspan="4" style="font-family: Khmer OS Muol; font-size:11pt;">១. ព័ត៌មានអ្នកបង់រំលស់</td>
  </tr>
  <tr>
    <td width="25%" style="font-family: Khmer OS Siemreap; font-size:11pt;">ឈ្មោះ <strong><?= $profile->name ?></strong></td>
    <td width="15%" style="font-family: Khmer OS Siemreap; font-size:11pt;">ភេទ <strong><?= $profile->getGenderInKH($profile->gender) ?></strong></td>
    <td width="20%" style="font-family: Khmer OS Siemreap; font-size:11pt;">សញ្ជាតិ <strong><?= $profile->nationality ?></strong></td>
    <td width="40%" style="font-family: Khmer OS Siemreap; font-size:11pt;">អត្តសញ្ញាណប័ណ្ណលេខ <strong><?= $profile->identity_number ?></strong></td>
  </tr>
</table>
<table style="width: 100%; border-collapse:collapse;">
  <tr>
    <td width="33%" style="font-family: Khmer OS Siemreap; font-size:11pt;">ថ្ងៃខែឆ្នាំកំណើត <strong><?= $utils->maskDateKH($profile->date_of_birth, "d M Y") ?></strong></td>
    <td width="27%" style="font-family: Khmer OS Siemreap; font-size:11pt;">ស្ថានភាពគ្រួសារ <strong><?= $profile->getMaritalStatusInKH($profile->marital_status) ?></strong></td>
    <td width="40%" style="font-family: Khmer OS Siemreap; font-size:11pt;">លេខទូរស័ព្ទ <strong><?= $profile->phone ?></strong></td>
  </tr>
</table>
<table style="width: 100%; border-collapse:collapse;">
  <tr>
    <td width="100%" style="font-family: Khmer OS Siemreap; font-size:11pt;">អាសយដ្ឋានបច្ចុប្បន្ន <strong><?= $profile->current_address ?></strong></td>
  </tr>
</table>
<br />
<table style="width: 100%; border-collapse:collapse;">
  <tr>
    <td colspan="3" style="font-family: Khmer OS Muol; font-size:11pt;">២. ព័ត៌មានអាជីវកម្ម</td>
  </tr>
  <tr>
    <td width="50%" style="font-family: Khmer OS Siemreap; font-size:11pt;">ឈ្មោះ <strong><?= $profile->company_name ?></strong></td>
    <td width="50%" style="font-family: Khmer OS Siemreap; font-size:11pt;">លេខទូរស័ព្ទ <strong><?= $profile->company_phone ?></strong></td>
  </tr>
</table>
<table style="width: 100%; border-collapse:collapse;">
  <tr>
    <td width="100%" style="font-family: Khmer OS Siemreap; font-size:11pt;">អាសយដ្ឋាន <strong><?= $profile->company_address ?></strong></td>
  </tr>
</table>
<table style="width: 100%; border-collapse:collapse;">
  <tr>
    <td width="33%" style="font-family: Khmer OS Siemreap; font-size:11pt;">មុខតំណែង <strong><?= $profile->position ?></strong></td>
    <td width="34%" style="font-family: Khmer OS Siemreap; font-size:11pt;">ប្រាក់ចំណូលប្រចាំខែ <strong><?= $profile->income ?> USD</strong></td>
    <td width="33%" style="font-family: Khmer OS Siemreap; font-size:11pt;">កាលបរិច្ឆេទបើកប្រាក់បៀវត្ស <strong><?= $profile->payroll_day ?></strong></td>
  </tr>
</table>
<br />
<table style="width: 100%; border-collapse:collapse;">
  <tr>
    <td colspan="4" style="font-family: Khmer OS Muol; font-size:11pt;">៣. ព័ត៌មានអ្នករួមខ្ចី</td>
  </tr>
  <tr>
    <td width="25%" style="font-family: Khmer OS Siemreap; font-size:11pt;">ឈ្មោះ <strong><?= $profile->guarantor_name ?></strong></td>
    <td width="15%" style="font-family: Khmer OS Siemreap; font-size:11pt;">ភេទ <strong><?= $profile->getGenderInKH($profile->guarantor_gender) ?></strong></td>
    <td width="20%" style="font-family: Khmer OS Siemreap; font-size:11pt;">សញ្ជាតិ <strong><?= $profile->guarantor_nationality ?></strong></td>
    <td width="40%" style="font-family: Khmer OS Siemreap; font-size:11pt;">អត្តសញ្ញាណប័ណ្ណលេខ <strong><?= $profile->guarantor_identity_number ?></strong></td>
  </tr>
</table>
<table style="width: 100%; border-collapse:collapse;">
  <tr>
    <td width="33%" style="font-family: Khmer OS Siemreap; font-size:11pt;">ថ្ងៃខែឆ្នាំកំណើត <strong><?= $utils->maskDateKH($profile->guarantor_date_of_birth, "d M Y") ?></strong></td>
    <td width="27%" style="font-family: Khmer OS Siemreap; font-size:11pt;">លេខទូរស័ព្ទ <strong><?= $profile->guarantor_phone ?></strong></td>
    <td width="40%" style="font-family: Khmer OS Siemreap; font-size:11pt;">ត្រូវជា <strong><?= $profile->guarantor_relationship ?></strong> អ្នកខ្ចីប្រាក់។</td>
  </tr>
</table>
<table style="width: 100%; border-collapse:collapse;">
  <tr>
    <td width="100%" style="font-family: Khmer OS Siemreap; font-size:11pt;">អាសយដ្ឋានបច្ចុប្បន្ន <strong><?= $profile->guarantor_current_address ?></strong></td>
  </tr>
</table>
<br />
<table style="width: 100%; border-collapse:collapse;">
  <tr>
    <td style="text-align: center; font-family: Khmer OS Muol; font-size:11pt;">
      ព័ត៌មានផលិតផលបង់រំលស់ និងប្រាក់ត្រូវបង់
    </td>
  </tr>
</table>
<table style="width: 100%; border-collapse:collapse;">
  <tr>
    <td width="5%">•</td>
    <td width="50%" style="font-family: Khmer OS Siemreap; font-size:11pt;">ផលិតផល <strong><?= $model->item_name ?></strong></td>
    <td width="25%" style="font-family: Khmer OS Siemreap; font-size:11pt;">តម្លៃចំនួន <strong><?= $model->loan_amount - $model->deposit_amount ?> USD</strong></td>
    <td width="25%" style="font-family: Khmer OS Siemreap; font-size:11pt;">រយៈពេលខ្ចី <strong> <?= $model->payment_term_id ?> </strong>ខែ</td>
  </tr>
</table>
<table style="width: 100%; border-collapse:collapse;">
  <tr>
    <td width="5%">•</td>
    <td width="95%" style="font-family: Khmer OS Siemreap; font-size:11pt;">អត្រាការប្រាក់ <strong><?= $model->interest_rate ?></strong>%ក្នុងមួយខែ។ ប្រភេទកម្ចី៖ &#x25a2; បង់ដំបូង តម្លៃសេវា <strong><?= $model->service_charge ?></strong>% ៖ <strong><?= $model->service_charge_amount ?> USD</strong></td>
  </tr>
</table>
<table style="width: 100%; border-collapse:collapse;">
  <tr>
    <td width="5%">•</td>
    <td width="35%" style="font-family: Khmer OS Siemreap; font-size:11pt;">ទឹកប្រាក់សរុបត្រូវបង់ <strong><?= $model->grand_total ?> USD</strong></td>
    <td width="30%" style="font-family: Khmer OS Siemreap; font-size:11pt;">ការប្រាក់សរុប <strong><?= $model->interest_rate_amount ?> USD</strong></td>
    <td width="30%" style="font-family: Khmer OS Siemreap; font-size:11pt;">ប្រាក់ត្រូវបង់ក្នុងមួយខែៗ <strong> <?= $model->installment_amount ?> USD </strong></td>
  </tr>
</table>
<table style="width: 100%; border-collapse:collapse;">
  <tr>
    <td width="5%">•</td>
    <td width="45%" style="font-family: Khmer OS Siemreap; font-size:11pt;">កាលបរិច្ឆេទបង់ដំបូង <strong><?= $utils->maskDateKH($model->getFirstPayDate(), "d M Y") ?></strong></td>
    <td width="45%" style="font-family: Khmer OS Siemreap; font-size:11pt;">កាលបរិច្ឆេទបង់ចុងក្រោយ <strong><?= $utils->maskDateKH($model->getLastPayDate(), "d M Y") ?></strong></td>
  </tr>
</table>
<br />
<table style="width: 100%; border:none; border-collapse:collapse;">
  <tr>
    <td width="50%" style="text-align: center; font-family: Khmer OS Muol; font-size:11pt;">
      ស្នាមមេដៃអ្នករួមបង់រំលស់
    </td>
    <td width="50%" style="text-align: center; font-family: Khmer OS Muol; font-size:11pt;">
      ស្នាមមេដៃអ្នកសុំបង់រំលស់
    </td>
  </tr>
  <tr>
    <td width="50%" style="text-align: center; font-family: Khmer OS Siemreap; font-size:11pt;">
      <br />
      <br />
      <br />
      ឈ្មោះ <strong><?= $profile->guarantor_name ?></strong>
    </td>
    <td width="50%" style="text-align: center; font-family: Khmer OS Siemreap; font-size:11pt;">
      <br />
      <br />
      <br />
      ឈ្មោះ <strong><?= $profile->name ?></strong>
    </td>
  </tr>
</table>
<br />
<br />
<table style="width: 100%; border-collapse:collapse;">
  <tr>
    <td width="40%"></td>
    <td style="text-align: center; font-family: Khmer OS Siemreap; font-size:11pt;">
      <strong>យោបល់ និងការអនុម័តឱ្យបង់រំលស់</strong> ដោយពិនិត្យឃើញថា<br />
      អតិថិជនមានចរិតល្អ មុខរបរត្រឹមត្រូវ ដូចោ្នះអាចផ្តល់កម្ចីបាន។
    </td>
  </tr>
  <tr>
    <td width="40%"></td>
    <td style="text-align: center; font-family: Khmer OS Siemreap; font-size:11pt;">ថ្ងៃទី <strong><?= date_format(date_create($model->date), "d") ?></strong> ខែ <strong><?= $utils->maskMonthKH(date_format(date_create($model->date), "M")) ?></strong> ឆ្នាំ <strong><?= date_format(date_create($model->date), "Y") ?></strong></td>
  </tr>
  <tr>
    <td width="40%"></td>
    <td style="text-align: center; font-family: Khmer OS Muol; font-size:11pt;">ហត្ថលេខាអ្នកអនុម័ត</td>
  </tr>
</table>