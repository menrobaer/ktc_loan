<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700',
        'theme/css/nucleo-icons.css',
        'theme/css/nucleo-svg.css',
        'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css',
        'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css',

        'theme/css/argon-dashboard.min-v=2.0.5.css',
        'css/custom.css'
    ];
    public $js = [
        'theme/js/core/popper.min.js',
        'theme/js/core/bootstrap.min.js',
        // 'https://kit.fontawesome.com/42d5adcbca.js',
        'theme/js/plugins/perfect-scrollbar.min.js',
        'theme/js/plugins/smooth-scrollbar.min.js',
        'theme/js/plugins/dragula/dragula.min.js',
        'theme/js/plugins/jkanban/jkanban.js',
        'theme/js/plugins/chartjs.min.js',
        'https://cdn.jsdelivr.net/npm/sweetalert2@11',
        // 'https://cdn.jsdelivr.net/npm/flatpickr',
        'theme/js/plugins/flatpickr.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js',

        'theme/js/argon-dashboard.min-v=2.0.5.js',
        'js/custom.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset'
    ];
}
