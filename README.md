# Yii2-FormWizard (v1.4.6)

### What is this repository for?

A Yii2 plugin used for creating stepped form or form wizard using `yii\widgets\ActiveForm` and `\yii\db\ActiveRecord`, it uses [smart wizard library](https://github.com/mstratman/jQuery-Smart-Wizard) for creating the form interface that uses 3 builtin and 2 extra themes, moreover you can also create your own customized theme too.

**_Note : It uses limited features of the jquery plugin SmartWizard that suite the needs of the ActiveForm validation so not all options in the javascript plugin library are allowed to be changed or customized from within this plugin._**

![preview](https://yii2plugins.idowstech.com/theme/assets/img/form-wizard.jpg)

### External Libraries Used

- [Smart Wizard](https://github.com/mstratman/jQuery-Smart-Wizard).
- [jQuery>= v2.2.4](https://jquery.com/download/)
- [Bootstrap v3.3.7](https://getbootstrap.com/docs/3.3/) && [Bootstrap v4](http://getbootstrap.com/)

---

### New Features Added

#### 24th April, 2019 : [Group Step Fields](https://github.com/buttflattery/yii2-formwizard/wiki/Group-Step-Fields-using-Heading)

#### 2nd April, 2019 : [Enable Persistence](https://github.com/buttflattery/yii2-formwizard/wiki/Form-Persistence)

#### 25th March, 2019 : [Preview Step](https://github.com/buttflattery/yii2-formwizard/wiki/Enable-Preview-Step), [Tabular Step](<https://github.com/buttflattery/yii2-formwizard/wiki/Tabular-Steps-(New-Feature)>)

---

### About Bootstrap Version Usage

The extension detects if you are using the `yiisoft/yii2-bootstrap` or `yiisoft/yii2-bootstrap4` and loads the appropriate assets for the extension. It will check first the `"yiisoft/yii2-bootstrap4"` if it exists then it will load bootstrap4 resources otherwise it will fall back to use bootstrap3. So make sure you are following the correct guide to use the [`yiisoft/yii2-bootstrap4"`](https://github.com/yiisoft/yii2-bootstrap4) and remove the `"yiisoft/yii2-bootstrap": "~2.0.0",` from you `composer.json` and change the `minimum-stability:"dev"` here is the complete [guide](https://github.com/yiisoft/yii2-bootstrap4/blob/master/docs/guide/README.md).

### How do I get set up?

use composer to install the extension

```
php composer.phar require  buttflattery/yii2-formwizard "@dev"
```

or add into the `composer.json` file under `require` section

```
"buttflattery/yii2-formwizard":"@dev"
```

### Demos

See all [demos](http://yii2plugins.idowstech.com/formwizard/index) with all options.

### Available Options

#### Widget options

- `wizardContainerId (string)`: Id of the main container for the wizard.
- `formOptions (array)`: Specify the [ActiveForm](https://www.yiiframework.com/doc/api/2.0/yii-widgets-activeform) properties.
- `labelNext (string)` : Next button label, default value `Next`.
- `labelPrev (string)` : Previous button label, default value `Previous`.
- `labelFinish (string)` : Finish button label, default value `Finish`.
- `classNext (string)` : Css classes for the button Next, default `btn btn-info`.
- `iconNext (string)` : The html string for the Next button, defaults to `<i class="formwizard-arrow-right-alt1-ico"></i>`.
- `classPrev (string)` : Css classes for the button Previous, default `btn btn-info`.
- `iconPrev (string)` : The html string for the prev icon, defaults to `<i class="formwizard-arrow-left-alt1-ico"></i>`.
- `classFinish (string)` : Css classes for the button Finish, default `btn btn-success`.
- `iconFinish (string)` : The Html string for the icon, defaults to `<i class="formwizard-check-alt-ico"></i>`.
- `classAdd (string)` : Css class for Add Row Button default to `btn btn-info`
- `iconAdd (string)` : The html string for the button default to `<i class="formwizard-plus-ico"></i>`
- `enablePreview (boolean)` : Adds a Preview Step as the last step in the form wizard where you can preview all the entered inputs grouped by steps, clicking on any step will navigate to that step for quick edit, it defaults to `false`. [See Wiki For Code Samples](https://github.com/buttflattery/yii2-formwizard/wiki/Enable-Preview-Step)

  When using `'enablePreview'=>true` you can customize the classes using the below options.

  - `classListGroup (string)` : Css class for the list group defaults to `'list-group'`.
  - `classListGroupHeading (string)` : Css class for the list group heading element, defaults to `'list-group-heading'`.
  - `classListGroupItem (string)` : Css class for the list group item, defaults to `'list-group-item-success'`.
  - `classListGroupBadge (string)` : Css class for the list group badge that displays the input label, defaults to `'success'`.

- `enablePersistence (boolean)` : Enables to save and restore an un-saved form to the local storage for later use, defaults to `false`.

- `steps (array)` : An array of the steps(`array`), the steps can have models dedicated to each step, or a single model for all steps. Following options are recognized when specifying a step.

  - `type (string)` : The type of the step, defaults to `default`. This option is used if you need to have tabular inputs for the step by specifying the type as `tabular`, you can use the provided constants like `FormWizard::STEP_TYPE_TABULAR` or `FormWizard::STEP_TYPE_DEFAULT`.
  - `limitRows (int)` : The number of rows to limit the tabular step Add Rows functionality in combination with the `FormWizard::STEP_TYPE_TABULAR`, default is unlimited `-1`. [See Wiki](https://github.com/buttflattery/yii2-formwizard/wiki/Tabular-Steps:-Limiting-Rows).
  - `model (object | array of models)` : The `\yii\model\ActiveRecord` model object or array of models to create step fields.

    **Note: After the addition of the feature Tabular Steps when using `'type'=>'tabular'` you must remember that you cannot provide different models, although you can provide multiple instances when in edit mode but for the same model only.**

    While adding a new record you can have like below

    ```
    $addressModel = new Address();

    "steps"=>[
      [
        'title'=>'Step Title',
        'type' => FormWizard::STEP_TYPE_TABULAR,
        'model'=> $addressModel
      ]
    ]
    ```

    When in Edit Mode you can provide the multiple instances of `Address`

    ```
    //either using model directly
      $addressModel = Address::find()
          ->where(
            ['=','user_id',Yii::$app->user->id]
          )->all();

      //or using the model relation if you have `getAddress()` defined inside
      //the `User` model. `$user` has the selected user in code below
      $addressModel = $user->address;

    "steps"=>[
      [
          'title'=>'Step Title',
          'type' => FormWizard::STEP_TYPE_TABULAR,
          'model'=> $addressModel
      ]
    ]

    ```
    [See wiki For Tabular Step Code Sample](https://github.com/buttflattery/yii2-formwizard/wiki/Tabular-Steps-(New-Feature))

  * `title (string)` : The title of the step to be displayed inside the step Tab.
  * `description (string)` : The short description for the step.
  * `formInfoText (text)` : The text to be displayed on the top of the form fields inside the step.
  * `fieldOrder (array)` : The default order of the fields in the steps, if specified then the fields will be populated according to the order of the fields in the array, if not then the fields will be ordered according to the order in the `fieldConfig` option, and if `fieldConfig` option is not specified then the default order in which the attributes are returned from the model will be used.
  * `stepHeadings (array)` : takes a collection of arrays to group step fields under the headings, it accepts arrays with the following keys. [Wiki Code Sample](https://github.com/buttflattery/yii2-formwizard/wiki/Group-Step-Fields-using-Heading)
  
        - `text (string)` the text to be displayed as the heading.
        - `before (string)` the field before which you want the heading to appear.
        - `icon (string)` the markup for the icon like `<i class="fa-user"></i>`or the image tag with url you want to display, it defaults to
        `<i class="formwizard-quill-ico"></i>`. If you dont wish to use an icon you can pass `false`.

  * `fieldConfig (array)` : This option is used mainly to customize the form fields for the step. 3 options are recognized inside the `fieldConfig`, 2 of them are `except` and `only`. See below for the details

  - `except (array)` : List of fields that should not be populated in the step or should be ignored, for example

    ```

    'fieldConfig'=>[
        'except'=>[
            'created_on','updated_on'
        ]
    ]

    ```

    By default all the attributes that are safe to load value are populated, and the `id` or `primary_key` is ignored by default.

  - `only (array)` : list of the fields that should be populated for the step, only the fields specified in the list will be available and all other fields will be ignored.

    Apart from the above options the `fieldConfig` recognizes some special options specific to every field separately when customizing a field, for example

    ```

    'fieldConfig'=>[
        'username'=>[
            'options'=>[
                'class'=>'my-class'
            ]
        ]
    ]

    ```

    you should specify the field name of the model and its customization settings in form of `name=>value` pairs. The following special options can be used when specifying the form/model `field_name`.

    - `options`
    - `containerOptions`
    - `inputOptions`
    - `template`
    - `labelOptions`
    - `widget`
    - `multifield (boolean)` 
    - `hint`
    - `tabularEvents` (used with the tabular steps)
    - `persistenceEvents` (effective with `enablePersistence`) [See Wiki](https://github.com/buttflattery/yii2-formwizard/wiki/Form-Persistence)
     [See Wiki For Customizing Fields using options above](https://github.com/buttflattery/yii2-formwizard/wiki/Customizing-form-fields)
    Details

    - `options (array)` : You can specify the HTML attributes (name-value pairs) for the field

      ```
      'field_name'=>['options'=>['class'=>'my-class']]`

      ```

      All those special options that are recognized by the

      - `checkbox(), radio()` : `uncheck`, `label`, `labelOptions`
      - `checkboxList(), radioList()` : `tag`, `unselect`, `encode`, `separator`, `itemOptions`, `item`.

      can be used with-in the `options` option. The following 2 options are specially recognized by this widget when used with-in the `options`.

      - `type (string)`: The type of the form field to be created, this can be `text`, `dropdown`,`checkbox`, `radio`, `textarea`, `file`, `password`, `hidden`. Default value for this option is `text`.

      - `itemsList (string/array)` : This option can be used with a `dropdown`, `checkboxList` or `radioList`. It is used in combination of the the option `type`. If you provide the `itemsList` an array and use the `'type'=>'checkbox'` , it will call `checkboxList()`, and a `checkbox()` if you provide string, same goes for the radioList and radio.

    - `lableOptions (array)`: The HTML and special options for customizing the field label, you can use the following settings
      - `label (string)`: The label text.
      - `options (array)` : The HTML attributes (name-value pairs) for the label.
    - `template (string)` : The template used for the field the default value used is `{label}\n{input}\n{hint}\n{error}`.
    - `containerOptions (array)` : HTML atrtibutes for the cotnainer tag used as `name=>value` pairs.
    - `widget` : This option can be used if you want to use a widget instead of the the default fields, you can specify the widget class name `'widget'=>widget::class`, and the options for the widget will be provided in the `options` option. -`inputOptions (array)` : this is same as the `inputOptions` used by the ActiveForm `field()` method.
    - `multifield (boolean)` : a boolean which decides if the field name should consist of an array or not, for example using multi file upload widgets require the `name` attribute for the field to be declared as an array like `filed_name[]` instead of `field_name`. you can pass this option as true by default it is false.
    - `hint (string)` : it is used to provide a hint text for the field if you dont provide a custom hint text for any field it will attempt to show the custom hints that are provided inside the model by overriding the `attributeHints()`, otherwise it wont show any hint.
    - `tabularEvents (array)`: it takes an array as an argument with the following values `beforeClone`, `afterClone` and `afterInsert` in the form `"eventName"=>"function(event){}"`, [see](<https://github.com/buttflattery/yii2-formwizard/wiki/Tabular-Steps-(New-Feature)#working-with-thrid-party-widgets>) for details
      - `beforeClone` : Takes a callback `function(event){}` used for the PRE processing of the source element before it is cloned, using `$(this)` inside the function callback referes to the element in the first rows always.
      - `afterClone` : Takes a callback `function(event){}` used for the POST processing of the source element before it is cloned, using`\$(this)` inside the function callback referes to the element in the first rows always.
      - `afterInsert`: Takes a callback `function(event, params){console.log(params)}` used for the POST processing of the newly added element after it is cloned, using `$(this)` inside the function callback referes to the newly added row.the `params` is json object which holds the `rowIndex`.
    - `persistenceEvents (array)` : it accepts an array of events with the following name. (currently only `afterRestore` is supported).

      - `afteRestore` : take a callback as string `"function(event,params){}"` to be called for post-restore operations, it provides 2 parameters `event` and `params` where params is a JSON `{fieldId: "field_name",fieldValue: "field_value"}`.

#### Widget Plugin (SmartWizard) Options

Only the following options of the plugin SmartWizard are allowed to be customized

- `theme` : name of the theme to be used, there are mainly 6 themes supported by the plugin
  - `default` : `const THEME_DEFAULT`
  - `dots` : `const THEME_DOTS`
  - `arrows` : `const THEME_ARROWS`
  - `circles` : `const THEME_CIRCLES`
  - `material` : `const THEME_MATERIAL`
  - `material-v` : `const THEME_MATERIAL_V`
- `transitionEffect (string)` : The effect used when sliding the step it can be one of the
  - `none`
  - `slide`
  - `fade`
- `showStepURLhash (boolean)` : Show url hash based on step, default `false`.
- `useURLHash (boolean)` : Enable selection of the step based on url hash, default value is `false`.
- `toolbarPosition` : Position of the toolbar (`none, top, bottom, both`), default value `top`.
- `toolbarButtonPosition`: Position of the toolbar buttons (left, right), default value is left.
- `toolbarExtraButtons` : Specify the extra buttons and its events to show on toolbar.
- `markDoneSteps (boolean)` : Make already visited steps as done, default value is `true`.
- `markAllPreviousStepsAsDone (boolean)`: When a step selected by url hash, all previous steps are marked done, default value is `true`.
- `removeDoneStepOnNavigateBack (boolean)` : While navigate back done step after active step will be cleared, default value is `false`.
- `enableAnchorOnDoneStep (boolean)` : Enable/Disable the done steps navigation, default value is `true`.

### Widget Constants

- Icons

  - `FormWizard::ICON_NEXT` defaults to `'<i class="formwizard-arrow-right-alt1-ico"></i>'`.
  - `FormWizard::ICON_PREV` defaults to `'<i class="formwizard-arrow-left-alt1-ico"></i>'`.
  - `FormWizard::ICON_FINISH` defaults to `'<i class="formwizard-check-alt-ico"></i>'`.
  - `FormWizard::ICON_ADD` defaults to `'<i class="formwizard-plus-ico"></i>'`.
  - `FormWizard::ICON_RESTORE` defaults to `'<i class="formwizard-restore-ico"></i>'`.

- Step Types

  - `FormWizard::STEP_TYPE_DEFAULT` defaults to `'default'`.
  - `FormWizard::STEP_TYPE_TABULAR` default to `'tabular'`.
  - `FormWizard::STEP_TYPE_PREVIEW` default to `'preview'`.

- Themes
  - `FormWizard::THEME_DEFAULT` defaults to `'default'`.
  - `FormWizard::THEME_DOTS` defaults to `'dots'`.
  - `FormWizard::THEME_ARROWS` defaults to `'arrows'`.
  - `FormWizard::THEME_CIRCLES` defaults to `'circles'`.
  - `FormWizard::THEME_MATERIAL` defaults to `'material'`.
  - `FormWizard::THEME_MATERIAL_V` defaults to `'material-v'`.
  - `FormWizard::THEME_TAGS` defaults to `'tags'`.

### Who do I talk to?

- buttflattery@hotmail.com
- omeraslam@idowstech.com
