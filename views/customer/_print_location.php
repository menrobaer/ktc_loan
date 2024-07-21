<?php

$base_url =  Yii::getAlias('@web/product');
$formater = Yii::$app->formater;

$this->title = "Invoice";
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style type="text/css">
    :root {
      /* --pageWidth: 4in;
      --pageHeight: 4.1in; */
      --pageWidth: 80mm;
      --pageHeight: 100mm;
    }

    @import url('https://fonts.googleapis.com/css2?family=Bokor&family=PT+Sans:ital,wght@0,400;0,700;1,700&display=swap');

    body {
      background: rgb(204, 204, 204);
      font-weight: 500;
      font-family: 'PT Sans', sans-serif;
    }

    page {
      background: white;
      display: block;
      margin: 0 auto;
      margin-bottom: 0.5cm;
      box-shadow: 0 0 0.5cm rgba(0, 0, 0, 0.5);
      font-size: 8pt;
      width: var(--pageWidth);
      height: var(--pageHeight);
      position: relative;
    }

    .kh-font {
      font-family: 'Bokor', sans-serif;
    }

    table {
      border-collapse: collapse;
      width: 100%;
    }

    table.border,
    table.border th,
    table.border td {
      border: 2px solid;
    }

    .table-address {
      font-size: 14pt;
    }

    .text-center {
      text-align: center;
    }

    .text-right {
      text-align: right;
    }

    .font-weight-bold {
      font-weight: bold;
    }

    @media print {

      body,
      page {
        color: #000 !important;
        margin: 0;
        box-shadow: 0;
        width: var(--pageWidth);
        height: var(--pageHeight);
      }

      @page {
        margin: 0;
        size: 4.1in 8cm;
      }
    }

    @page {
      width: var(--pageWidth);
      height: var(--pageHeight);
      margin: 0;
      padding: 0;
    }
  </style>
</head>

<body>

  <page id="custom">
    <h1 style="text-align:center; font-style:italic; font-weight:600 !important;">KANO TECH STORE</h1>
    <table class="table-address border" style="text-align: center;">
      <tr>
        <td class="kh-font">អ្នកផ្ញើ៖</td>
        <td class="text-center font-weight-bold">012 456 556</td>
      </tr>
      <tr>
        <td class="kh-font">អ្នកទទួល៖</td>
        <td class="kh-font text-center"><?= $model->address ?></td>
      </tr>
      <tr>
        <td class="kh-font">លេខទូរស័ព្ទ៖</td>
        <td class="text-center font-weight-bold"><?= $model->phone_number ?></td>
      </tr>
    </table>
    <h1 class="kh-font" style="text-align:center; margin:5px auto;">ប្រយ័ត្នបែកបាក់ ឬសើម</h1>
    <table class="kh-font" style="font-size: 12pt;">
      <tr>
        <td class="text-center">ថ្លៃសេវា</td>
        <td class="text-center">អ្នកផ្ញើ ▢</td>
        <td class="text-center">អ្នកទទួល ▢</td>
      </tr>
    </table>
    <table class="border" style="font-size: 14pt; margin:5px auto;">
      <tr>
        <td class="text-center">J&T ▢ | VET ▢ | Khmer Express ▢</td>
      </tr>
    </table>
  </page>
</body>

</html>
<script type="text/javascript">
  // window.onload = function() {
  //   window.print();
  // }
  window.print();
  setTimeout(function() {
    window.close();
  }, 500);
</script>