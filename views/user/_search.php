<?php

use app\assets\Select2Asset;
use app\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

Select2Asset::register($this);

$inputFloatLabel = '<div class="form-label-group">{input} {label} {error}{hint}</div>';
$userArr = Yii::$app->setup->getOfficers('map');
?>
<style>
  .field-usersearch-id {
    width: 250px !important;
  }

  /* Example: make the dropdown menu wider */
  .select2-container .select2-dropdown {
    min-width: 200px;
    /* Adjust as needed */
  }
</style>
<div class="formUserSearch">
  <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'options' => ['data-pjax' => true, 'id' => 'formUserSearch'],
    'method' => 'get',
  ]); ?>

  <div class="d-flex align-items-end">
    <?= Html::button("+ Create <span class='d-none d-lg-inline'>User</span>", [
      'class' => 'btn btn-warning',
      'data' => [
        'bs-toggle' => 'modal',
        'bs-target' => '#modal-user',
        'title' => 'Create User',
        'url' => \yii\helpers\Url::to(['user/create'])
      ]
    ]) ?>
    <div class="ms-3">
      <?= $form->field($model, 'id')->dropDownList($userArr, ['prompt' => 'All'])->label('Employee name');
      ?>
    </div>
  </div>


  <?php ActiveForm::end(); ?>

</div>
<?php
$script = <<<JS

    $(document).on("change","#usersearch-id", function(){
        $('#formUserSearch').trigger('submit');
    });

    $("#usersearch-id").select2();

JS;
$this->registerJs($script);

?>