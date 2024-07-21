<?php

use app\widgets\Modal;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$base_url = Yii::getAlias("@web");

$this->title = 'Officer';
$this->params['breadcrumbs'][] = ['url' => ['index'], 'class' => 'text-white', 'label' => $this->title];
$this->params['breadcrumbs'][] = "Lists";
$this->params['pageTitle'] = $this->title;
?>
<div class="<?= Yii::$app->controller->action->id; ?>">
  <?= Modal::widget([
    'id' => 'modal-officer',
  ]) ?>

  <div class="card">
    <div class="card-body">

      <?= $this->render('_search', ['model' => $searchModel]); ?>
      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => [
          'class' => 'table table-hover',
          'id' => 'table-officer',
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
            'attribute' => 'name',
            'label' => 'Name',
            'format' => 'raw',
            'value' => function ($model) {
              return $model->name == '' ? "<span class='text-muted'>not set</span>" : $model->name;
            }
          ],
          [
            'attribute' => 'phone_number',
            'label' => 'Phone Number',
            'format' => 'raw',
            'value' => function ($model) {
              return  $model->phone_number;
            }
          ],
          [
            'attribute' => 'address',
            'label' => 'Address',
            'format' => 'raw',
            'value' => function ($model) {
              return  $model->address;
            }
          ],
          [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Actions',
            'headerOptions' => ['class' => 'text-center'],
            'contentOptions' => ['class' => 'text-center'],
            'template' => '{update} {delete}',
            'buttons' => [
              'update' => function ($url, $model) {
                return Html::button('<i class="fas fa-edit"></i>', [
                  'class' => 'btn btn-sm btn-icon rounded-circle bg-gradient-default',
                  'title' => 'Update this item',
                  'data' => [
                    'bs-toggle' => 'modal',
                    'bs-target' => '#modal-officer',
                    'url' => \yii\helpers\Url::to(['officer/update', 'id' => $model->id]),
                    'toggle' => 'tooltip',
                    'title' => "Update Officer: " . $model->name
                  ],
                ]);
              },
              'delete' => function ($url, $model) {
                $disabled = $model->isUsed ? 'disabled' : 'button-delete';
                return Html::a('<i class="fas fa-trash"></i>', '#', [
                  'class' => "btn btn-sm btn-icon rounded-circle bg-gradient-default {$disabled}",
                  'title' => 'Delete this item',
                  'data' => [
                    'url' => Url::toRoute(['officer/delete', 'id' => $model->id]),
                    'confirm' => 'You won\'t be able to revert this!',
                    'method' => 'post',
                    'toggle' => 'tooltip',
                  ]
                ]);
              }
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