<?php
namespace buttflattery\formwizard\assetbundles\bs4;

use yii\web\AssetBundle;

class ThemeBase extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../../assets/';
    public $baseUrl = '@web';

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap4\BootstrapAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
    ];
}
