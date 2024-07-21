<?php

use app\widgets\Breadcrumbs;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl z-index-sticky " id="navbarBlur" data-scroll="false">
  <div class="container-fluid py-1 px-3">
    <nav aria-label="breadcrumb" class="d-none">
      <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm">
          <a class="text-white" href="javascript:;">
            <i class="ni ni-box-2"></i>
          </a>
        </li>
        <li class="breadcrumb-item text-sm text-white"><a class="opacity-5 text-white" href="javascript:;">Pages</a>
        </li>
        <li class="breadcrumb-item text-sm text-white active" aria-current="page">Default</li>
      </ol>
      <h6 class="font-weight-bolder mb-0 text-white">Default</h6>
    </nav>
    <?= Breadcrumbs::widget([
      'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ]) ?>
    <div class="sidenav-toggler sidenav-toggler-inner d-xl-block d-none ">
      <a href="javascript:;" class="nav-link p-0">
        <div class="sidenav-toggler-inner">
          <i class="sidenav-toggler-line bg-white"></i>
          <i class="sidenav-toggler-line bg-white"></i>
          <i class="sidenav-toggler-line bg-white"></i>
        </div>
      </a>
    </div>
    <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
      <div class="ms-md-auto pe-md-3 d-flex align-items-center">
        <!-- <div class="input-group">
          <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
          <input type="text" class="form-control" placeholder="Type here...">
        </div> -->
      </div>
      <ul class="navbar-nav  justify-content-end">
        <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
          <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
            <div class="sidenav-toggler-inner">
              <i class="sidenav-toggler-line bg-white"></i>
              <i class="sidenav-toggler-line bg-white"></i>
              <i class="sidenav-toggler-line bg-white"></i>
            </div>
          </a>
        </li>
        <li class="nav-item dropdown pe-2 d-flex align-items-center">
          <a href="javascript:;" class="nav-link text-white p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
            <?= Html::img(Yii::$app->user->identity->avatar, ['class' => 'avatar avatar-sm me-2 shadow bg-white', 'title' => 'User Profile']) ?>
            <span class="d-sm-inline d-none text-capitalize font-weight-bold"><?= Yii::$app->user->identity->fullName ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" style="top: .25rem !important;" aria-labelledby="dropdownMenuButton">
            <li class="mb-2">
              <a class="dropdown-item border-radius-md" href="<?= Url::toRoute(['user/view', 'id' => Yii::$app->user->identity->id]) ?>">
                <i class="fas fa-user pe-2"></i>My Profile
              </a>
            </li>
            <li class="mb-2">
              <a class="dropdown-item border-radius-md" href="<?= Url::toRoute(['company/index']) ?>">
                <i class="fas fa-suitcase pe-2"></i>My Company
              </a>
            </li>
            <li class="mb-2">
              <a class="dropdown-item border-radius-md" href="<?= Url::toRoute(['user/role', 'id' => Yii::$app->user->identity->id]) ?>">
                <i class="fas fa-user pe-2"></i>Manage Roles
              </a>
            </li>
            <li>
              <?= Html::a('<i class="fas fa-sign-out-alt pe-2"></i>Logout', ['#'], [
                'class' => 'dropdown-item border-radius-md sign-out-user',
                'data' => [
                  'confirm' => 'Are you sure, you want to Logout?',
                  'url' => Url::toRoute(['site/logout']),
                  'method' => 'post',
                ]
              ]) ?>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>