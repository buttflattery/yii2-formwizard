<?php
namespace buttflattery\formwizard\step;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use buttflattery\formwizard\traits\StepTrait;

class Normal
{
    use StepTrait;
    /**
     * @var mixed
     */
    public $models;

    /**
     * @var mixed
     */
    public $form;

    /**
     * @var mixed
     */
    public $fieldConfig;

    /**
     * @var mixed
     */
    public $attributes;

    /**
     * @var mixed
     */
    private $_persistenceEvents;

    /**
     * @var mixed
     */
    private $_dependentInputScript;

    /**
     * @return mixed
     */
    public function getPersistenceEvents()
    {
        return $this->_persistenceEvents;
    }

    /**
     * @param $script
     */
    public function setDependentInputScript($script)
    {
        $this->_dependentInputScript = $script;
    }

    /**
     * @return mixed
     */
    public function getDependentInputScript()
    {
        return $this->_dependentInputScript;
    }

    /**
     * Creates a Step
     *
     * @return mixed
     */
    public function create()
    {
        $models = $this->models;

        //field configurations
        $fieldConfig = ArrayHelper::getValue($this->stepConfig, 'fieldConfig', false);

        //get the step headings
        $stepHeadings = ArrayHelper::getValue($this->stepConfig, 'stepHeadings', false);

        $sorter = Yii::createObject(
            [
                'class' => Sorter::class,
                'models' => $models,
                'stepConfig' => $this->stepConfig,
            ]
        );

        $attributes = $sorter->sort();

        $this->attributes = $attributes;
        return $this->_createStepHtml($fieldConfig, $stepHeadings);
    }

    /**
     * Generates Html for normal steps fields
     *
     * @param array $attributes   the attributes to iterate
     * @param array $fieldConfig  customer field configurations
     * @param array $stepHeadings the headings for the current step
     *
     * @return mixed
     */
    private function _createStepHtml($fieldConfig, $stepHeadings)
    {
        $htmlFields = '';

        foreach ($this->attributes as $modelIndex => $row) {

            $model = $row['model'];
            $attribute = $row['attribute'];

            //prefix attributes with model name
            $attributePrefixed = strtolower($model->formName()) . '.' . $attribute;

            //attribute name
            $attributeName = $attribute;
            $customConfigDefinedForField = $fieldConfig && (isset($fieldConfig[$attribute]) || isset($fieldConfig[$attributePrefixed]));

            //has heading for the field
            $hasHeading = false !== $stepHeadings;

            //add heading
            if ($hasHeading) {
                $htmlFields .= $this->addHeading($attribute, $stepHeadings);
            }

            //if custom config available for field
            if ($customConfigDefinedForField) {

                $customFieldConfig = (isset($fieldConfig[$attributePrefixed])) ? $fieldConfig[$attributePrefixed] : $fieldConfig[$attribute];
                $dependentInput = ArrayHelper::getValue($customFieldConfig, 'depends', false);

                //if filtered field
                $isFilteredField = $customFieldConfig === false;

                //skip the field and go to next
                if ($isFilteredField) {
                    continue;
                }

                //custom field population
                $htmlFields .= $this->createCustomInput(
                    $model,
                    $attributeName,
                    $customFieldConfig
                );

                //id of the input
                $attributeId = Html::getInputId($model, $attributeName);

                //add the restore events
                $this->_addRestoreEvents($customFieldConfig, $attributeId);

                //add dependent input script if available
                $dependentInput && $this->_addDependentInputScript($dependentInput, $attributeId, $model, $modelIndex);
            } else {
                //default field population
                $htmlFields .= $this->createDefaultInput($model, $attributeName);
            }
        }
        return $htmlFields;
    }
}