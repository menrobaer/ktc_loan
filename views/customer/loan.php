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

<div class="card mt-4">
  <div class="card-body">
    <?= Html::a("+ Create <span class='d-none d-lg-inline'>Loan</span>", ['create'], [
      'class' => 'btn btn-warning',
    ]) ?>
    <?= GridView::widget([
      'dataProvider' => $dataProvider,
      'rowOptions' => function ($model, $key, $index, $grid) {
        return ['data-id' => $model->generate_code, 'class' => "cs-pointer"];
      },
      'tableOptions' => [
        'class' => 'table table-hover',
        'id' => 'table-loan',
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
          'attribute' => 'generate_code',
          'label' => 'Code',
          'format' => 'raw',
          'value' => function ($model) {
            return "#" . $model->generate_code;
          }
        ],
        [
          'attribute' => 'customer_id',
          'label' => 'Customer',
          'format' => 'raw',
          'value' => function ($model) {
            return $model->customer ? $model->customer->name : '';
          }
        ],
        [
          'attribute' => 'date',
          'label' => 'Date',
          'format' => 'raw',
          'value' => function ($model) use ($utils) {
            return $utils->date($model->date);
          }
        ],
        [
          'attribute' => 'total_amount',
          'label' => 'Value',
          'headerOptions' => ['style' => 'min-width: 100px;'],
          'value' => function ($model) use ($utils) {
            return $utils->DollarFormat($model->grand_total);
          }
        ],
        [
          'attribute' => 'balance_amount',
          'label' => 'Balance',
          'headerOptions' => ['style' => 'min-width: 100px;'],
          'value' => function ($model) use ($utils) {
            return $utils->DollarFormat($model->balance_amount);
          }
        ],
        [
          'attribute' => 'status',
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center', 'style' => 'min-width: 100px;'],
          'format' => 'raw',
          'value' => function ($model) {
            return $model->getStatusTemp();
          },
        ],

        [
          'class' => 'yii\grid\ActionColumn',
          'header' => 'Actions',
          'headerOptions' => ['class' => 'text-center'],
          'contentOptions' => ['class' => 'text-center'],
          'template' => '{update} {delete}',
          'buttons' => [
            'update' => function ($url, $model) {
              return Html::a('<i class="fas fa-edit"></i>', $url, [
                'class' => 'btn btn-sm btn-icon rounded-circle bg-gradient-default',
                'title' => 'Update this item',
                'data' => [
                  'title' => "Update loan"
                ],
              ]);
            },
            'delete' => function ($url, $model) {
              $disabled = $model->isUsed ? 'button-delete' : 'button-delete';
              return Html::a('<i class="fas fa-trash"></i>', '#', [
                'class' => "btn btn-sm btn-icon rounded-circle bg-gradient-default {$disabled}",
                'title' => 'Delete this item',
                'data' => [
                  'url' => Url::toRoute(['customer/delete', 'id' => $model->id]),
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

<?php

$this->registerJs("

    $('#table-loan td').click(function (e) {
        var code = $(this).closest('tr').data('id');
        if(e.target == this)
            location.href = '" . Url::to(['loan/view']) . "?code=' + code;
    });

");
$script = <<<JS

JS;
$this->registerJs($script);
?>