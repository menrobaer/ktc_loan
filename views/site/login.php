<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">


    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
    ]); ?>

    <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => 'Username', 'class' => 'form-control form-control-lg']) ?>

    <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Password', 'class' => 'form-control form-control-lg']) ?>

    <?= $form->field($model, 'rememberMe')->checkbox([
        'template' => "<div class=\"form-check form-switch\">{input} {label}</div>\n{error}\n{hint}",
        'labelOptions' => ['class' => 'form-check-label'],
        'class' => 'form-check-input'
    ]) ?>

    <div class="form-group">
        <div>
            <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>