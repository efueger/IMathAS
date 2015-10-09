<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use app\components\AppConstant;
use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\web\YiiAsset',
    ];
    public $css = [
        'css/imascore.css?ver=14',
        'css/jquery-ui.min.css?ver=14',
        'css/jquery-ui.structure.min.css?ver=14',
        'css/site.css?ver=14',
        'css/default.css?ver=14',
        'css/font-awesome.min.css?ver=14'
    ];
    public $jsOptions = array(
        'position' => \yii\web\View::POS_HEAD
    );
    public $js = [
        'js/mathjax/MathJax.js?config=AM_HTMLorMML',
        'js/ASCIIsvg_min.js?ver=13',
        'js/mathgraphcheck.js?ver=13',
        'js/jquery-ui.min.js?ver=13',
        'js/common.js?ver=13',
    ];
}