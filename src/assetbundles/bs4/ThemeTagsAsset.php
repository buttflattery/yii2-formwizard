<?php
namespace buttflattery\formwizard\assetbundles\bs4;

use buttflattery\formwizard\assetbundles\bs4\ThemeBase;

class ThemeNoteBookAsset extends ThemeBase
{

    /**
     * @var array
     */
    public $js = [
        'js/theme/waves.js',
        'js/theme/material.js'
    ];

    /**
     * @var array
     */
    public $css = [
        'css/theme/smart_wizard_theme_tags.min.css',
        'css/theme/waves.css'
    ];

    public function init()
    {
        parent::init();
        array_push($this->depends, 'buttflattery\formwizard\assetbundles\bs4\FormWizardAsset');
    }
}
