<?php

/** @var yii\web\View $this */

use yii\helpers\Url;

/** @var \app\components\Utils $utils */
$utils = Yii::$app->utils;
/** @var \app\components\Setup $setup */
$setup = Yii::$app->setup;
$this->title = $setup->company->name . " Dashboard";
?>
<style>
    .percentage.text-success:before {
        content: "+";
    }
</style>
<div class="site-index">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="card  mb-4">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">This month payment</p>
                                        <h5 class="font-weight-bolder">
                                            <?= $utils->DollarFormat($currentMonthPayment) ?>
                                        </h5>
                                        <p class="mb-0">
                                            <span class="percentage <?= $paymentPercentageChange > 0 ? 'text-success' : 'text-danger' ?> text-sm font-weight-bolder"><?= $paymentPercentageChange ?>%</span>
                                            to previous month
                                        </p>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                        <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="card  mb-4">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">New Customers</p>
                                        <h5 class="font-weight-bolder">
                                            <?= $currentMonthCustomer ?>
                                        </h5>
                                        <p class="mb-0">
                                            <span class="percentage <?= $customerPercentageChange > 0 ? 'text-success' : 'text-danger' ?> text-sm font-weight-bolder"><?= $customerPercentageChange ?>%</span>
                                            to previous month
                                        </p>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                        <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="card  mb-4">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">New Loans</p>
                                        <h5 class="font-weight-bolder">
                                            <?= $utils->DollarFormat($currentMonthLoan) ?>
                                        </h5>
                                        <p class="mb-0">
                                            <span class="percentage <?= $loanPercentageChange > 0 ? 'text-success' : 'text-danger' ?> text-sm font-weight-bolder"><?= $loanPercentageChange ?>%</span> to previous month
                                        </p>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                        <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-7 mb-4 mb-lg-0">
            <div class="card z-index-2 h-100">
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <h6 class="text-capitalize">Loans overview</h6>
                    <!-- <p class="text-sm mb-0">
                        <i class="fa fa-arrow-up text-success"></i>
                        <span class="font-weight-bold">4% more</span> in 2021
                    </p> -->
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="chart-line" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card card-carousel overflow-hidden h-100 p-0">
                <div id="carouselExampleCaptions" class="carousel slide h-100" data-bs-ride="carousel">
                    <div class="carousel-inner border-radius-lg h-100">
                        <?php
                        foreach ($recentlyLoans as $key => $loan) :
                        ?>
                            <div class="carousel-item h-100 <?= $key == 0 ? 'active' : '' ?>">
                                <div class="carousel-caption  top-0 text-start start-0 ms-5">
                                    <h5>Recently loan</h5>
                                    <div class="text-sm">
                                        <a href="<?= Url::toRoute(['loan/view', 'code' => $loan->generate_code]) ?>" class="text-info">#<?= $loan->generate_code ?></a>
                                        <span class="text-muted px-1">-</span>
                                        <span class="text-muted"><?= $utils->date($loan->date) ?></span>
                                    </div>
                                    <h6 class="text-danger mt-3"><?= $loan->item_name ?></h6>
                                    <div class="border-1 border-dashed text-danger p-3 border-radius-md d-inline-block">
                                        <h4 class="mb-0"><?= $utils->DollarFormat($loan->original_amount) ?></h4>
                                    </div>
                                </div>
                                <div class="carousel-caption  bottom-0 text-start start-0 mx-5">
                                    <div class="card">
                                        <div class="card-body py-3">
                                            <div class="d-flex align-items-center">
                                                <a href="javascript:;">
                                                    <img src="<?= $loan->customer->getAvatar() ?>" class="avatar" alt="profile-image">
                                                </a>
                                                <div class="mx-3">
                                                    <h6 class="mb-0"><?= $loan->customer->name ?></h6>
                                                    <small class="d-block text-muted"><?= $loan->customer->phone_number ?></small>
                                                </div>
                                            </div>
                                            <div class="pt-3">
                                                <a href="<?= Url::toRoute(['loan/view', 'code' => $loan->generate_code]) ?>" class="btn btn-block btn-xs btn-primary mb-0">
                                                    <i class="far fa-eye pe-2" aria-hidden="true"></i> View loan
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        <?php
                        endforeach;
                        ?>
                    </div>
                    <button class="carousel-control-prev w-5 me-3" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                        <i class="fas fa-chevron-left text-dark"></i>
                    </button>
                    <button class="carousel-control-next w-5 me-3 text-dark" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                        <i class="fas fa-chevron-right text-dark"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row d-none mt-4">
        <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
            <div class="card h-100 ">
                <div class="card-header">
                    <h5 class="mb-0 text-capitalize">Team members</h5>
                </div>
                <div class="card-body pt-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <div class="row align-items-center">
                                <div class="col-auto d-flex align-items-center">
                                    <a href="javascript:;" class="avatar">
                                        <img class="border-radius-lg" alt="Image placeholder" src="<?= Yii::getAlias("@web/theme/") ?>img/team-1.jpg">
                                    </a>
                                </div>
                                <div class="col ml-2">
                                    <h6 class="mb-0">
                                        <a href="javascript:;">John Michael</a>
                                    </h6>
                                    <span class="badge badge-success badge-sm">Online</span>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-outline-primary btn-xs mb-0">Add</button>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item px-0">
                            <div class="row align-items-center">
                                <div class="col-auto d-flex align-items-center">
                                    <a href="javascript:;" class="avatar">
                                        <img class="border-radius-lg" alt="Image placeholder" src="<?= Yii::getAlias("@web/theme/") ?>img/team-2.jpg">
                                    </a>
                                </div>
                                <div class="col ml-2">
                                    <h6 class="mb-0">
                                        <a href="javascript:;">Alex Smith</a>
                                    </h6>
                                    <span class="badge badge-warning badge-sm">in Meeting</span>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-xs btn-outline-primary mb-0">Add</button>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item px-0">
                            <div class="row align-items-center">
                                <div class="col-auto d-flex align-items-center">
                                    <a href="javascript:;" class="avatar">
                                        <img class="border-radius-lg" alt="Image placeholder" src="<?= Yii::getAlias("@web/theme/") ?>img/team-5.jpg">
                                    </a>
                                </div>
                                <div class="col ml-2">
                                    <h6 class="mb-0">
                                        <a href="javascript:;">Samantha Ivy</a>
                                    </h6>
                                    <span class="badge badge-danger badge-sm">Offline</span>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-xs btn-outline-primary mb-0">Add</button>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item px-0">
                            <div class="row align-items-center">
                                <div class="col-auto d-flex align-items-center">
                                    <a href="javascript:;" class="avatar">
                                        <img class="border-radius-lg" alt="Image placeholder" src="<?= Yii::getAlias("@web/theme/") ?>img/team-4.jpg">
                                    </a>
                                </div>
                                <div class="col ml-2">
                                    <h6 class="mb-0">
                                        <a href="javascript:;">John Michael</a>
                                    </h6>
                                    <span class="badge badge-success badge-sm">Online</span>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-xs btn-outline-primary mb-0">Add</button>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
            <div class="card h-100 ">
                <div class="card-header">
                    <h5 class="mb-0 text-capitalize">To do list</h5>
                </div>
                <div class="card-body pt-0">
                    <ul class="list-group list-group-flush" data-toggle="checklist">
                        <li class="checklist-entry list-group-item px-0">
                            <div class="checklist-item checklist-item-success checklist-item-checked d-flex">
                                <div class="checklist-info">
                                    <h6 class="checklist-title mb-0">Call with Dave</h6>
                                    <small class="text-xs">09:30 AM</small>
                                </div>
                                <div class="form-check my-auto ms-auto">
                                    <input class="form-check-input" type="checkbox" id="customCheck1" checked>
                                </div>
                            </div>
                        </li>
                        <li class="checklist-entry list-group-item px-0">
                            <div class="checklist-item checklist-item-warning d-flex">
                                <div class="checklist-info">
                                    <h6 class="checklist-title mb-0">Brunch Meeting</h6>
                                    <small class="text-xs">11:00 AM</small>
                                </div>
                                <div class="form-check my-auto ms-auto">
                                    <input class="form-check-input" type="checkbox" id="customCheck1">
                                </div>
                            </div>
                        </li>
                        <li class="checklist-entry list-group-item px-0">
                            <div class="checklist-item checklist-item-info d-flex">
                                <div class="checklist-info">
                                    <h6 class="checklist-title mb-0">Argon Dashboard Launch</h6>
                                    <small class="text-xs">02:00 PM</small>
                                </div>
                                <div class="form-check my-auto ms-auto">
                                    <input class="form-check-input" type="checkbox" id="customCheck1">
                                </div>
                            </div>
                        </li>
                        <li class="checklist-entry list-group-item px-0">
                            <div class="checklist-item checklist-item-danger checklist-item-checked d-flex">
                                <div class="checklist-info">
                                    <h6 class="checklist-title mb-0">Winter Hackaton</h6>
                                    <small>10:30 AM</small>
                                </div>
                                <div class="form-check my-auto ms-auto">
                                    <input class="form-check-input" type="checkbox" id="customCheck2" checked>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100 ">
                <div class="card-header">
                    <h5 class="mb-0 text-capitalize">Progress track</h5>
                </div>
                <div class="card-body pt-0">
                    <ul class="list-group list-group-flush list">
                        <li class="list-group-item px-0">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <a href="javascript:;" class="avatar rounded-circle">
                                        <img alt="Image placeholder" src="<?= Yii::getAlias("@web/theme/") ?>img/small-logos/logo-jira.svg">
                                    </a>
                                </div>
                                <div class="col">
                                    <h6>React Material Dashboard</h6>
                                    <div class="progress progress-xs mb-0">
                                        <div class="progress-bar bg-primary" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" style="width: 90%;"></div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item px-0">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <a href="javascript:;" class="avatar rounded-circle">
                                        <img alt="Image placeholder" src="<?= Yii::getAlias("@web/theme/") ?>img/small-logos/logo-asana.svg">
                                    </a>
                                </div>
                                <div class="col">
                                    <h6>Argon Design System</h6>
                                    <div class="progress progress-xs mb-0">
                                        <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"></div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item px-0">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <a href="javascript:;" class="avatar rounded-circle">
                                        <img alt="Image placeholder" src="<?= Yii::getAlias("@web/theme/") ?>img/small-logos/logo-spotify.svg">
                                    </a>
                                </div>
                                <div class="col">
                                    <h6>VueJs Now UI Kit PRO</h6>
                                    <div class="progress progress-xs mb-0">
                                        <div class="progress-bar bg-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item px-0">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <a href="javascript:;" class="avatar rounded-circle">
                                        <img alt="Image placeholder" src="<?= Yii::getAlias("@web/theme/") ?>img/small-logos/bootstrap.svg">
                                    </a>
                                </div>
                                <div class="col">
                                    <h6>Soft UI Dashboard</h6>
                                    <div class="progress progress-xs mb-0">
                                        <div class="progress-bar bg-gradient-primary" role="progressbar" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100" style="width: 72%;"></div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

$script = <<<JS
    var ctx1 = document.getElementById("chart-line").getContext("2d");

    var gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);

    gradientStroke1.addColorStop(1, 'rgba(94, 114, 228, 0.2)');
    gradientStroke1.addColorStop(0.2, 'rgba(94, 114, 228, 0.0)');
    gradientStroke1.addColorStop(0, 'rgba(94, 114, 228, 0)');
    new Chart(ctx1, {
        type: "line",
        data: {
            labels: $chartLabel,
            datasets: [{
            label: "Loan",
            tension: 0.4,
            borderWidth: 0,
            pointRadius: 0,
            borderColor: "#5e72e4",
            backgroundColor: gradientStroke1,
            borderWidth: 3,
            fill: true,
            data: $chartAmount,
            maxBarThickness: 6

            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
            legend: {
                display: false,
            }
            },
            interaction: {
            intersect: false,
            mode: 'index',
            },
            scales: {
            y: {
                grid: {
                drawBorder: false,
                display: true,
                drawOnChartArea: true,
                drawTicks: false,
                borderDash: [5, 5]
                },
                ticks: {
                display: true,
                padding: 10,
                color: '#fbfbfb',
                font: {
                    size: 11,
                    family: "Open Sans",
                    style: 'normal',
                    lineHeight: 2
                },
                }
            },
            x: {
                grid: {
                drawBorder: false,
                display: false,
                drawOnChartArea: false,
                drawTicks: false,
                borderDash: [5, 5]
                },
                ticks: {
                display: true,
                color: '#ccc',
                padding: 20,
                font: {
                    size: 11,
                    family: "Open Sans",
                    style: 'normal',
                    lineHeight: 2
                },
                }
            },
            },
        },
    });

JS;
$this->registerJs($script);
?>