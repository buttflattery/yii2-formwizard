<?php
namespace buttflattery\formwizard\assetbundles\bs4;

use buttflattery\formwizard\assetbundles\bs4\ThemeBase;

class ThemeCirclesAsset extends ThemeBase
{

    public $css = [
        'css/theme/smart_wizard_theme_circles.min.css',
    ];

    public function init()
    {
        parent::init();
        array_push($this->depends, 'buttflattery\formwizard\assetbundles\bs4\FormWizardAsset');
    }
}
