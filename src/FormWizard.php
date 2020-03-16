<?php
/**
 * PHP VERSION >=5.6
 *
 * @category  Yii2-Plugin
 * @package   Yii2-formwizard
 * @author    Muhammad Omer Aslam <buttflattery@gmail.com>
 * @copyright 2018 IdowsTECH
 * @license   https://github.com/buttflattery/yii2-formwizard/blob/master/LICENSE BSD License 3.01
 * @link      https://github.com/buttflattery/yii2-formwizard
 */
namespace buttflattery\formwizard;

use Yii;
use yii\web\View;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use buttflattery\formwizard\step\Generator;
use yii\bootstrap\ActiveForm as BS3ActiveForm;
use buttflattery\formwizard\traits\WizardTrait;
use yii\bootstrap4\ActiveForm as BS4ActiveForm;
use yii\base\InvalidArgumentException as ArgException;
use buttflattery\formwizard\assetbundles\bs3\FormWizardAsset as Bs3Assets;
use buttflattery\formwizard\assetbundles\bs4\FormWizardAsset as Bs4Assets;

/**
 * A Yii2 plugin used for creating stepped form or form wizard using
 * yii\widgets\ActiveForm and \yii\db\ActiveRecord, it uses smart wizard
 * library for creating the form interface that uses 3 builtin and 2 extra themes,
 * moreover you can also create your own customized theme too.
 *
 * @category  Yii2-Plugin
 * @package   Yii2-formwizard
 * @author    Muhammad Omer Aslam <buttflattery@gmail.com>
 * @copyright 2018 IdowsTECH
 * @license   https://github.com/buttflattery/yii2-formwizard/blob/master/LICENSE BSDLicense 3.01
 * @version   Release: 1.0
 * @link      https://github.com/buttflattery/yii2-formwizard
 */
class FormWizard extends Widget
{

    use WizardTrait;

    /**
     * Holds the ActiveForm object
     *
     * @var mixed
     */
    private $_form;

    /**
     * Holds the collection of fields that are validated
     *
     * @var array
     */
    private $_allFields = [];

    /**
     * The Bootstrap Version to be loaded for the extension
     *
     * @var mixed
     */
    private $_bsVersion;

    /**
     * Used for collecting user provided custom Js for the formwizard.beforeClone event
     *
     * @var mixed
     */
    private $_tabularEventJs;

    /**
     * Used for adding limit var for the tabular steps to be used in javascript
     *
     * @var mixed
     */
    private $_rowLimitJs;

    /**
     * Used for collecting user provided callback for the event formwizard.afterRestore
     *
     * @var mixed
     */
    private $_persistenceEvents;

    /**
     * @var array
     */
    private $_previewHeadings = [];

    /**
     * @var mixed
     */
    private $_dependentInputScript;

    //options widget

    /**
     * The Main Wizard container id, this is assigned automatically if not assigned
     *
     * @var mixed
     */
    public $wizardContainerId;

    /**
     * Force use of the bootstrap version in case you have some
     * extension having dependencies on BS4 even though you are
     * using BS3 on the site overall
     *
     * @var mixed
     */
    public $forceBsVersion = false;

    /**
     * The array of steps that are to be created for the FormWizard,
     * this option is compulsary.
     *
     * Example:
     * steps=>[
     *      [
     *          "model"=>$model,
     *          "title"=>"Step Title"
     *      ],
     *      [
     *          "model"=>$modelUser
     *          "title"=>"Step Title"
     *      ]
     * ]
     *
     * @var array
     */
    public $steps = [];

    /**
     * Enable Edit mode for a saved record, it will enable all the steps
     * and user can jump to any steps by just clicking on the step anchor
     * so that if any single information needs to be changed in any step
     * he/she wont have to go through every step serially.
     *
     * @var mixed
     */
    public $editMode = false;

    /**
     * The array of steps that have errors
     *
     * @var array
     */
    public $errorSteps = [];

    /**
     * The Options for the ActiveForm see the
     * https://www.yiiframework.com/doc/api/2.0/yii-widgets-activeform
     * for the list of options that you can pass
     *
     * @var array
     */
    public $formOptions = [];

    //plugin options

    /**
     * The theme to be used for the formWizard plugin.
     * The `default` theme is used by Default.
     *
     * @var string
     */
    public $theme = self::THEME_DEFAULT;

    /**
     * The transition effect that is to be used for the steps while changing.
     * The Default is the `silde` effect
     *
     * @var string
     */
    public $transitionEffect = 'slide';

    /**
     * Automatically adjust content height, default value is `true`.`
     *
     * @var boolean
     */
    public $autoAdjustHeight = true;
    /**
     * An array of step numbers to show as disabled,
     * zero based array of step index ex: [2,4]
     *
     * @var array
     */
    public $disabledSteps = [];

    /**
     * Wether to show the step URL Hash in the url hash based on step,
     * Default is `false`
     *
     * @var boolean
     */
    public $showStepURLhash = false;

    /**
     * Enable selection of the step based on url hash, the default is `false`.
     *
     * @var mixed
     */
    public $useURLhash = false;

    /**
     * The position of the toolbar tht holds the buttons Next & Prev.
     *
     * @var string
     */
    public $toolbarPosition = 'top';

    /**
     * The Toolbar Extra buttons to be created.
     *
     * @var mixed
     */
    public $toolbarExtraButtons;

    /**
     * Mark the steps that are completed, default is `true`.
     *
     * @var mixed
     */
    public $markDoneStep = true;

    /**
     * Mark all the previous steps as completed, default is `true`.
     *
     * @var mixed
     */
    public $markAllPreviousStepsAsDone = true;

    /**
     * Mark a step as incomplete if moved to a previuos step. Default is `false`.
     *
     * @var mixed
     */
    public $removeDoneStepOnNavigateBack = true;

    /**
     * Enable/Disable the done steps navigation default is `true`.
     *
     * @var mixed
     */
    public $enableAnchorOnDoneStep = true;

    /**
     * Enable Preview Step option, default value `false`
     *
     * @var boolean
     */
    public $enablePreview = false;

    /**
     * Label used for the empty input in the preview step, default `NA`
     *
     * @var string
     */
    public $previewEmptyText = 'NA';

    /**
     * Enables restoring of the data for the unsaved form
     *
     * @var boolean
     */
    public $enablePersistence = false;

    /**
     * The Text label for the Next button. Default is `Next`.
     *
     * @var string
     */
    public $labelNext = 'Next';

    /**
     * The Text label for the Previous button. Default is `Previous`.
     *
     * @var string
     */
    public $labelPrev = 'Previous';

    /**
     * The Text label for the Finish button. Default is `Finish`.
     *
     * @var string
     */
    public $labelFinish = 'Finish';

    /**
     * The label text for the restore button
     *
     * @var string
     */
    public $labelRestore = 'Restore';

    /**
     * The icon for the Next button you want to be shown inside the button.
     * Default is `<i class="formwizard-arrow-right-alt1-ico"></i>`.
     *
     * This can be an html string '<i class="formwizard-arrow-right-alt1-ico"></i>'
     * in case you are using FA,Material or Glyph icons or an image tag
     * like '<img src="/path/to/image" />'.
     *
     * @var mixed
     */
    public $iconNext = self::ICON_NEXT;

    /**
     * The icon for the Previous button you want to be shown inside the button.
     * Default is `<i class="formwizard-arrow-left-alt1-ico"></i>`.
     *
     * This can be an html string '<i class="fa fa-arrow-left"></i>'
     * in case you are using FA,Material or Glyph icons or an image tag
     * like '<img src="/path/to/image" />'.
     *
     * @var mixed
     */
    public $iconPrev = self::ICON_PREV;

    /**
     * The icon for the Previous button you want to be shown inside the button.
     * Default is `<i class="formwizard-check-alt-ico"></i>`.
     *
     * This can be an html string '<i class="fa fa-done"></i>'
     * in case you are using FA,Material or Glyph icons or an image tag
     * like '<img src="/path/to/image" />'.
     *
     * @var mixed
     */
    public $iconFinish = self::ICON_FINISH;

    /**
     * The icon for the Add Row button you want to be shown inside the button.
     * Default is `<i class="formwizard-check-alt-ico"></i>`.
     *
     * This can be an html string '<i class="fa fa-add"></i>'
     * in case you are using FA, Material or Glyph icons, or an
     * image tag like '<img src="/path/to/image" />'.
     *
     * @var mixed
     */
    public $iconAdd = self::ICON_ADD;

    /**
     * The icon for the Restore button you want to be shown inside the button.
     * Default is `<i class="formwizard_restore-ico"></i>`.
     *
     * This can be an html string '<i class="fa fa-restore"></i>'
     * in case you are using FA, Material or Glyph icons, or an
     * image tag like '<img src="/path/to/image" />'.
     *
     * @var mixed
     */
    public $iconRestore = self::ICON_RESTORE;

    /**
     * The class for the Next button , default is `btn btn-info`
     *
     * @var string
     */
    public $classNext = 'btn btn-info ';

    /**
     * The class for the Previous button , default is `btn btn-info`
     *
     * @var string
     */
    public $classPrev = 'btn btn-info ';

    /**
     * The class for the Finish button, default is `btn btn-success`
     *
     * @var string
     */
    public $classFinish = 'btn btn-success ';

    /**
     * The class for the Add Row button, default is btn btn-info
     *
     * @var string
     */
    public $classAdd = 'btn btn-info ';

    /**
     * The class for the Add Row button, default is btn btn-info
     *
     * @var string
     */
    public $classRestore = 'btn btn-success ';

    /**
     * @var string
     */
    public $classListGroup = 'list-group';

    /**
     * @var string
     */
    public $classListGroupHeading = 'list-group-heading';

    /**
     * @var string
     */
    public $classListGroupItem = 'list-group-item-success';

    /**
     * @var string
     */
    public $classListGroupBadge = 'success';

    /**
     * BS VERSION
     */
    const BS_3 = 3;
    const BS_4 = 4;

    /**
     * TEXT LABELS CONSTANTS
     * */
    const DEFAULT_FORM_INFO_TEXT = 'Add details below';
    const DEFAULT_STEP_DESCRIPTION = 'Description';
    const PREVIEW_TITLE = 'Final Preview';
    const PREVIEW_DESCRIPTION = 'Final Preview of all Steps';
    const PREVIEW_FORM_INFO_TEXT = 'Review information below and click to change';

    /**
     * ICONS
     * */
    const ICON_NEXT = '<i class="formwizard-arrow-right-alt1-ico"></i>';
    const ICON_PREV = '<i class="formwizard-arrow-left-alt1-ico"></i>';
    const ICON_FINISH = '<i class="formwizard-check-alt-ico"></i>';
    const ICON_ADD = '<i class="formwizard-plus-ico"></i>';
    const ICON_RESTORE = '<i class="formwizard-restore-ico"></i>';
    const ICON_HEADING = '<i class="formwizard-quill-ico"></i>';

    /**
     * STEP TYPES
     * */
    const STEP_TYPE_DEFAULT = 'default';
    const STEP_TYPE_TABULAR = 'tabular';
    const STEP_TYPE_PREVIEW = 'preview';

    const ROWS_UNLIMITED = '-1';

    /**
     * MESSAGE CONSTANT
     */
    const MSG_TABULAR_CONSTRAINT = 'You cannot have multiple models in a step when the "type" property is set to "tabular", you must provide only a single model or remove the step "type" property.';
    const MSG_EMPTY_STEP = 'You must provide steps for the form.';
    const MSG_INVALID_FORM_ID = 'You must provide the id for the form that matches any word character (equal to [a-zA-Z0-9_])';

    /**
     * THEMES
     * */
    const THEME_DEFAULT = 'default';
    const THEME_DOTS = 'dots';
    const THEME_ARROWS = 'arrows';
    const THEME_CIRCLES = 'circles';
    const THEME_MATERIAL = 'material';
    const THEME_MATERIAL_V = 'material-v';
    const THEME_TAGS = 'tags';

    /**
     * Supported themes for the Widget, default value used is `default`.
     *
     * @var array
     */
    protected $themesSupported = [
        self::THEME_DOTS => 'Dots',
        self::THEME_CIRCLES => 'Circles',
        self::THEME_ARROWS => 'Arrows',
        self::THEME_MATERIAL => 'Material',
        self::THEME_MATERIAL_V => 'MaterialVerticle',
        self::THEME_TAGS => 'Tags',
    ];

    /**
     * Initializes the plugin
     *
     * @return null
     */
    public function init()
    {
        parent::init();
        $this->_setDefaults();
    }

    /**
     * Sets the defaults for the widget and detects to
     * use which version of Bootstrap.
     *
     * @return null
     * @throws ArgException
     */
    private function _setDefaults()
    {
        if ($this->isEmptySteps()) {
            throw new ArgException(self::MSG_EMPTY_STEP);
        }

        //set the form id for the form if not set by the user
        if (!$this->isFormIdSet()) {
            $this->formOptions['id'] = $this->getId() . '_form_wizard';
        } else {
            $formId = $this->formOptions['id'];
            preg_match('/\b(?<valid_form_id>\w+)\b/', $formId, $matches);

            if ($matches['valid_form_id'] !== $formId) {
                throw new ArgException(
                    self::MSG_INVALID_FORM_ID
                );
            }
        }

        //widget container ID
        if (!$this->isContainerIdSet()) {
            $this->wizardContainerId = $this->getId() . '-form_wizard_container';
        }

        //theme buttons material
        if ($this->isThemeMaterial()) {
            $this->classNext .= 'waves-effect';
            $this->classPrev .= 'waves-effect';
            $this->classFinish .= 'waves-effect';
        }

        //force bootstrap version usage
        if ($this->forceBsVersion) {
            $this->_bsVersion = $this->forceBsVersion;
            return;
        }

        //is bs4 version
        $isBs4 = class_exists(BS4ActiveForm::class);
        $this->_bsVersion = !$isBs4 ? self::BS_3 : self::BS_4;
    }

    /**
     * Retrives the plugin default options to be initiazed with
     *
     * @return array $options
     */
    public function getPluginOptions()
    {
        return [
            'selected' => 0,
            'keyNavigation' => false,
            'autoAdjustHeight' => $this->autoAdjustHeight,
            'disabledSteps' => $this->disabledSteps,
            'errorSteps' => $this->errorSteps,
            'backButtonSupport' => false,
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
                'anchorClickable' => $this->editMode,
                'enableAllAnchors' => $this->editMode,
                'markDoneStep' => $this->markDoneStep,
                'markAllPreviousStepsAsDone' => $this->markAllPreviousStepsAsDone,
                'removeDoneStepOnNavigateBack' => $this->removeDoneStepOnNavigateBack,
                'enableAnchorOnDoneStep' => $this->enableAnchorOnDoneStep,
            ],
        ];
    }

    /**
     * Runs the widget.
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function run()
    {
        parent::run();

        //get the plugin options
        $pluginOptions = $this->getPluginOptions();

        //get the persistence option for js use when creating buttons in toolbar
        $jsOptionsPersistence = Json::encode($this->enablePersistence);

        //create custom buttons for the navigation
        $jsButton = <<< JS
        $.formwizard.helper.appendButtons({
            form:'#{$this->formOptions["id"]}',
            labelNext:'{$this->labelNext}',
            labelPrev:'{$this->labelPrev}',
            labelFinish:'{$this->labelFinish}',
            labelRestore:'{$this->labelRestore}',
            iconNext:'{$this->iconNext}',
            iconPrev:'{$this->iconPrev}',
            iconFinish:'{$this->iconFinish}',
            iconRestore:'{$this->iconRestore}',
            classNext:'{$this->classNext}',
            classPrev:'{$this->classPrev}',
            classFinish:'{$this->classFinish}',
            classRestore:'{$this->classRestore}',
            enablePersistence:{$jsOptionsPersistence},

        }).concat({$pluginOptions['toolbarSettings']['toolbarExtraButtons']})
JS;

        //add buttons to the smartwizard plugin toolbar option
        $pluginOptions['toolbarSettings']['toolbarExtraButtons'] = new JsExpression($jsButton);

        //cerate the form
        $this->createForm();

        //register the assets and rutime script
        $this->registerScripts($pluginOptions, $jsOptionsPersistence);
    }

    /**
     * Creates the form for the form wizard
     *
     * @return null
     */
    public function createForm()
    {
        //get the container id
        $wizardContainerId = $this->wizardContainerId;

        //load respective bootstrap assets
        if ($this->isBs3()) {
            $activeForm = BS3ActiveForm::class;
        } else {
            $activeForm = BS4ActiveForm::class;
        }

        //start ActiveForm tag
        $this->_form = $activeForm::begin($this->formOptions);

        //start container tag
        echo Html::beginTag('div', ['id' => $wizardContainerId]);

        //draw form steps
        echo $this->createFormWizard();

        //end container div tag
        echo Html::endTag('div');

        //end form tag
        $this->_form->end();
    }

    /**
     * Creates the form wizard
     *
     * @return HTML
     */
    public function createFormWizard()
    {
        //get the steps
        $steps = $this->steps;

        //start tabs html
        $htmlTabs = Html::beginTag('ul');

        //start Body steps html
        $htmlSteps = Html::beginTag('div');

        //add preview step config if enabled
        if ($this->enablePreview) {
            $steps = array_merge(
                $steps,
                [
                    [
                        'type' => self::STEP_TYPE_PREVIEW,
                        'title' => self::PREVIEW_TITLE,
                        'description' => self::PREVIEW_DESCRIPTION,
                        'formInfoText' => self::PREVIEW_FORM_INFO_TEXT,
                    ],
                ]
            );
        }

        //loop thorugh all the steps
        foreach ($steps as $index => $step) {

            //create wizard steps
            list($tabs, $steps) = $this->createStep($index, $step);

            //tabs html
            $htmlTabs .= $tabs;

            //steps html
            $htmlSteps .= $steps;

            //get preview headings for Javascript
            $this->_previewHeadings[] = ArrayHelper::getValue($step, 'previewHeading', '');

        }

        //end tabs html
        $htmlTabs .= Html::endTag('ul');

        //end steps html
        $htmlSteps .= Html::endTag('div');

        $content = $htmlTabs . $htmlSteps;

        //return form wizard html
        return $content;
    }

    /**
     * Creates the single step in the form wizard
     *
     * @param int   $index index of the current step
     * @param array $step  config for the current step
     *
     * @return array
     */
    public function createStep($index, array $step)
    {
        //step title
        $stepTitle = ArrayHelper::getValue($step, 'title', 'Step-' . ($index + 1));

        //step description
        $stepDescription = ArrayHelper::getValue($step, 'description', self::DEFAULT_STEP_DESCRIPTION);

        //form body info text
        $formInfoText = ArrayHelper::getValue($step, 'formInfoText', self::DEFAULT_FORM_INFO_TEXT);

        //get html tabs
        $htmlTabs = $this->createTabs($index, $stepDescription, $stepTitle);

        //get html body
        $htmlBody = $this->createBody($index, $formInfoText, $step);

        //return html
        return [$htmlTabs, $htmlBody];
    }

    /**
     * Creates the tabs for the formwizard
     *
     * @param int    $index           index of the current step
     * @param string $stepDescription description text for the tab
     * @param string $stepTitle       step title to be displayed inside the tab
     *
     * @return HTML
     */
    public function createTabs($index, $stepDescription, $stepTitle)
    {
        $html = '';

        //add edit mode class for material theme to make all anchors visible
        $isEdit = !$this->editMode ?: 'edit';

        //make tabs
        $html .= Html::beginTag('li', ['class' => "{$isEdit}"]);
        $html .= Html::beginTag('a', ['href' => '#step-' . $index]);
        $html .= $stepTitle . '<br />';
        $html .= Html::tag('small', $stepDescription);
        $html .= Html::endTag('a');
        $html .= Html::endTag('li');

        return $html;
    }

    /**
     * Create the body for the Step
     *
     * @param int    $index        index of the current step
     * @param string $formInfoText description text for the form displayed
     *                             on top of the fields
     * @param array  $step         the config for the current step
     *
     * @return HTML $html
     */
    public function createBody($index, $formInfoText, array $step)
    {
        $html = '';

        //get the step type
        $stepType = ArrayHelper::getValue($step, 'type', self::STEP_TYPE_DEFAULT);

        $isSkipable = ArrayHelper::getValue($step, 'isSkipable', false);

        //check if tabular step
        $isTabularStep = $this->isTabularStep($stepType);

        //tabular rows limit
        $limitRows = ArrayHelper::getValue($step, 'limitRows', self::ROWS_UNLIMITED);

        //check if tabular step
        $isTabularStep && $this->_checkTabularConstraints($step['model']);

        //step data
        $dataStep = [
            'number' => $index,
            'type' => $stepType,
            'skipable' => $isSkipable,
        ];

        //start step wrapper div
        $html .= Html::beginTag(
            'div',
            ['id' => 'step-' . $index, 'data' => ['step' => Json::encode($dataStep)]]
        );

        $html .= Html::tag('div', $formInfoText, ['class' => 'border-bottom border-gray pb-2']);

        //Add Row Buton to add fields dynamically
        if ($isTabularStep) {

            $html .= Html::button(
                $this->iconAdd . '&nbsp;Add',
                [
                    'class' => $this->classAdd . (($this->_bsVersion == self::BS_3) ? ' pull-right add_row' : ' float-right add_row'),
                ]
            );
        }

        //check if not preview step and add fields container
        if (!$this->isPreviewStep($step)) {

            //start field container tag <div class="fields_container">
            $html .= Html::beginTag('div', ["class" => "fields_container", 'data' => ['rows-limit' => $limitRows]]);
            //create step fields
            $html .= $this->createStepFields($index, $step, $isTabularStep, $limitRows);
        }

        //close the field container tag </div>
        $html .= Html::endTag('div');

        //close the step div </div>
        $html .= Html::endTag('div');
        return $html;
    }

    /**
     * Creates the fields for the current step
     *
     * @param int     $stepIndex     index of the current step
     * @param array   $stepConfig          config for the current step
     * @param boolean $isTabularStep if the current step is tabular or not
     * @param int     $limitRows     the rows limit for the tabular step
     *
     * @return HTML
     */
    public function createStepFields($stepIndex, array $stepConfig, $isTabularStep, $limitRows)
    {
        //create step generator object
        $stepGenerator = Yii::createObject(
            [
                'class' => Generator::class,
                'form' => $this->_form,
                'formOptions' => $this->formOptions,
                'stepConfig' => $stepConfig,
                'stepIndex' => $stepIndex,
                'isTabular' => $isTabularStep,
                'limit' => $limitRows,
            ]
        );

        //draw the step
        $response = $stepGenerator->draw();

        //parse response
        $this->_dependentInputScript .= $response->dependentInputJs;
        $this->_persistenceEvents .= $response->persistenceJs;
        $this->_tabularEventJs .= $response->tabularEventsJs;
        $this->_allFields[$stepIndex] = $response->jsFields;

        //return the html
        return $response->html;
    }

    /**
     * Registers the necessary AssetBundles for the widget
     *
     * @param array   $pluginOptions        the plugin options initialized for the runtime
     * @param string  $jsOptionsPersistence the json string for the persistence option
     *
     * @return null
     */
    public function registerScripts(array $pluginOptions, $jsOptionsPersistence)
    {
        //get the container id
        $wizardContainerId = $this->wizardContainerId;

        //get the view
        $view = $this->getView();

        //register theme specific files
        $themeSelected = $this->theme;

        //register plugin assets
        $this->isBs3()
            ?
        Bs3Assets::register($view)
            : Bs4Assets::register($view);

        //is supported theme
        if (in_array($themeSelected, array_keys($this->themesSupported))) {
            $themeAsset = __NAMESPACE__ . '\\assetbundles\\bs' .
            $this->_bsVersion . '\\Theme' .
            $this->themesSupported[$themeSelected] . 'Asset';

            $themeAsset::register($view);
        }

        //get current view object
        $view = $this->getView();

        //get all fields json for javascript processing
        $fieldsJSON = Json::encode($this->_allFields);

        //preview headings
        $headingsJSON = Json::encode($this->_previewHeadings);

        //encode plugin options
        $pluginOptionsJson = Json::encode($pluginOptions);
        $previewEmptyText = $this->previewEmptyText;

        //register inline js
        //add tabular events call back js
        $js = $this->_tabularEventJs;
        $js .= $this->_persistenceEvents;
        $js .= $this->_dependentInputScript;

        //init script for the wizard

        $js .= <<<JS

        //start observer for the smart wizard to run the script
        //when the child HTMl elements are populated
        //necessary for material themes and the button
        //events for tabular row
        $.formwizard.observer.start('#{$wizardContainerId}');

        // Step show event
        $.formwizard.helper.updateButtons('#{$wizardContainerId}');

        // Smart Wizard
        $('#{$wizardContainerId}').smartWizard({$pluginOptionsJson});

        //bind Yii ActiveForm event afterValidate to check
        //only current steps fields for validation and allow to next step
        if($('#{$this->formOptions["id"]}').yiiActiveForm('data').attributes.length){
            $.formwizard.formValidation.bindAfterValidate('#{$this->formOptions["id"]}');
        }

        //fields list
        $.formwizard.fields.{$this->formOptions['id']}={$fieldsJSON};
        $.formwizard.previewHeadings={$headingsJSON};
        $.formwizard.previewEmptyText="{$previewEmptyText}";

        $.formwizard.options.{$this->formOptions['id']}={
            wizardContainerId:'{$wizardContainerId}',
            classAddRow:'{$this->classAdd}',
            labelNext:'{$this->labelNext}',
            labelPrev:'{$this->labelPrev}',
            labelFinish:'{$this->labelFinish}',
            iconNext:'{$this->iconNext}',
            iconPrev:'{$this->iconPrev}',
            iconFinish:'{$this->iconFinish}',
            iconAdd:'{$this->iconAdd}',
            classNext:'{$this->classNext}',
            classPrev:'{$this->classPrev}',
            classFinish:'{$this->classFinish}',
            enablePreview:'{$this->enablePreview}',
            editMode:'{$this->editMode}',
            bsVersion:'{$this->_bsVersion}',
            classListGroup:'{$this->classListGroup}',
            classListGroupHeading:'{$this->classListGroupHeading}',
            classListGroupItem:'{$this->classListGroupItem}',
            classListGroupBadge:'{$this->classListGroupBadge}'
        };

        //init the data persistence if enabled

        if(true =={$jsOptionsPersistence}){
            $.formwizard.persistence.init('{$this->formOptions["id"]}');
        }
JS;
        //register script
        $view->registerJs($js, View::POS_READY);
    }
}
