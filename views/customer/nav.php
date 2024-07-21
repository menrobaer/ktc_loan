<?php

use yii\helpers\Url;

$action = Yii::$app->controller->action->id;
?>
<style>
  .nav-pills .nav-link.active,
  .nav-pills .show>.nav-link {
    color: var(--bs-nav-pills-link-active-color);
    background-color: var(--bs-nav-pills-link-active-bg);
  }
</style>
<div class="card shadow-lg mt-5">
  <div class="card-body p-3">
    <div class="row gx-4">
      <div class="col-auto">
        <div class="avatar avatar-xl position-relative">
          <img src="<?= $model->getAvatar() ?>" alt="profile_image" class="w-100 border-radius-lg shadow-sm">
        </div>
      </div>
      <div class="col-auto my-auto">
        <div class="h-100">
          <h5 class="mb-1">
            <?= $model->name ?>
          </h5>
          <p class="mb-0 font-weight-bold text-sm">
            <?= $model->phone_number ?>
          </p>
        </div>
      </div>
      <div class="col-lg-6 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mx-auto mt-3">
        <div class="nav-wrapper position-relative end-0">
          <ul class="nav nav-pills nav-fill p-1">
            <li class="nav-item">
              <a class="nav-link mb-0 px-0 py-1 <?= $action == 'view' ? 'active shadow' : '' ?>" href="<?= Url::toRoute(['view', 'id' => $model->id]) ?>">
                <i class="ni ni-badge text-sm me-2"></i> Profile
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link mb-0 px-0 py-1 <?= $action == 'attachment' ? 'active shadow' : '' ?>" href="<?= Url::toRoute(['attachment', 'id' => $model->id]) ?>">
                <i class="ni ni-album-2 text-sm me-2"></i> Attachment
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link mb-0 px-0 py-1 <?= $action == 'loan' ? 'active shadow' : '' ?>" href="<?= Url::toRoute(['loan', 'id' => $model->id]) ?>">
                <i class="ni ni-calendar-grid-58 text-sm me-2"></i> Loan
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>