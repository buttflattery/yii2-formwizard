<?php
namespace buttflattery\formwizard;

use buttflattery\formwizard\ThemeBase;

class ThemeDotsAsset extends ThemeBase
{

    public $css = [
        'css/theme/smart_wizard_theme_dots.css',
    ];

    public function init()
    {
        parent::init();
        array_push($this->depends, 'buttflattery\formwizard\FormWizardAsset');
    }
}
