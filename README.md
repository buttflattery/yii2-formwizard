# Yii2-FormWizard (v1.0)

### What is this repository for?

A Yii2 plugin used for creating stepped form or form wizard using `yii\widgets\ActiveForm` and `\yii\db\ActiveRecord`, it uses [smart wizard library](https://github.com/mstratman/jQuery-Smart-Wizard) for creating the form interface that uses 3 builtin and 2 extra themes, moreover you can also create your own customized theme too.

**_Note : It uses limited features of the jquery plugin SmartWizard that suite the needs of the ActiveForm validation so not all options in the javascript plugin library are allowed to be changed or customized from within this plugin._**

![preview](https://yii2plugins.idowstech.com/theme/assets/img/form-wizard.jpg)

### External Libraries Used

- [Smart Wizard](https://github.com/mstratman/jQuery-Smart-Wizard).
- [jQuery v2.2.4](https://jquery.com/download/)
- [Bootstrap v3.3.7](https://getbootstrap.com/docs/3.3/) && [Bootstrap v4](http://getbootstrap.com/)

### UPDATE: About Bootstrap Version Usage

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
- `formOptions (array)`: specify the [ActiveForm](https://www.yiiframework.com/doc/api/2.0/yii-widgets-activeform) properties.
- `labelNext` : Next button label, default value `Next`.
- `labelPrev` : Previous button label, default value `Previous`.
- `labelFinish` : Finish button label, default value `Finish`.
- `classNext` : css classes for the button Next, default `btn btn-info`.
- `classPrev` : css classes for the button Previous, default `btn btn-info`.
- `classFinish` : css classes for the button Finish, default `btn btn-success`.
- `steps (array)` : An array of the steps(`array`), the steps can have models dedicated to each step, or a single model for all steps. Following options are recognized when specifying a step.

  - `model (object | array of models)` : The `\yii\model\ActiveRecord` model object or array of models to create step fields.
  - `title (string)` : The title of the step to be displayed inside the step Tab.
  - `description (string)` : The short description for the step.
  - `formInfoText (text)` : The text to be displayed on the top of the form fields inside the step.
  - `fieldOrder (array)` : The default order of the fields in the steps, if specified then the fields will be populated according to the order of the fields in the array, if not then the fields will be ordered according to the order in the `fieldConfig` option, and if `fieldConfig` option is not specified then the default order in which the attributes are returned from the model will be used.
  - `fieldConfig (array)` : This option is used mainly to customize the form fields for the step. 3 options are recognized inside the `fieldConfig`, 2 of them are `except` and `only`. See below for the details

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

### Who do I talk to?

- buttflattery@hotmail.com
- omeraslam@idowstech.com
