<?php

use app\widgets\Modal;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$base_url = Yii::getAlias("@web");

/** @var \app\components\Utils $utils */
$utils = Yii::$app->utils;

$this->title = 'Loan';
$this->params['breadcrumbs'][] = ['url' => ['index'], 'class' => 'text-white', 'label' => $this->title];
$this->params['breadcrumbs'][] = "Lists";
$this->params['pageTitle'] = $this->title;
?>
<div class="<?= Yii::$app->controller->action->id; ?>">

  <div class="card">
    <div class="card-body">

      <?= $this->render('_search', ['model' => $searchModel]); ?>
      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions'   => function ($model, $key, $index, $grid) {
          return ['data-id' => $model->generate_code, 'class' => "cs-pointer", 'role' => 'button'];
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
            'attribute' => 'original_amount',
            'label' => 'Loan amount',
            'format' => 'raw',
            'headerOptions' => ['class' => 'text-end', 'style' => 'min-width: 100px;'],
            'contentOptions' => ['class' => 'text-end'],
            'value' => function ($model) {
              $amount = number_format($model->original_amount, 2);
              return " <div class='d-flex justify-content-between'>
                      <span>$</span>
                      <span>{$amount}</span>
                    </div>";
            }
          ],
          [
            'attribute' => 'grand_total',
            'label' => 'Value',
            'format' => 'raw',
            'headerOptions' => ['class' => 'text-end', 'style' => 'min-width: 100px;'],
            'contentOptions' => ['class' => 'text-end'],
            'value' => function ($model) {
              $amount = number_format($model->grand_total, 2);
              return " <div class='d-flex justify-content-between'>
                      <span>$</span>
                      <span>{$amount}</span>
                    </div>";
            }
          ],
          [
            'attribute' => 'balance_amount',
            'label' => 'Balance',
            'format' => 'raw',
            'headerOptions' => ['class' => 'text-end', 'style' => 'min-width: 100px;'],
            'contentOptions' => ['class' => 'text-end'],
            'value' => function ($model) {
              $amount = number_format($model->balance_amount, 2);
              return " <div class='d-flex justify-content-between'>
                      <span>$</span>
                      <span>{$amount}</span>
                    </div>";
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
            'template' => '{update} {void}',
            'buttons' => [
              'update' => function ($url, $model) {
                $disabled = $model->paid_amount > 0 ? 'disabled' : '';
                return Html::a('<i class="fas fa-edit"></i>', $url, [
                  'class' => 'btn btn-sm btn-icon rounded-circle bg-gradient-default ' . $disabled,
                  'title' => 'Update this item',
                  'data' => [
                    'title' => "Update loan"
                  ],
                ]);
              },
              'void' => function ($url, $model) {
                return Html::a('<i class="fas fa-times"></i>', '#', [
                  'class' => "btn btn-sm btn-icon rounded-circle bg-gradient-default button-action-swal",
                  'title' => 'Void this loan?',
                  'data' => [
                    'url' => Url::toRoute(['loan/void', 'id' => $model->id]),
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