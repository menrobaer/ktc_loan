<?php

use app\widgets\ImageUploadWidget;
use app\widgets\Modal;
use yii\bootstrap5\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$base_url = Yii::getAlias("@web");

$this->title = "Bussiness setup";
$this->params['pageTitle'] = $this->title;

$inlineTemplate = "<div class='row align-items-center'>
<div class='col-lg-4'>{label}</div>
<div class='col-lg-8'>{input}{error}{hint}</div>
</div>";

/** @var \app\components\Setup $setup */
$genders = Yii::$app->setup->getGenders();
$maritalStatus = Yii::$app->setup->getMaritalStatus();
?>
<?php
$validationUrl = ['company/validation'];
if (!$model->isNewRecord) {
  $validationUrl['id'] = $model->id;
}
$form = ActiveForm::begin([
  'id' => 'frm-company',
  'enableAjaxValidation' => true,
  'enableClientValidation' => true,
  'validationUrl' => $validationUrl,
  'options' => ['enctype' => 'multipart/form-data']
]);
?>
<div class="row">
  <div class="col-lg-3">
    <div class="card mb-3">
      <div class="card-body">
        <h6>Bussiness logo</h6>
        <?= $form->field($model, 'imageFile')->widget(ImageUploadWidget::class, ['imageUrl' => $model->isNewRecord || empty($model->getImagePath()) ? NULL : $model->getImagePath()])->label(false) ?>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h6>File banner</h6>
        <?= $form->field($model, 'headerFile')->widget(ImageUploadWidget::class, ['imageUrl' => $model->isNewRecord || empty($model->getHeaderPath()) ? NULL : $model->getHeaderPath()])->label(false) ?>
      </div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card">
      <div class="card-body">
        <?= $form->field($model, 'name')->textInput(['class' => 'form-control']) ?>
        <?= $form->field($model, 'phone')->textInput(['class' => 'form-control']) ?>
        <?= $form->field($model, 'email')->textInput(['class' => 'form-control']) ?>
        <?= $form->field($model, 'website')->textInput(['class' => 'form-control']) ?>
        <?= $form->field($model, 'bank_account')->textarea(['rows' => 3]) ?>
        <?= $form->field($model, 'address')->textarea(['rows' => 3]) ?>
        <?= $form->field($model, 'term_condition')->textarea(['rows' => 3]) ?>
        <div class="card mb-3">
          <div class="card-body">
            <div class="row">
              <div class="col-6">
                <?= $form->field($model, 'interest_rate')->textInput(['type' => 'number', 'step' => '0.01', 'class' => 'form-control']) ?>
              </div>
              <div class="col-6">
                <?= $form->field($model, 'service_charge')->textInput(['type' => 'number', 'step' => '0.01', 'class' => 'form-control']) ?>
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex flex-row-reverse gap-2">
          <?= Html::submitButton('Save changes', ['class' => 'btn bg-gradient-primary']) ?>
        </div>
      </div>
    </div>

  </div>
</div>
<?php ActiveForm::end(); ?>