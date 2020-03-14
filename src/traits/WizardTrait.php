<?php
/**
 * Wizard trait
 */
namespace buttflattery\formwizard\traits;

use yii\base\InvalidArgumentException as ArgException;

trait WizardTrait
{
    /**
     * Check if tabular step has the multiple models if the same type or throw an exception
     *
     * @param array $models the model(s) of the step
     *
     * @return null
     * @throws ArgException
     */
    private function _checkTabularConstraints(array $models)
    {
        $classes = [];
        foreach ($models as $model) {
            $classes[] = get_class($model);
        }
        $classes = array_unique($classes);

        //check if not a multiple model step with the type set to tabular
        if (sizeof($classes) > 1) {
            throw new ArgException(self::MSG_TABULAR_CONSTRAINT);
        }
        return true;
    }
}
