<?php

namespace buttflattery\formwizard;

use buttflattery\formwizard\FormWizardAsset;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;

class FormWizard extends Widget {

    private $form;
    private $allFields = [];
    //options widget
    public $wizardContainerId;
    public $steps = [];
    public $formOptions = [];
    //plugin options
    public $theme = 'default';
    public $transitionEffect = 'slide';
    public $showStepURLhash = false;
    public $useURLhash = false;
    public $enableYiiActiveFormValidation = true;
    public $toolbarPosition = 'top';
    public $toolbarExtraButtons;
    public $markDoneStep = true;
    public $markAllPreviousStepsAsDone = true;
    public $removeDoneStepOnNavigateBack = false;
    public $enableAnchorOnDoneStep = true;
    public $labelNext = 'Next';
    public $labelPrev = 'Previous';
    public $labelFinish = 'Finish';
    public $classNext = 'btn btn-info';
    public $classPrev = 'btn btn-info';
    public $classFinish = 'btn btn-success';

    const THEME_DEFAULT = 'default';
    const THEME_DOTS = 'dots';
    const THEME_ARROWS = 'arrows';
    const THEME_CIRCLES = 'circles';
    const THEME_MATERIAL = 'material';
    const THEME_MATERIAL_V = 'material-v';

    public $themesSupported = [
        self::THEME_DOTS => 'Dots',
        self::THEME_CIRCLES => 'Circles',
        self::THEME_ARROWS => 'Arrows',
        self::THEME_MATERIAL => 'Material',
        self::THEME_MATERIAL_V => 'MaterialVerticle',
    ];

    /**
     * 
     */
    public function init() {
        parent::init();
        $this->setDefaults();
    }

    /**
     * 
     * @throws InvalidArgumentException
     */
    private function setDefaults() {
        if( empty($this->steps) ){
            throw new InvalidArgumentException('You must provide steps for the form.');
        }

        //set the form id for the form if not set by the user
        if( !isset($this->formOptions['id']) ){
            $this->formOptions['id'] = $this->getId() . '_form_wizard';
        } else{
            preg_match('/\b(\w+)\b/', $this->formOptions['id'], $matches);

            if( $matches[0] !== $this->formOptions['id'] ){
                throw new InvalidArgumentException('You must provide the id for the form that matches any word character (equal to [a-zA-Z0-9_])');
            }
        }

        //set default action of the form to the current controller/actio if not set by user
        $this->formOptions['action'] = ArrayHelper::getValue($this->formOptions, 'action', Url::to(['/' . Yii::$app->controller->id . '/' . Yii::$app->controller->action->id]));

        //widget container ID
        if( !isset($this->wizardContainerId) ){
            $this->wizardContainerId = $this->getId() . '-form_wizard_container';
        }

        //theme buttons material 
        if( $this->theme == self::THEME_MATERIAL || $this->theme == self::THEME_MATERIAL_V ){
            $this->classNext = 'btn bg-teal waves-effect';
            $this->classPrev = 'btn bg-teal waves-effect';
            $this->classFinish = 'btn bg-green waves-effect';
            $this->labelNext = '<i class="glyphicon glyphicon-menu-right"></i>';
            $this->labelPrev = '<i class="glyphicon glyphicon-menu-left"></i>';
        }
    }

    /**
     * 
     * @return type
     */
    public function getPluginOptions() {
        return [
            'selected' => 0,
            'theme' => $this->theme,
            'transitionEffect' => $this->transitionEffect,
            'showStepURLhash' => $this->showStepURLhash,
            'toolbarSettings' => [
                'toolbarPosition' => $this->toolbarPosition,
                'showNextButton' => false,
                'showPreviousButton' => false,
                'toolbarExtraButtons' => $this->toolbarExtraButtons,
            ],
            'anchorSettings' => [
                'anchorClickable' => false,
                'enableAllAnchors' => false,
                'markDoneStep' => $this->markDoneStep,
                'markAllPreviousStepsAsDone' => $this->markAllPreviousStepsAsDone,
                'removeDoneStepOnNavigateBack' => $this->removeDoneStepOnNavigateBack,
                'enableAnchorOnDoneStep' => $this->enableAnchorOnDoneStep,
            ],
        ];
    }

    /**
     * 
     */
    public function run() {
        parent::run();

        $wizardContainerId = $this->wizardContainerId;

        $pluginOptions = $this->getPluginOptions();
        $jsButton = <<< JS
        $.formwizard.helper.appendButtons({
            form:'#{$this->formOptions['id']}',
            labelNext:'{$this->labelNext}',
            labelPrev:'{$this->labelPrev}',
            labelFinish:'{$this->labelFinish}',
            classNext:'{$this->classNext}',
            classPrev:'{$this->classPrev}',
            classFinish:'{$this->classFinish}'
        }).concat({$pluginOptions['toolbarSettings']['toolbarExtraButtons']})
JS;
        $pluginOptions['toolbarSettings']['toolbarExtraButtons'] = new JsExpression($jsButton);

        //start ActiveForm tag
        $this->form = \yii\widgets\ActiveForm::begin($this->formOptions);

        //start container tag
        echo Html::beginTag('div', ['id' => $wizardContainerId]);
        //draw form steps
        echo $this->createFormWizard();
        //end container div tag
        echo Html::endTag('div');

        //end form tag
        $this->form->end();

        //get current view object
        $view = $this->getView();

        //get all fields json for javascript processing
        $fieldsJSON = Json::encode($this->allFields);

        //encode plugin options
        $pluginOptionsJson = Json::encode($pluginOptions);

        $this->registerScripts();

        //init script for the wizard
        $js = <<< JS
        //start observer for the smart wizard to run the script when the child HTMl elements are populated
        if('{$this->theme}'=='material' || '{$this->theme}'=='material-v'){
            $.formwizard.observer.start('#{$wizardContainerId}');
        }
        
        // Step show event
        $.formwizard.helper.updateButtons('#{$wizardContainerId}');

        // Smart Wizard
        $('#{$wizardContainerId}').smartWizard({$pluginOptionsJson});
        
        //bind Yii ActiveForm event afterValidate to check
        //only current steps fields for validation and allow to next step
        if($('#{$this->formOptions['id']}').yiiActiveForm('data').attributes.length){
            $.formwizard.validation.bindAfterValidate('#{$this->formOptions['id']}');
        }

        //fields list
        $.formwizard.fields.{$this->formOptions['id']}={$fieldsJSON};
JS;

        //register script
        $view->registerJs($js, View::POS_READY);
    }

    /**
     * 
     * @return type
     */
    public function createFormWizard() {
        //get the steps
        $steps = $this->steps;

        //start tabs html
        $htmlTabs = Html::beginTag('ul');

        //start Body steps html
        $htmlSteps = Html::beginTag('div');

        //loop thorugh all the steps
        foreach( $steps as $index => $step ){
            //create wizard steps
            list($tabs, $steps) = $this->createStep($index, $step);
            $htmlTabs .= $tabs;
            $htmlSteps .= $steps;
        }

        //end tabs html
        $htmlTabs .= Html::endTag('ul');

        //end steps html
        $htmlSteps .= Html::endTag('div');

        //return form wizard html
        return $htmlTabs . $htmlSteps;
    }

    /**
     * 
     * @param type $index
     * @param type $step
     * @return type
     */
    public function createStep($index, $step) {
        //step title
        $stepTitle = ArrayHelper::getValue($step, 'title','Step-' . ($index + 1));//!isset($step['title']) ? 'Step-' . ($index + 1) : $step['title'];

        //step description
        $stepDescription = ArrayHelper::getValue($step, 'description','Description');//!isset($step['description']) ? 'Sample Description' : $step['description'];

        $model = $step['model'];

        //form body info text
        $formInfoText = ArrayHelper::getValue($step,'formInfoText','Add ' . basename(get_class($model)) . ' details below');//!isset($step['formInfoText']) ? 'Add ' . basename(get_class($model)) . ' details below' : $step['formInfoText'];

        //get html tabs
        $htmlTabs = $this->createTabs($index, $stepDescription, $stepTitle);

        //get html body
        $htmlBody = $this->createBody($index, $formInfoText, $step);

        //return html
        return [$htmlTabs, $htmlBody];
    }

    /**
     * 
     * @param type $index
     * @param type $stepDescription
     * @param type $stepTitle
     * @return type
     */
    public function createTabs($index, $stepDescription, $stepTitle) {
        $html = '';

        //make tabs
        $html .= Html::beginTag('li');
        $html .= Html::beginTag('a', ['href' => '#step-' . $index]);
        $html .= $stepTitle . '<br />';
        $html .= Html::tag('small', $stepDescription);
        $html .= Html::endTag('a');
        $html .= Html::endTag('li');

        return $html;
    }

    /**
     * 
     * @param type $index
     * @param type $formInfoText
     * @param type $step
     * @return type
     */
    public function createBody($index, $formInfoText, $step) {
        $html = '';
        //make steps
        $html .= Html::beginTag('div', ['id' => 'step-' . $index]);
        $html .= Html::tag('h3', $formInfoText, ['class' => 'border-bottom border-gray pb-2']);
        $html .= Html::beginTag('div');
        $html .= $this->createStepFields($index, $step);
        $html .= Html::endTag('div');
        $html .= Html::endTag('div');
        return $html;
    }

    /**
     * 
     * @param type $step
     * @return type
     */
    public function createStepFields($index, $step) {
        $model = $step['model'];
        $htmlFields = '';

        //field configurations
        $fieldConfig = ArrayHelper::getValue($step,'fieldConfig',false);//!isset($step['fieldConfig']) ? false : $step['fieldConfig'];

        //disabled fields
        $disabledFields = ArrayHelper::getValue($fieldConfig, 'disabled',[]);//isset($fieldConfig['disabled']) ? $fieldConfig['disabled'] : [];

        //only fields
        $onlyFields = ArrayHelper::getValue($fieldConfig,'only',[]);//isset($fieldConfig['only']) ? $fieldConfig['only'] : [];

        //get safe attributes
        $attributes = $this->getStepFields($model, $onlyFields, $disabledFields);

        //add all the field ids to array
        $this->allFields[$index] = array_map(function ($element) use ($model){
            return Html::getInputId($model, $element);
        }, $attributes);

        //iterate all fields associated to the relevant model
        foreach( $attributes as $attribute ){

            if( $fieldConfig && isset($fieldConfig[$attribute]) ){
                //if filtered field
                $isFilteredField = $fieldConfig[$attribute] === false;

                //skip the field and go to next
                if( $isFilteredField ){
                    continue;
                }

                //custom field population
                $htmlFields .= $this->createCustomizedField($model, $attribute, $fieldConfig[$attribute]);
            } else{
                //default field population
                $htmlFields .= $this->createDefaultField($model, $attribute);
            }
        }
        return $htmlFields;
    }

    /**
     * 
     * @param type $model
     * @param type $onlyFields
     * @param type $disabledFields
     * @return type
     */
    public function getStepFields($model, $onlyFields, $disabledFields) {
        if( !empty($onlyFields) ){
            return array_values(array_filter(array_keys($model->getAttributes($model->safeAttributes())), function ($item) use ($onlyFields){
                        return in_array($item, $onlyFields);
                    }));
        }
        return array_filter(array_keys($model->getAttributes($model->safeAttributes())), function ($item) use ($disabledFields){
            return !in_array($item, $disabledFields);
        });
    }

    /**
     * 
     * @param type $model
     * @param type $attribute
     * @param type $fieldConfig
     * @return type
     */
    public function createCustomizedField($model, $attribute, $fieldConfig) {
        //options
        $options = ArrayHelper::getValue($fieldConfig,'options',[]);//isset($fieldConfig['options']) ? $fieldConfig['options'] : [];

        //field type
        $fieldType = ArrayHelper::getValue($options,'type','text');//isset($options['type']) ? $options['type'] : 'text';


        //widget
        $widget = ArrayHelper::getValue($fieldConfig, 'widget',false);//isset($fieldConfig['widget']) ? $fieldConfig['widget'] : false;

        //label configuration
        $labelConfig = ArrayHelper::getValue($fieldConfig,'labelOptions',null);//isset($fieldConfig['labelOptions']) ? $fieldConfig['labelOptions'] : null;

        //template
        $template = ArrayHelper::getValue($fieldConfig, 'template',"{label}\n{input}\n{hint}\n{error}");//isset($fieldConfig['template']) ? $fieldConfig['template'] : "{label}\n{input}\n{hint}\n{error}";

        //container
        $containerOptions = ArrayHelper::getValue($fieldConfig, 'containerOptions',[]);//isset($fieldConfig['containerOptions']) ? $fieldConfig['containerOptions'] : [];

        //items list
        $itemsList = ArrayHelper::getValue($options, 'itemsList','');//isset($options['itemsList']) ? $options['itemsList'] : '';


        //label text
        $label = ArrayHelper::getValue($labelConfig, 'label',null);//isset($labelConfig['label']) ? $labelConfig['label'] : null;

        //label options
        $labelOptions = ArrayHelper::getValue($labelConfig, 'options',[]);//isset($labelConfig['options']) ? $labelConfig['options'] : [];

        //create field
        $field = $this->createField($model, $attribute, ['template' => $template, 'options' => $containerOptions]);

        //remove the type and itemList from options
        unset($options['type']);
        unset($options['itemsList']);

        //widget
        if( $widget ){
            return $field->widget($widget, $options)->label($label, $labelOptions);
        }

        $defaultFieldTypes = [
            'text' => function($field, $options, $labelOptions, $label){
                return $field->textInput($options)->label($label, $labelOptions);
            },
            'dropdown' => function($field, $options, $labelOptions, $label, $itemsList){
                return $field->dropDownList($itemsList, $options)->label($label, $labelOptions);
            },
            'radio' => function($field, $options, $labelOptions, $label, $itemsList){
                if( is_array($itemsList) ){
                    return $field->radioList($itemsList, $options)->label($label, $labelOptions);
                } else{
                    return $field->radio($options);
                }
            },
            'checkbox' => function($field, $options, $labelOptions, $label, $itemsList){
                if( is_array($itemsList) ){
                    return $field->checkboxList($itemsList, $options)->label($label, $labelOptions);
                } else{
                    $labelNull = $label === null;
                    $labelOptionsEmpty = empty($labelOptions);
                    $nothingSetByUser = ($labelNull && $labelOptionsEmpty);
                    $label = $nothingSetByUser ? false : $label;

                    return $field->checkbox($options)->label($label, $labelOptions);
                }
            },
            'textarea' => function($field, $options, $labelOptions, $label){
                return $field->textarea($options)->label($label, $labelOptions);
            }
        ];

        //create field depending on the type of the value provided
        if( isset($defaultFieldTypes[$fieldType]) ){
            return $defaultFieldTypes[$fieldType]($field, $options, $labelOptions, $label, $itemsList);
        }
    }

    /**
     * 
     * @param type $model
     * @param type $attribute
     * @return type
     */
    public function createDefaultField($model, $attribute) {
        $columnMapping = [
            'string' => function ($field){
                return $field->textInput()->label(null, ['class' => 'form-label']);
            },
            'integer' => function ($field){
                return $field->textInput()->label(null, ['class' => 'form-label']);
            },
        ];

        //get column schema
        $columnSchema = $model->getTableSchema()->getColumn($attribute);

        //create field
        $field = $this->createField($model, $attribute);

        if( isset($columnMapping[$columnSchema->phpType]) ){
            return $columnMapping[$columnSchema->phpType]($field);
        } else{
            // throw new InvalidParamException("The Field Type {$columnSchema->type} Provided for the column {$attribute} is invalid");
        }
    }

    /**
     * 
     * @param type $model
     * @param type $attribute
     * @param type $fieldOptions
     * @return type
     */
    public function createField($model, $attribute, $fieldOptions = []) {
        return $this->form->field($model, $attribute, $fieldOptions);
    }

    /**
     * 
     */
    public function registerScripts() {
        $view = $this->getView();

        //register plugin assets
        FormWizardAsset::register($view);

        //register theme specific files
        $themeSelected = $this->theme;

        //is supported theme
        if( in_array($themeSelected, array_keys($this->themesSupported)) ){
            $themeAsset = __NAMESPACE__ . '\Theme' . $this->themesSupported[$themeSelected] . 'Asset';
            $themeAsset::register($view);
        }
    }

}
