<?php

use app\models\UserRole;
use app\widgets\ImageUploadWidget;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;

/** @var \app\components\Setup $setup */
$genders = Yii::$app->setup->getGenders();
$roles = ArrayHelper::map(UserRole::find()->all(), 'id', 'name');
?>

<div class="frm-user">

  <?php
  $validationUrl = ['user/validation'];
  if (!$model->isNewRecord) {
    $validationUrl['id'] = $model->id;
  }
  $form = ActiveForm::begin([
    'id' => 'frm-user',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'validationUrl' => $validationUrl,
    'options' => ['enctype' => 'multipart/form-data']
  ]);
  ?>
  <h6>Profile upload</h6>
  <?= $form->field($model, 'imageFile')->widget(ImageUploadWidget::class, ['imageUrl' => $model->isNewRecord || empty($model->getImagePath()) ? NULL : $model->getImagePath()])->label(false) ?>
  <div class="row">
    <div class="col-6">
      <?= $form->field($model, 'first_name')->textInput(['class' => 'form-control']) ?>
    </div>
    <div class="col-6">
      <?= $form->field($model, 'last_name')->textInput(['class' => 'form-control']) ?>
    </div>
  </div>
  <div class="row">
    <div class="col-6">
      <?= $form->field($profile, 'gender')->dropDownList($genders, ['class' => 'custom-select']) ?>
    </div>
    <div class="col-6">
      <?= $form->field($profile, 'phone')->textInput(['class' => 'form-control']) ?>
    </div>
  </div>
  <?= $form->field($profile, 'address')->textarea(['rows' => 3]) ?>
  <h6>User credential</h6>
  <div class="card py-0 mb-3">
    <div class="card-body">

      <div class="row">
        <div class="col-md-6">
          <?= $form->field($model, 'role_id')->dropDownList($roles, ['class' => 'custom-select']) ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <?= $form->field($model, 'username')->textInput(['class' => 'form-control', 'readonly' => !$model->isNewRecord]) ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($model, 'email')->textInput(['class' => 'form-control', 'readonly' => !$model->isNewRecord]) ?>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <?= $form->field($model, 'password')->textInput(['type' => 'password']) ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($model, 'confirm_password')->textInput(['type' => 'password']) ?>
        </div>
      </div>

    </div>
  </div>

  <div class="d-flex flex-row-reverse gap-2">
    <?= Html::button('Cancel', ['class' => 'btn bg-gradient-secondary', 'id' => 'btn-dismiss-modal']) ?>
    <?= Html::submitButton('Save changes', ['class' => 'btn bg-gradient-primary']) ?>
  </div>

  <?php ActiveForm::end(); ?>
</div>