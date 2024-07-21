<?php

use app\widgets\ImageUploadWidget;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;

?>

<div class="frm-officer">

  <?php
  $validationUrl = ['officer/validation'];
  if (!$model->isNewRecord) {
    $validationUrl['id'] = $model->id;
  }
  $form = ActiveForm::begin([
    'id' => 'frm-officer',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'validationUrl' => $validationUrl,
    'options' => ['enctype' => 'multipart/form-data']
  ]);
  ?>
  <h6>Profile upload</h6>
  <?= $form->field($model, 'imageFile')->widget(ImageUploadWidget::class, ['imageUrl' => $model->isNewRecord || empty($model->getImagePath()) ? NULL : $model->getImagePath()])->label(false) ?>
  <?= $form->field($model, 'name')->textInput(['class' => 'form-control']) ?>
  <?= $form->field($model, 'phone_number')->textInput(['class' => 'form-control']) ?>
  <?= $form->field($model, 'address')->textArea(['rows' => 3]) ?>

  <div class="d-flex flex-row-reverse gap-2">
    <?= Html::button('Cancel', ['class' => 'btn bg-gradient-secondary', 'id' => 'btn-dismiss-modal']) ?>
    <?= Html::submitButton('Save changes', ['class' => 'btn bg-gradient-primary']) ?>
  </div>

  <?php ActiveForm::end(); ?>
</div>