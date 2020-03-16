<?php
namespace buttflattery\formwizard\traits;

use yii\base\Model;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use buttflattery\formwizard\FormWizard;

trait StepTrait
{
    /**
     * Adds the dependent input script for the inputs
     *
     * @param array   $dependentInput the dependent input configurations
     * @param string  $attributeId    the id of the input it is applied on
     * @param object  $model          the model object for the dependent input
     * @param integer $attributeIndex the attribute index of the current attribute
     *
     * @return null
     */
    private function _addDependentInputScript(array $dependentInput, $attributeId, Model $model, $attributeIndex)
    {
        $dependentAttribute = $dependentInput['attribute'];
        $dependentValue = $model->$dependentAttribute;
        $dependentValueRequired = $dependentInput['when'];
        $dependentCondition = ArrayHelper::getValue($dependentInput, 'condition', '==');

        $dependentActionThen = ArrayHelper::getValue(
            $dependentInput,
            'then',
            "function(){\$('#{$attributeId}').show();}"
        );

        $dependentActionElse = ArrayHelper::getValue(
            $dependentInput,
            'else',
            "function(){\$('#{$attributeId}').hide();}"
        );

        $this->_dependentInputScript .= <<<JS

        let thenCallback_{$dependentAttribute}_{$attributeIndex}={$dependentActionThen};
        let elseCallback_{$dependentAttribute}_{$attributeIndex}={$dependentActionElse};

        if('{$dependentValue}'$dependentCondition'{$dependentValueRequired}'){
            thenCallback_{$dependentAttribute}_{$attributeIndex}.call(this,'{$attributeId}');
        }else{
            elseCallback_{$dependentAttribute}_{$attributeIndex}.call(this,'{$attributeId}');
        }
JS;
    }

    /**
     * Adds the Heading for the Step
     *
     * @param string $attribute    the name of the attribute
     *
     * @return mixed
     */
    public function addHeading($attribute, $stepHeadings)
    {

        $headingFields = ArrayHelper::getColumn($stepHeadings, 'before', true);
        if (in_array($attribute, $headingFields)) {
            $currentIndex = array_search($attribute, array_values($headingFields));
            $headingConfig = $stepHeadings[$currentIndex];

            //add heading
            return $this->_addHeading($headingConfig);
        }
        return '';
    }

    /**
     * Adds heading before the desired field
     *
     * @param array $headingConfig the configuration array
     *
     * @return HTML
     */
    private function _addHeading(array $headingConfig)
    {
        $headingText = $headingConfig['text'];
        $headingClass = ArrayHelper::getValue($headingConfig, 'className', 'field-heading');
        $headingIcon = ArrayHelper::getValue($headingConfig, 'icon', FormWizard::ICON_HEADING);

        return Html::tag('h3', $headingIcon . Html::encode($headingText), ['class' => $headingClass]);
    }

    /**
     * Adds the restore events for the fields
     *
     * @param array  $attributeConfig the configurations for the attribute
     * @param string $attributeId     the field attribute id
     *
     * @return null
     */
    private function _addRestoreEvents(array $attributeConfig, $attributeId)
    {
        $persistenceEvents = ArrayHelper::getValue($attributeConfig, 'persistencEvents', []);
        $formId = $this->formOptions['id'];

        foreach ($persistenceEvents as $eventName => $callback) {
            $eventCallBack = new JsExpression($callback);
            $this->_persistenceEvents .= <<<JS
            $(document).on("formwizard.{$formId}.{$eventName}","#{$formId} #{$attributeId}",{$eventCallBack});
JS;
        }
    }

    /**
     * Creates a default field for the steps if no fields under
     * the activefield config is provided
     *
     * @param object $model     instance of the current model
     * @param string $attribute name of the attribute / field
     *
     * @return \yii\widgets\ActiveField
     */
    public function createDefaultInput(Model $model, $attribute)
    {
        //create field
        $field = $this->createField($model, $attribute);
        return $field->textInput()->label(null, ['class' => 'form-label']);
    }

    /**
     * Creates a default ActiveFieldObject
     *
     * @param object  $model        instance of the current model
     * @param string  $attribute    name of the current field / attribute
     * @param array   $fieldOptions options for the field as in
     *                              \yii\widgets\ActiveField `fieldOptions`
     * @param boolean $isMulti      determines if the field will be using array name
     *                              or not for example : first_name[] will be used
     *                              if true and first_name if false
     *
     * @return \yii\widgets\ActiveField
     */
    public function createField(
        Model $model,
        $attribute,
        array $fieldOptions = [],
        $isMulti = false
    ) {
        return $this->form->field(
            $model,
            $attribute . ($isMulti ? '[]' : ''),
            $fieldOptions
        );
    }

    /**
     * Creates a customized input field according to the
     * structured option for the steps by user
     *
     * @param object $model       instance of the current model
     * @param string $attribute   name of the current field
     * @param array  $fieldConfig config for the current field
     *
     * @return \yii\widgets\ActiveField
     */
    public function createCustomInput(Model $model, $attribute, array $fieldConfig)
    {

        //get the options
        list(
            $options, $isMultiField, $fieldType, $widget, $template, $containerOptions, $inputOptions, $itemsList, $label, $labelOptions, $hintText
        ) = $this->_parseFieldConfig($fieldConfig);

        //create field
        $field = $this->createField(
            $model,
            $attribute,
            [
                'template' => $template,
                'options' => $containerOptions,
                'inputOptions' => $inputOptions,
            ],
            $isMultiField
        );

        //widget
        if ($widget) {
            $field = $field->widget($widget, $options)->label($label, $labelOptions);
            return (!$hintText) ? $field : $field->hint($hintText);
        }

        //remove the type and itemList from options list
        if (isset($options['type']) && $options['type'] !== 'number') {
            unset($options['type']);
        }

        //unset the itemsList from the options list
        unset($options['itemsList']);

        //init the options for the field types
        $fieldTypeOptions = [
            'field' => $field,
            'options' => $options,
            'labelOptions' => $labelOptions,
            'label' => $label,
            'itemsList' => $itemsList,
        ];

        //create the field
        return $this->_createField($fieldType, $fieldTypeOptions, $hintText);
    }

    /**
     * Creates a custom field of the given type
     *
     * @param string         $fieldType        the type of the field to be created
     * @param array          $fieldTypeOptions the optinos for the field to be created
     * @param boolean|string $hintText         the hint text to be used
     *
     * @return yii\widgets\ActiveField;
     */
    private function _createField($fieldType, array $fieldTypeOptions, $hintText = false)
    {
        $defaultFieldTypes = [
            'text' => function ($params) {
                $field = $params['field'];
                $options = $params['options'];
                $label = $params['label'];
                $labelOptions = $params['labelOptions'];

                return $field->textInput($options)->label($label, $labelOptions);
            },
            'number' => function ($params) {
                $field = $params['field'];
                $options = $params['options'];
                $label = $params['label'];
                $labelOptions = $params['labelOptions'];

                return $field->textInput($options)->label($label, $labelOptions);
            },
            'dropdown' => function ($params) {
                $field = $params['field'];
                $options = $params['options'];
                $label = $params['label'];
                $labelOptions = $params['labelOptions'];
                $itemsList = $params['itemsList'];

                return $field->dropDownList($itemsList, $options)
                    ->label($label, $labelOptions);
            },
            'radio' => function ($params) {
                $field = $params['field'];
                $options = $params['options'];
                $label = $params['label'];
                $labelOptions = $params['labelOptions'];
                $itemsList = $params['itemsList'];

                if (is_array($itemsList)) {
                    return $field->radioList($itemsList, $options)
                        ->label($label, $labelOptions);
                }
                return $field->radio($options);
            },
            'checkbox' => function ($params) {
                $field = $params['field'];
                $options = $params['options'];
                $label = $params['label'];
                $labelOptions = $params['labelOptions'];
                $itemsList = $params['itemsList'];

                //if checkboxList needs to be created
                if (is_array($itemsList)) {
                    return $field->checkboxList($itemsList, $options)
                        ->label($label, $labelOptions);
                }

                //if a single checkbox needs to be created
                $labelNull = $label === null;
                $labelOptionsEmpty = empty($labelOptions);
                $nothingSetByUser = ($labelNull && $labelOptionsEmpty);
                $label = $nothingSetByUser ? false : $label;

                return $field->checkbox($options)->label($label, $labelOptions);
            },
            'textarea' => function ($params) {
                $field = $params['field'];
                $options = $params['options'];
                $label = $params['label'];
                $labelOptions = $params['labelOptions'];

                return $field->textarea($options)->label($label, $labelOptions);
            },
            'file' => function ($params) {
                $field = $params['field'];
                $options = $params['options'];
                $label = $params['label'];
                $labelOptions = $params['labelOptions'];

                return $field->fileInput($options)->label($label, $labelOptions);
            },
            'hidden' => function ($params) {
                $field = $params['field'];
                $options = $params['options'];

                return $field->hiddenInput($options)->label(false);
            },
            'password' => function ($params) {
                $field = $params['field'];
                $options = $params['options'];
                $label = $params['label'];
                $labelOptions = $params['labelOptions'];

                return $field->passwordInput($options)->label($label, $labelOptions);
            },
        ];

        //create field depending on the type of the value provided
        if (array_key_exists($fieldType, $defaultFieldTypes)) {
            $field = $defaultFieldTypes[$fieldType]($fieldTypeOptions);
            return (!$hintText) ? $field : $field->hint($hintText);
        }
    }

    /**
     * Parse the configurations for the field
     *
     * @param array $fieldConfig the configurations array passed by the user
     *
     * @return array
     */
    private function _parseFieldConfig(array $fieldConfig)
    {
        //options
        $options = ArrayHelper::getValue($fieldConfig, 'options', []);

        //is multi field name
        $isMultiField = Arrayhelper::getValue($fieldConfig, 'multifield', false);

        //field type
        $fieldType = ArrayHelper::getValue($options, 'type', 'text');

        //widget
        $widget = ArrayHelper::getValue($fieldConfig, 'widget', false);

        //label configuration
        $labelConfig = ArrayHelper::getValue($fieldConfig, 'labelOptions', null);

        //template
        $template = ArrayHelper::getValue(
            $fieldConfig,
            'template',
            "{label}\n{input}\n{hint}\n{error}"
        );

        //container
        $containerOptions = ArrayHelper::getValue(
            $fieldConfig,
            'containerOptions',
            []
        );

        //inputOptions
        $inputOptions = ArrayHelper::getValue($fieldConfig, 'inputOptions', []);

        //items list
        $itemsList = ArrayHelper::getValue($options, 'itemsList', '');

        //label text
        $label = ArrayHelper::getValue($labelConfig, 'label', null);

        //label options
        $labelOptions = ArrayHelper::getValue($labelConfig, 'options', []);

        //get the hint text for the field
        $hintText = ArrayHelper::getValue($fieldConfig, 'hint', false);

        return [$options, $isMultiField, $fieldType, $widget, $template, $containerOptions, $inputOptions, $itemsList, $label, $labelOptions, $hintText];
    }

    /**
     * Allowed Row limit
     * 
     * @param int $modelIndex the model index
     * 
     * @return mixed
     */
    public function allowedRowLimit($modelIndex)
    {
        return $this->limit === FormWizard::ROWS_UNLIMITED || $this->limit > $modelIndex;
    }
}
