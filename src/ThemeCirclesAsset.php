<?php
namespace buttflattery\formwizard;

use buttflattery\formwizard\ThemeBase;

class ThemeCirclesAsset extends ThemeBase
{

    public $css = [
        'css/theme/smart_wizard_theme_circles.css',
    ];

    public function init()
    {
        parent::init();
        array_push($this->depends, 'buttflattery\formwizard\FormWizardAsset');
    }
}
