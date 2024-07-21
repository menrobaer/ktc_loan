<?php

use app\widgets\Modal;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$base_url = Yii::getAlias("@web");
/** @var \app\components\Utils $utils */
$utils = Yii::$app->utils;

$this->title = "View Customer: $model->name";
$this->params['breadcrumbs'][] = ['url' => ['index'], 'class' => 'text-white', 'label' => "Customer Lists"];
$this->params['breadcrumbs'][] = $model->name;
$this->params['pageTitle'] = $this->title;
?>
<?= $this->render('nav', ['model' => $model]) ?>
<?= Modal::widget([
  'id' => 'modal-add-attachment',
]) ?>

<div class="card mt-4">
  <div class="card-body">
    <?= Html::button("+ Add <span class='d-none d-lg-inline'>Attachments</span>", [
      'class' => 'btn btn-warning',
      'data' => [
        'bs-toggle' => 'modal',
        'bs-target' => '#modal-add-attachment',
        'title' => 'Add Attachment',
        'url' => \yii\helpers\Url::to(['customer/add-attachment', 'customerID' => $model->id])
      ]
    ]) ?>

    <table class="table table-hover">
      <thead class="thead-light">
        <tr>
          <th width="50%">Name</th>
          <th>Added date</th>
          <th>File size</th>
          <th>Uploaded by</th>
          <th></th>
        </tr>
      </thead>
      <tbody class="text-sm">
        <?php
        if (!empty($attachments)) {
          foreach ($attachments as $key => $value) {
        ?>
            <input type="hidden" class="filePath" value="<?= $value->getFilePath() ?>" data-name="<?= $value->filename ?>">
            <tr>
              <td><span class="px-1"><i class='far fa-file'></i></span> <?= "{$value->filename}.{$value->extension}" ?></td>
              <td><?= $utils->dateTime($value->created_at) ?></td>
              <td><?= $value->formatSize() ?></td>
              <td><?= !empty($value->user) ? $value->user->name : '' ?></td>
              <td>
                <a href="<?= $value->getFilePath() ?>" download="<?= "{$value->filename}.{$value->extension}" ?>" class="btn btn-sm btn-icon rounded-circle bg-gradient-default" data-toggle="tooltip" data-title="Download this file"><i class="fas fa-download"></i></a>
                <?= Html::button('<i class="fas fa-trash"></i>', [
                  'class' => 'btn btn-sm btn-icon rounded-circle bg-gradient-default button-delete',
                  'title' => 'Delete this item',
                  'data' => [
                    'url' => Url::toRoute(['customer/delete-attachment', 'id' => $value->id]),
                    'confirm' => 'You won\'t be able to revert this!',
                    'method' => 'post',
                    'toggle' => 'tooltip',
                  ]
                ]); ?>
              </td>
            </tr>
        <?php }
        } ?>
        <?= empty($attachments) ? "<tr><td colspan='100%'>No results found.</td></tr>" : "" ?>
      </tbody>
    </table>
  </div>
</div>