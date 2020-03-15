<?php
namespace buttflattery\formwizard\step;

use yii\base\Model;
use yii\helpers\ArrayHelper;

class Sorter
{
    /**
     * @var mixed
     */
    public $models;

    /**
     * @var mixed
     */
    public $stepConfig;

    /**
     * Sorts the attributes of the models
     * 
     * @return mixed
     */
    public function sort()
    {

        $mappedFields = [];

        //field configurations
        $fieldConfig = ArrayHelper::getValue($this->stepConfig, 'fieldConfig', false);

        //disabled fields
        $disabledFields = ArrayHelper::getValue($fieldConfig, 'except', []);

        //only fields
        $onlyFields = ArrayHelper::getValue($fieldConfig, 'only', []);

        //iterate models
        foreach ($this->models as $model) {

            //get safe attributes
            $attributes = $this->getStepFields($model, $onlyFields, $disabledFields);

            //field order
            foreach ($attributes as $attribute) {
                $mappedFields[] = ['model' => $model, 'attribute' => $attribute];
            }
        }

        return $mappedFields;
    }
    
    /**
     * Sorts the fields. If the `fieldOrder` option is specified then the
     * order will be dependend on the order specified in the `fieldOrder`
     * array. If not provided the order will be according to the order of
     * the fields specified under the `fieldConfig` option, and if none of
     * the above is given then it will fallback to the order in which they
     * are retrieved from the model.
     *
     * @param array $attributes  the attributes reference for the model
     *
     * @return null
     */
    public function sortFields(array &$attributes)
    {
        //field configurations
        $fieldConfig = ArrayHelper::getValue($this->stepConfig, 'fieldConfig', false);

        $defaultOrder = $fieldConfig !== false ? array_keys($fieldConfig) : false;
        $fieldOrder = ArrayHelper::getValue($this->stepConfig, 'fieldOrder', $defaultOrder);

        if ($fieldOrder) {
            $orderedAttributes = [];
            $unorderedAttributes = [];

            foreach ($attributes as $item) {
                $attribute = isset($item['attribute']) ? $item['attribute'] : $item;
                $moveToIndex = array_search($attribute, $fieldOrder);

                if ($moveToIndex !== false) {
                    $orderedAttributes[$moveToIndex] = $item;
                    continue;
                }
                $unorderedAttributes[] = $item;
            }

            //sort new order according to keys
            ksort($orderedAttributes);

            //merge array with unordered attributes
            $attributes = array_merge($orderedAttributes, $unorderedAttributes);
        }
    }

    /**
     * Filters the step fields for the `except` and `only` options if mentioned
     *
     * @param object $model          instance of the model dedicated for the step
     * @param array  $onlyFields     the field to be populated only
     * @param array  $disabledFields the fields to be ignored
     *
     * @return array $fields
     */
    public function getStepFields(Model $model, array $onlyFields = [], array $disabledFields = [])
    {
        //return $onlyFields list
        if (!empty($onlyFields)) {
            return array_values(
                array_filter(
                    array_keys($model->getAttributes($model->safeAttributes())),
                    function ($item) use ($onlyFields) {
                        return in_array($item, $onlyFields);
                    }
                )
            );
        }

        //return all fields list for the model
        return array_filter(
            array_keys($model->getAttributes($model->safeAttributes())),
            function ($item) use ($disabledFields) {
                return !in_array($item, $disabledFields);
            }
        );
    }
}
