<?php

use app\assets\Select2Asset;
use app\models\Officer;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

Select2Asset::register($this);

$inputFloatLabel = '<div class="form-label-group">{input} {label} {error}{hint}</div>';
$officerArr = Officer::find()
  ->select(['officer.id', 'officer.name'])
  ->andWhere(['!=', 'officer.name', ''])
  ->orderBy(['officer.name' => SORT_ASC])
  ->all();
$officerArr = ArrayHelper::map($officerArr, 'id', 'name');
?>
<style>
  .field-officersearch-id {
    width: 250px !important;
  }

  /* Example: make the dropdown menu wider */
  .select2-container .select2-dropdown {
    min-width: 200px;
    /* Adjust as needed */
  }
</style>
<div class="formOfficerSearch">
  <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'options' => ['data-pjax' => true, 'id' => 'formOfficerSearch'],
    'method' => 'get',
  ]); ?>

  <div class="d-flex align-items-end">
    <?= Html::button("+ Create <span class='d-none d-lg-inline'>Officer</span>", [
      'class' => 'btn btn-warning',
      'data' => [
        'bs-toggle' => 'modal',
        'bs-target' => '#modal-officer',
        'title' => 'Create Officer',
        'url' => \yii\helpers\Url::to(['officer/create'])
      ]
    ]) ?>
    <div class="ms-3">
      <?= $form->field($model, 'id')->dropDownList($officerArr, ['prompt' => 'All'])->label('Officer Name');
      ?>
    </div>
  </div>


  <?php ActiveForm::end(); ?>

</div>
<?php
$script = <<<JS

    $(document).on("change","#officersearch-id", function(){
        $('#formOfficerSearch').trigger('submit');
    });

    $("#officersearch-id").select2();

JS;
$this->registerJs($script);

?>