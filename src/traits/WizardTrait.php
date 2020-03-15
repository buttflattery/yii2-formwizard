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

    public function isFormIdSet()
    {
        return isset($this->formOptions['id']);
    }

    public function isEmptySteps()
    {
        return empty($this->steps);
    }

    public function isContainerIdSet()
    {
        return isset($this->wizardContainerId);
    }

    /**
     * @return mixed
     */
    public function isThemeMaterial()
    {
        return $this->theme == self::THEME_MATERIAL || $this->theme == self::THEME_MATERIAL_V;
    }

    /**
     * @return mixed
     */
    public function isBs3()
    {
        return $this->_bsVersion == self::BS_3;
    }

    /**
     * @param $stepType
     * @return mixed
     */
    public function isTabularStep($stepType)
    {
        return $stepType === self::STEP_TYPE_TABULAR;
    }

    public function isPreviewStep($step){
        return empty($step['model']);
    }
}
