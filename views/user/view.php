<?php

use app\widgets\Modal;
use yii\bootstrap5\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$base_url = Yii::getAlias("@web");

$this->title = "View Profile: $model->fullName";
$this->params['pageTitle'] = $this->title;

$inlineTemplate = "<div class='row align-items-center'>
<div class='col-lg-4'>{label}</div>
<div class='col-lg-8'>{input}{error}{hint}</div>
</div>";

/** @var \app\components\Setup $setup */
$genders = Yii::$app->setup->getGenders();
$maritalStatus = Yii::$app->setup->getMaritalStatus();

?>

<div class="card mt-4">
  <div class="card-body">
    <div class="row">
      <div class="col-lg-5">
        <?= $this->render('_form', ['model' => $model, 'profile' => $profile]) ?>
      </div>
    </div>
  </div>
</div>
<?php
$script = <<<JS

  $('.has-select2').select2({
    allowClear: true,
    // placeholder: $(this).attr('prompt'),
    placeholder: "Select an option",
  });

JS;
$this->registerJs($script);
?>