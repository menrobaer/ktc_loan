<?php

namespace app\widgets;

use yii\base\Widget;
use yii\helpers\Html;

class Modal extends Widget
{
  public $id;
  public $title;
  public $footer;

  public function init()
  {
    parent::init();
    if ($this->id === null) {
      $this->id = 'modal';
    }
  }

  public function run()
  {
    $script = <<<JS
      $('body').on('click', "[data-bs-toggle='modal']", function() {
          var target = $(this).data('bs-target');
          var url = $(this).data('url');
          var title = $(this).data('title');
          if (target === '#{$this->id}') {
              $('#{$this->id}-content').load(url);
              $('#{$this->id}Label').text(title);
          }
      });

      var formChanged = false;
      $('#{$this->id}').on("shown.bs.modal", function (e) {
        $(".modal form :input, .modal form select").change(function () {
          formChanged = true;
        });
      });

      $('#{$this->id}').on("hidden.bs.modal", function (e) {
        formChanged = false;
        $("#modal-customer-content").html("");
      });

      $('body').on("click", "#btn-dismiss-modal", function () {
        if (formChanged) {
          Swal.fire({
              title: 'Are you sure?',
              text: "You have unsaved changes. Do you really want to close?",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Yes, close it!',
              cancelButtonText: 'No, keep it'
          }).then((result) => {
              if (result.isConfirmed) {
                $('#{$this->id}').modal("hide");
              }
          });
        } else {
          $('#{$this->id}').modal("hide");
        }
      });
      
    JS;
    $this->getView()->registerJs($script, \yii\web\View::POS_END);
    return $this->renderModal();
  }

  protected function renderModal()
  {
    $modal = Html::beginTag('div', ['class' => 'modal fade', 'id' => $this->id, 'tabindex' => '-1', 'aria-labelledby' => $this->id . 'Label', 'aria-hidden' => 'true', 'data-bs-backdrop' => 'static', 'data-bs-keyboard' => 'false']);
    $modal .= Html::beginTag('div', ['class' => 'modal-dialog']);
    $modal .= Html::beginTag('div', ['class' => 'modal-content']);

    // Modal Header
    $modal .= Html::beginTag('div', ['class' => 'modal-header']);
    $modal .= Html::tag('h5', $this->title, ['class' => 'modal-title', 'id' => $this->id . 'Label']);
    $modal .= Html::endTag('div');

    // Modal Body
    $modal .= Html::beginTag('div', ['class' => 'modal-body']);
    $modal .= Html::tag('div', '', ['id' => $this->id . '-content']);
    $modal .= Html::endTag('div');

    // Modal Footer
    if ($this->footer !== null) {
      $modal .= Html::beginTag('div', ['class' => 'modal-footer']);
      $modal .= $this->footer;
      $modal .= Html::endTag('div');
    }

    $modal .= Html::endTag('div');
    $modal .= Html::endTag('div');
    $modal .= Html::endTag('div');

    return $modal;
  }
}
