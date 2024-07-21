<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

$this->title = $model->isNewRecord ? 'New Role' : 'Update Role';
$this->params['pageTitle'] = $this->title;
?>

<div class="<?= $model->formName(); ?>">

  <?php $form = ActiveForm::begin(); ?>

  <div class="card">
    <div class="card-body">
      <a class="btn bg-gradient-default" href="<?= Url::to(['role']) ?>"><i class="fas fa-long-arrow-alt-left pe-2"></i>Back to list</a>
      <div class="row">
        <div class="col-md-6">
          <div class="card-title"><?= Yii::t('app', 'Set role name') ?></div>
          <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
          <div class="card-title"><?= Yii::t('app', 'Choose what this role can access') ?></div>

          <table class="table table-condensed">
            <thead class="thead-light">
              <tr>
                <th>Feature</th>
                <th>Capabilities</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (!empty($userRoleActionByGroup)) {
                foreach ($userRoleActionByGroup as $key => $value) {
              ?>
                  <tr>
                    <td><?= $key ?></td>
                    <td>
                      <?php
                      foreach ($value as $k => $v) {
                        $unique_key = 'chkboxAction_' . $key . $k;
                        $checked = $v['checked'] == 1 ? 'checked' : '';
                        $currentVal = '';
                        if ($v['checked'] == 1) {
                          $currentVal = $v['id'];
                        }
                        echo "<div class='form-check'>
                                <input {$checked} type='checkbox' class='form-check-input chkboxAction' data-val='{$v['id']}' id='{$unique_key}'>
                                <input type='hidden' data-id='{$unique_key}' name='chkboxAction[]' value='{$currentVal}' />
                                <label class='custom-control-label' for='{$unique_key}'>{$v['name']}</label>
                              </div>";
                      }
                      ?>
                    </td>
                  </tr>
              <?php
                }
              }
              ?>
            </tbody>
          </table>


          <?= Html::submitButton('<i class="far fa-save"></i> Save', ['class' => 'btn btn-success px-5 rounded-pill']) ?>
          <?php ActiveForm::end(); ?>
        </div>
      </div>
    </div>

  </div>

</div>

<?php

$script = <<<JS

  $(".chkboxAction").click(function(){
    var id = $(this).attr("id");
    var val = $(this).data("val");
    if($(this).is(":checked")){
      $("input[name='chkboxAction[]'][data-id='"+id+"']").val(val);
    }else{
      $("input[name='chkboxAction[]'][data-id='"+id+"']").val('');
    }
  });

JS;

$this->registerJs($script);
?>