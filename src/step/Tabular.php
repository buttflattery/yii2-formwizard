<?php
//@codingStandardsIgnoreStart
namespace buttflattery\formwizard\step;

use Yii;
use yii\base\Model;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use buttflattery\formwizard\FormWizard;
use buttflattery\formwizard\traits\StepTrait;

class Tabular
{
    //@codingStandardsIgnoreEnd

    use StepTrait;
    /**
     * The current form obect
     *
     * @var mixed
     */
    public $form;

    /**
     * The models for the tabular step
     *
     * @var mixed
     */
    public $models;

    /**
     * Step configuration
     *
     * @var mixed
     */
    public $stepConfig;

    /**
     * Step index
     *
     * @var mixed
     */
    public $index;

    /**
     * Form options
     *
     * @var mixed
     */
    public $formOptions;

    /**
     * Dependent input script
     *
     * @var mixed
     */
    private $_dependentInputScript;

    /**
     * Tabular event js
     *
     * @var mixed
     */
    private $_tabularEventJs;

    /**
     * Persistence events script
     *
     * @var mixed
     */
    private $_persistenceEvents;

    /**
     * @return mixed
     */
    public function getPersistenceEvents()
    {
        return $this->_persistenceEvents;
    }

    /**
     * Returns the tabularEventJs
     *
     * @return mixed
     */
    public function getTabularEventJs()
    {
        return $this->_tabularEventJs;
    }

    /**
     * Sets the Dependent Input script
     *
     * @param string $script the script for the dependent input
     */
    public function setDependentInputScript($script)
    {
        $this->_dependentInputScript = $script;
    }

    /**
     * Returns the dependent input script
     *
     * @return mixed
     */
    public function getDependentInputScript()
    {
        return $this->_dependentInputScript;
    }

    /**
     * Creates the tabular step
     *
     * @return mixed
     */
    public function create()
    {

        //field configurations
        $fieldConfig = ArrayHelper::getValue($this->stepConfig, 'fieldConfig', false);

        //disabled fields
        $disabledFields = ArrayHelper::getValue($fieldConfig, 'except', []);

        //get the step headings
        $stepHeadings = ArrayHelper::getValue($this->stepConfig, 'stepHeadings', false);

        //only fields
        $onlyFields = ArrayHelper::getValue($fieldConfig, 'only', []);

        $html = '';

        $sorter = Yii::createObject(
            [
                'class' => Sorter::class,
                'stepConfig' => $this->stepConfig,
            ]
        );

        foreach ($this->models as $modelIndex => $model) {

            //get safe attributes
            $attributes = $sorter->getStepFields($model, $onlyFields, $disabledFields);

            //sort fields
            $sorter->sortFields($attributes);

            //add tabular row if limit not exceed
            if (!$this->addTabularRow($model, $modelIndex, $html, $fieldConfig, $attributes, $stepHeadings)) {
                break;
            }
        }

        return $html;

    }

    /**
     * Adds a tabular row in the tabular step
     *
     * @param object  $model        the model object
     * @param integer $modelIndex   the model index for the tabular step model
     * @param integer $stepIndex    the current step index
     * @param string  $htmlFields   the html for the fields
     * @param array   $fieldConfig  the field configurations
     * @param array   $attributes   the list of the attributes in the current model
     * @param integer $limitRows    the rows limit if set
     * @param mixed   $stepHeadings the stepheadings configurations
     *
     * @return boolean
     */
    protected function addTabularRow(
        Model $model, $modelIndex, &$htmlFields,
        array $fieldConfig, array $attributes,
        $stepHeadings
    ) {

        //limit not exceeded
        if ($this->allowedRowLimit($modelIndex)) {
            //start the row constainer
            $htmlFields .= Html::beginTag('div', ['id' => 'row_' . $modelIndex, 'class' => 'tabular-row']);

            //add the remove icon if edit mode and more than one rows
            ($modelIndex > 0) && $htmlFields .= Html::tag('i', '', ['class' => 'remove-row formwizard-x-ico', 'data' => ['rowid' => $modelIndex]]);

            //generate the html for the step
            $htmlFields .= $this->_createTabularStepHtml($attributes, $modelIndex, $model, $fieldConfig, $stepHeadings);

            //close row div
            $htmlFields .= Html::endTag('div');
            return true;
        }
        return false;
    }

    /**
     * Generates Html for the tabular step fields
     *
     * @param array   $attributes    the attributes to iterate
     * @param integer $modelIndex    the index of the current model
     * @param object  $model         the model object
     * @param array   $fieldConfig   customer field confitigurations
     * @param mixed   $stepHeadings  the headings configurations for the current step, false if not provided
     *
     * @return mixed
     */
    private function _createTabularStepHtml(array $attributes, $modelIndex, Model $model, array $fieldConfig, $stepHeadings)
    {
        $htmlFields = '';
        $stepIndex = $this->index;

        //prefix attributes with model name
        $attributesPrefixed = preg_filter('/^/', strtolower($model->formName()) . '.', $attributes);

        //iterate all fields associated to the relevant model
        foreach ($attributes as $attributeIndex => $attribute) {

            //attribute name
            $attributeName = "[$modelIndex]" . $attribute;
            $customConfigDefinedForField = $fieldConfig && (isset($fieldConfig[$attribute]) || isset($fieldConfig[$attributesPrefixed[$attributeIndex]]));

            //has heading for the field
            $hasHeading = false !== $stepHeadings;

            //add heading
            if ($hasHeading) {
                $htmlFields .= $this->addHeading($stepHeadings, $attribute);
            }

            //if custom config available for field
            if ($customConfigDefinedForField) {

                $customFieldConfig = (isset($fieldConfig[$attributesPrefixed[$attributeIndex]])) ? $fieldConfig[$attributesPrefixed[$attributeIndex]] : $fieldConfig[$attribute];
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

                //add tabular events
                $this->_addTabularEvents($customFieldConfig, $modelIndex, $attributeId, $stepIndex);

                //add the restore events
                $this->_addRestoreEvents($customFieldConfig, $attributeId);

                //add dependent input script if available
                $dependentInput && $this->_addDependentInputScript($dependentInput, $attributeId, $model, $attributeIndex);

                //go to next iteration, add after removing the else part of this if statement
                continue;
            }

            //default field population
            $htmlFields .= $this->createDefaultInput($model, $attributeName);
        }

        return $htmlFields;
    }

    /**
     * Adds tabular events for the attribute
     *
     * @param array   $attributeConfig attribute configurations passed
     * @param int     $modelIndex      the index of the current model
     * @param string  $attributeId     the id of the current field
     * @param int     $index           the index of the current step
     *
     * @return null
     */
    private function _addTabularEvents(array $attributeConfig, $modelIndex, $attributeId, $index)
    {
        //get the tabular events for the field
        $tabularEvents = ArrayHelper::getValue($attributeConfig, 'tabularEvents', false);

        //check if tabular step and tabularEvents provided for field
        if (is_array($tabularEvents) && $modelIndex == 0) {

            //id of the form
            $formId = $this->formOptions['id'];

            //iterate all events attached and bind them
            foreach ($tabularEvents as $eventName => $callback) {
                //get the call back
                $eventCallBack = new JsExpression($callback);

                $this->_bindEvents($eventName, $eventCallBack, $formId, $index, $attributeId);
            }
        }
    }

    /**
     * Binds the tabular events provided by the user
     *
     * @param string  $eventName     the name of the event to bind
     * @param string  $eventCallBack the js callback event provided by the user
     * @param string  $formId        the id of the form
     * @param integer $index         the current model index
     * @param string  $attributeId   the attribute id to triger the event for
     *
     * @return null
     */
    private function _bindEvents($eventName, $eventCallBack, $formId, $index, $attributeId)
    {
        $formEvents = [
            'afterInsert' => function ($eventName, $formId, $index, $eventCallBack) {
                $this->_tabularEventJs .= <<<JS
                    $(document).on("formwizard.{$eventName}","#{$formId} #step-{$index} .fields_container>div[id^='row_']",{$eventCallBack});
JS;
            },
            'afterClone' => function ($eventName, $formId, $index, $eventCallBack, $attributeId) {
                $this->_tabularEventJs .= <<<JS
                    $(document).on("formwizard.{$eventName}","#{$formId} #step-{$index} #{$attributeId}",{$eventCallBack});
JS;
            },
            'beforeClone' => function ($eventName, $formId, $index, $eventCallBack, $attributeId) {
                $this->_tabularEventJs .= <<<JS
                    $(document).on("formwizard.{$eventName}","#{$formId} #step-{$index} #{$attributeId}",{$eventCallBack});
JS;
            },
        ];
        //call the event array literals
        array_key_exists($eventName, $formEvents) && $formEvents[$eventName]($eventName, $formId, $index, $eventCallBack, $attributeId);
    }

}
