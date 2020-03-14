<?php
//@codingStandardsIgnoreStart
namespace buttflattery\formwizard\step;

use Yii;

/**
 *
 */
class Generator
{
    //@codingStandardsIgnoreEnd

    /**
     * @var mixed
     */
    public $limit;

    /**
     * @var mixed
     */
    public $stepConfig;

    /**
     * @var mixed
     */
    public $isTabular;

    /**
     * @var mixed
     */
    public $stepIndex;

    /**
     * @var mixed
     */
    public $form;

    /**
     * @var mixed
     */
    public $formOptions;

    /**
     * @return mixed
     */
    public function draw()
    {
        //is array of models
        $isArrayOfModels = is_array($this->stepConfig['model']);

        //check if models
        $models = !$isArrayOfModels ? [$this->stepConfig['model']] : $this->stepConfig['model'];

        //check if tabular step
        if ($this->isTabular) {
            return $this->createTabularStep($models);
        }

        //return for normal step
        return $this->createStep($models);
    }

    /**
     * Creates a tabular step
     *
     * @param array $models the models used for the step
     *
     * @return StepResponse
     */
    public function createTabularStep($models)
    {
        //tabular step object
        $step = Yii::createObject(
            [
                'class' => Tabular::class,
                'models' => $models,
                'stepConfig' => $this->stepConfig,
                'limit' => $this->limit,
                'form' => $this->form,
                'formOptions' => $this->formOptions,
            ]
        );

        //get the step html
        $html = $step->create();

        //populate response object
        $response = Yii::createObject(
            [
                'class' => Response::class,
                'html' => $html,
                'tabularEventsJs' => $step->getTabularEventJs(),
                'persistenceJs' => $step->getPersistenceEvents(),
                'dependentInputJs' => $step->getDependentInputScript(),
            ],
            [$models, $this->stepConfig]
        );

        return $response;
    }

    /**
     * Creates a normal step
     *
     * @param array $models array of models for the current step
     *
     * @return StepResponse
     */
    public function createStep($models)
    {
        //create a step object
        $step = Yii::createObject(
            [
                'class' => Normal::class,
                'models' => $models,
                'stepConfig' => $this->stepConfig,
                'form' => $this->form,
                'formOptions' => $this->formOptions,
            ]
        );

        //get the step html
        $html = $step->create();

        //populate the response object
        $response = Yii::createObject(
            [
                'class' => Response::class,
                'html' => $html,
                'persistenceJs' => $step->getPersistenceEvents(),
                'dependentInputJs' => $step->getDependentInputScript(),
            ],
            [$models, $this->stepConfig]
        );

        //return response
        return $response;
    }

}