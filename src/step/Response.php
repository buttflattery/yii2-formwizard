<?php
namespace buttflattery\formwizard\step;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use buttflattery\formwizard\FormWizard;

class Response
{
    /**
     * @var mixed
     */
    public $html;
    /**
     * @var mixed
     */
    public $dependentInputJs;
    /**
     * @var mixed
     */
    public $persistenceJs;
    /**
     * @var mixed
     */
    public $tabularEventsJs;

    /**
     * @var mixed
     */
    public $jsFields;

    /**
     * @param $models
     */
    public function __construct($models, $stepConfig)
    {
        $this->setJsFields($models, $stepConfig);
    }

    /**
     * @param $models
     */
    public function setJsFields($models, $stepConfig)
    {
        $fields = [];

        //sorter class object
        $sorter = Yii::createObject(Sorter::class);

        //disabled fields
        $disabledFields = ArrayHelper::getValue($stepConfig, 'fieldConfig.except', []);

        //only fields
        $onlyFields = ArrayHelper::getValue($stepConfig, 'fieldConfig.only', []);

        //step type
        $stepType = ArrayHelper::getValue($stepConfig, 'type', FormWizard::STEP_TYPE_DEFAULT);

        //is tabular
        $isTabularStep = $this->isTabularStep($stepType);

        foreach ($models as $modelIndex => $model) {

            //get the fields for the current model
            $attributes = $sorter->getStepFields($model, $onlyFields, $disabledFields);

            //add all the field ids to array
            $fields = array_merge(
                $fields,
                array_map(
                    function ($element) use ($model, $modelIndex, $isTabularStep) {
                        return Html::getInputId($model, ($isTabularStep) ? "[$modelIndex]" . $element : $element);
                    },
                    $attributes
                )
            );
        }
        $this->jsFields = $fields;
    }

    /**
     * @param $stepType
     * @return mixed
     */
    public function isTabularStep($stepType)
    {
        return $stepType === FormWizard::STEP_TYPE_TABULAR;
    }
}
