<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$form = ActiveForm::begin([
  'id' => $model->formName(),
  'enableAjaxValidation' => false,
  'enableClientValidation' => true,
  'options' => ['enctype' => 'multipart/form-data'],
]); ?>

<?= $form->field($model, 'files[]')->fileInput(['multiple' => true]) ?>

<div class="d-flex flex-row-reverse gap-2">
  <?= Html::button('Cancel', ['class' => 'btn bg-gradient-secondary', 'id' => 'btn-dismiss-modal']) ?>
  <?= Html::submitButton('Save changes', ['class' => 'btn bg-gradient-primary']) ?>
</div>

<?php ActiveForm::end() ?>