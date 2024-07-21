<?php

use app\widgets\Modal;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$base_url = Yii::getAlias("@web");

$this->title = 'User';
$this->params['breadcrumbs'][] = ['url' => ['index'], 'class' => 'text-white', 'label' => $this->title];
$this->params['breadcrumbs'][] = "Lists";
$this->params['pageTitle'] = $this->title;
?>
<div class="<?= Yii::$app->controller->action->id; ?>">
  <?= Modal::widget([
    'id' => 'modal-user',
  ]) ?>

  <div class="card">
    <div class="card-body">

      <?= $this->render('_search', ['model' => $searchModel]); ?>
      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => [
          'class' => 'table table-hover',
          'id' => 'table-user',
          'cellspacing' => '0',
          'width' => '100%',
        ],
        'layout' => "
            <div class='table-responsive'>
                {items}
            </div>
            <hr>
            <div class='d-flex justify-content-between'>
              {summary}
              {pager}
            </div>
        ",
        'pager' => [
          'class' => 'yii\bootstrap5\LinkPager',
          'maxButtonCount' => 5,
        ],

        'columns' => [
          ['class' => 'yii\grid\SerialColumn'],
          [
            'label' => 'Role',
            'format' => 'raw',
            'value' => function ($model) {
              return !empty($model->role) ? $model->role->name : '';
            }
          ],
          [
            'attribute' => 'first_name',
            'label' => 'Full Name',
            'format' => 'raw',
            'value' => function ($model) {
              return $model->getFullName();
            }
          ],          [
            'label' => 'Phone Number',
            'format' => 'raw',
            'value' => function ($model) {
              return !empty($model->profile) ? $model->profile->phone : '';
            }
          ],

          [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Actions',
            'headerOptions' => ['class' => 'text-center'],
            'contentOptions' => ['class' => 'text-center'],
            'template' => '{update}',
            'buttons' => [
              'update' => function ($url, $model) {
                return Html::button('<i class="fas fa-edit"></i>', [
                  'class' => 'btn btn-sm btn-icon rounded-circle bg-gradient-default',
                  'title' => 'Update this item',
                  'data' => [
                    'bs-toggle' => 'modal',
                    'bs-target' => '#modal-user',
                    'url' => \yii\helpers\Url::to(['user/update', 'id' => $model->id]),
                    'toggle' => 'tooltip',
                    'title' => "Update User: " . $model->name
                  ],
                ]);
              },
            ],

          ],
        ],
      ]); ?>

    </div>
  </div>
</div>
<?php
$script = <<<JS

JS;
$this->registerJs($script);
?>