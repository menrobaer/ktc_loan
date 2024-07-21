<?php

use yii\helpers\Url;

$controller = Yii::$app->controller->id;
$controllerAction = $controller . "-" . Yii::$app->controller->action->id;
?>
<!-- <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 " id="sidenav-main" data-color="dark"> -->
<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 " id="sidenav-main" data-color="dark">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    <a class="navbar-brand m-0" href="<?= Yii::$app->homeUrl ?>">
      <img src="<?= Yii::$app->setup->company->getImagePath() ?>" class="navbar-brand-img h-100" alt="main_logo">
      <span class="ms-1 font-weight-bold"><?= Yii::$app->setup->company->name ?></span>
    </a>
  </div>
  <hr class="horizontal dark mt-0">
  <div class="collapse navbar-collapse  w-auto h-auto" id="sidenav-collapse-main">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link <?= $controller == 'site' ? 'active' : ''; ?>" href="<?= Url::toRoute(['site/index']) ?>" role="button" aria-expanded="false">
          <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
            <i class="ni ni-shop text-primary text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Dashboards</span>
        </a>
      </li>
      <?php
      if (Yii::$app->setup->isLoanMonthly()) :
      ?>
        <li class="nav-item">
          <a class="nav-link <?= $controller == 'loan' ? 'active' : ''; ?>" href="<?= Url::toRoute(['loan/index']) ?>" role="button" aria-expanded="false">
            <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
              <i class="ni ni-money-coins text-warning text-sm opacity-10" style="top: 0;"></i>
            </div>
            <span class="nav-link-text ms-1">Loan</span>
          </a>
        </li>
      <?php
      else :
      ?>
        <li class="nav-item">
          <a class="nav-link <?= $controller == 'loan-daily' ? 'active' : ''; ?>" href="<?= Url::toRoute(['loan-daily/index']) ?>" role="button" aria-expanded="false">
            <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
              <i class="ni ni-money-coins text-warning text-sm opacity-10" style="top: 0;"></i>
            </div>
            <span class="nav-link-text ms-1">Loan</span>
          </a>
        </li>
      <?php
      endif;
      ?>
      <li class="nav-item">
        <a class="nav-link <?= $controller == 'customer' ? 'active' : ''; ?>" href="<?= Url::toRoute(['customer/index']) ?>" role="button" aria-expanded="false">
          <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
            <i class="far fa-user text-info text-sm opacity-10" style="top: 0;"></i>
          </div>
          <span class="nav-link-text ms-1">Customers</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= $controller == 'user' ? 'active' : ''; ?>" href="<?= Url::toRoute(['user/index']) ?>" role="button" aria-expanded="false">
          <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
            <i class="far fa-user text-warning text-sm opacity-10" style="top: 0;"></i>
          </div>
          <span class="nav-link-text ms-1">Employees</span>
        </a>
      </li>
      <li class="nav-item">
        <a data-bs-toggle="collapse" href="#reports" class="nav-link <?= $controller == 'report' ? 'active' : ''; ?>" aria-controls="reports" role="button" aria-expanded="<?= $controller == 'report' ? true : false; ?>">
          <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
            <i class="ni ni-single-copy-04 text-danger text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1">Reports</span>
        </a>
        <div class="collapse <?= $controller == 'report' ? 'show' : ''; ?>" id="reports">
          <ul class="nav ms-4">
            <li class="nav-item ">
              <a class="nav-link <?= $controllerAction == 'report-balance-statement' ? 'active' : '' ?>" href="<?= Url::toRoute(['report/balance-statement']) ?>">
                <span class="sidenav-mini-icon"> BS </span>
                <span class="sidenav-normal"> Balance Statement </span>
              </a>
            </li>
            <li class="nav-item ">
              <a class="nav-link <?= $controllerAction == 'report-payment-receivable' ? 'active' : '' ?>" href="<?= Url::toRoute(['report/payment-receivable']) ?>">
                <span class="sidenav-mini-icon"> PR </span>
                <span class="sidenav-normal"> Payment Receivable </span>
              </a>
            </li>
          </ul>
        </div>
      </li>
    </ul>
  </div>

</aside>