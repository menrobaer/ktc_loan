<?php

use app\widgets\Modal;
use yii\bootstrap5\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$base_url = Yii::getAlias("@web");

$this->title = "View Customer: $model->name";
$this->params['breadcrumbs'][] = ['url' => ['index'], 'class' => 'text-white', 'label' => "Customer Lists"];
$this->params['breadcrumbs'][] = $model->name;
$this->params['pageTitle'] = $this->title;

$inlineTemplate = "<div class='row align-items-center'>
<div class='col-lg-4'>{label}</div>
<div class='col-lg-8'>{input}{error}{hint}</div>
</div>";

/** @var \app\components\Setup $setup */
$genders = Yii::$app->setup->getGenders();
$maritalStatus = Yii::$app->setup->getMaritalStatus();

?>
<?= $this->render('nav', ['model' => $model]) ?>
<style>
  form div.required label:after {
    content: "";
    display: none;
  }
</style>
<div class="card mt-4">
  <div class="card-body">
    <a class="btn bg-gradient-default" href="<?= Url::to(['index']) ?>"><i class="fas fa-long-arrow-alt-left pe-2"></i>Back to list</a>

    <div class="row">
      <div class="col-lg-5">
        <?= $this->render('_form', ['model' => $model]) ?>
      </div>
      <div class="offset-lg-1 col-lg-5">
        <?php
        $form = ActiveForm::begin([
          'id' => 'frm-loan',
          'enableAjaxValidation' => false,
          'enableClientValidation' => true,
        ]);
        ?>
        <div class="card">
          <div class="card-header p-3">
            <h5 class="mb-0">Profile Details In Khmer</h5>
          </div>
          <div class="card-body p-3">
            <div class="nav-wrapper">
              <ul class="nav nav-pills nav-fill p-1" role="tablist">
                <li class="nav-item" role="presentation">
                  <a class="nav-link mb-0 px-0 py-1 active" data-bs-toggle="tab" href="#profile-tabs" role="tab" aria-controls="preview" aria-selected="true">
                    <i class="ni ni-badge text-sm me-2"></i> Customer
                  </a>
                </li>
                <li class="nav-item" role="presentation">
                  <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#guarantor-tabs" role="tab" aria-controls="code" aria-selected="false">
                    <i class="ni ni-single-02 text-sm me-2"></i> Guarantor
                  </a>
                </li>
                <li class="nav-item" role="presentation">
                  <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#company-tabs" role="tab" aria-controls="code" aria-selected="false">
                    <i class="ni ni-building text-sm me-2"></i> Company
                  </a>
                </li>
              </ul>
              <div class="p-3 shadow border-radius-md">
                <div class="tab-content" id="myTabContent">
                  <h6 class="text-danger"><i class="fas fa-exclamation-circle pe-1"></i>All fields are required</h6>
                  <hr class="border-1">
                  <div class="tab-pane fade show active" id="profile-tabs" role="tabpanel" aria-labelledby="profile-tab">
                    <?= $form->field($customerDetail, 'name', ['template' => $inlineTemplate])->textInput() ?>
                    <?= $form->field($customerDetail, 'gender', ['template' => $inlineTemplate])->dropDownList($genders, ['class' => 'custom-select']) ?>
                    <?= $form->field($customerDetail, 'phone', ['template' => $inlineTemplate])->textInput() ?>
                    <?= $form->field($customerDetail, 'nationality', ['template' => $inlineTemplate])->textInput() ?>
                    <?= $form->field($customerDetail, 'date_of_birth', ['template' => $inlineTemplate])->textInput(['class' => 'form-control datepicker flatpickr-input']) ?>
                    <?= $form->field($customerDetail, 'identity_number', ['template' => $inlineTemplate])->textInput() ?>
                    <?= $form->field($customerDetail, 'marital_status', ['template' => $inlineTemplate])->dropDownList($maritalStatus, ['class' => 'custom-select']) ?>
                    <?= $form->field($customerDetail, 'current_address', ['template' => $inlineTemplate])->textarea(['rows' => 3]) ?>
                  </div>
                  <div class="tab-pane fade" id="company-tabs" role="tabpanel" aria-labelledby="company-tab">
                    <?= $form->field($customerDetail, 'company_name', ['template' => $inlineTemplate])->textInput() ?>
                    <?= $form->field($customerDetail, 'company_phone', ['template' => $inlineTemplate])->textInput() ?>
                    <?= $form->field($customerDetail, 'position', ['template' => $inlineTemplate])->textInput() ?>
                    <?= $form->field($customerDetail, 'income', ['template' => $inlineTemplate])->textInput(['type' => 'number', 'step' => 0.01]) ?>
                    <?= $form->field($customerDetail, 'payroll_day', ['template' => $inlineTemplate])->textInput(['class' => 'form-control datepicker flatpickr-input']) ?>
                    <?= $form->field($customerDetail, 'company_address', ['template' => $inlineTemplate])->textarea(['rows' => 3]) ?>
                  </div>
                  <div class="tab-pane fade" id="guarantor-tabs" role="tabpanel" aria-labelledby="guarantor-tab">
                    <?= $form->field($customerDetail, 'guarantor_name', ['template' => $inlineTemplate])->textInput() ?>
                    <?= $form->field($customerDetail, 'guarantor_gender', ['template' => $inlineTemplate])->dropDownList($genders, ['class' => 'custom-select']) ?>
                    <?= $form->field($customerDetail, 'guarantor_phone', ['template' => $inlineTemplate])->textInput() ?>
                    <?= $form->field($customerDetail, 'guarantor_relationship', ['template' => $inlineTemplate])->textInput()->label("Relationship to customer") ?>
                    <?= $form->field($customerDetail, 'guarantor_nationality', ['template' => $inlineTemplate])->textInput() ?>
                    <?= $form->field($customerDetail, 'guarantor_identity_number', ['template' => $inlineTemplate])->textInput() ?>
                    <?= $form->field($customerDetail, 'guarantor_date_of_birth', ['template' => $inlineTemplate])->textInput(['class' => 'form-control datepicker flatpickr-input']) ?>
                    <?= $form->field($customerDetail, 'guarantor_current_address', ['template' => $inlineTemplate])->textarea(['rows' => 3]) ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer pb-0">
            <div class="d-flex flex-row-reverse">
              <?= Html::submitButton('Save changes', ['class' => 'btn bg-gradient-primary']) ?>
            </div>
          </div>
        </div>
        <?php ActiveForm::end(); ?>
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

  $("#customerdetail-date_of_birth, #customerdetail-guarantor_date_of_birth").flatpickr({
    altInput: true,
    altFormat: 'F j, Y',
    dateFormat: 'Y-m-d',
    defaultDate: "1990-01-01", 
  });

  $("#customerdetail-payroll_day").flatpickr({
    altInput: true,
    altFormat: 'J',
    dateFormat: 'd',
    onOpen: function(selectedDates, dateStr, instance) {
      const monthElements = instance.calendarContainer.querySelectorAll('.flatpickr-month');
      monthElements.forEach(element => {
        element.style.display = 'none';
      });

      const weekDays = instance.calendarContainer.querySelector('.flatpickr-weekdays');
      if (weekDays) {
        weekDays.style.display = 'none';
      }

      const prevArrow = instance.calendarContainer.querySelector('.flatpickr-prev-month');
      if (prevArrow) {
        prevArrow.style.display = 'none';
      }

      const nextArrow = instance.calendarContainer.querySelector('.flatpickr-next-month');
      if (nextArrow) {
        nextArrow.style.display = 'none';
      }
    }
  });

JS;
$this->registerJs($script);
?>