<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::$app->setup->company->getImagePath()]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body class="g-sidenav-show bg-gray-100">
    <!-- <body class="g-sidenav-show dark-version bg-gray-600"> -->
    <?php $this->beginBody() ?>
    <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('<?= Yii::getAlias("@web/img/profile-layout-header.webp") ?>'); background-position-y: 50%;">
        <span class="mask bg-primary opacity-6"></span>
    </div>
    <?= $this->render('aside') ?>

    <main class="main-content position-relative min-vh-100 max-height-vh-100 h-100">
        <?= $this->render('navbar') ?>
        <div class="container-fluid py-4">
            <?= $content ?>
        </div>
        <!-- <footer class="footer py-3">
            <div class="container-fluid">
                <div class="row align-items-center justify-content-lg-between">
                    <div class="col-lg-6 mb-lg-0 mb-4">
                        <div class="copyright text-center text-sm text-muted text-lg-start">
                            <div class="col-md-6 text-center text-md-start">&copy; <?= Yii::$app->setup->company->name ?> <?= date('Y') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </footer> -->
    </main>
    <?php $this->endBody() ?>
</body>
<?php
$script = <<<JS
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
JS;
$this->registerJs($script);
?>

</html>
<?= $this->render('_toast') ?>
<?php $this->endPage() ?>