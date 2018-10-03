<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use app\models\Shoots;
use app\models\ShootTag;
use buttflattery\formwizard\FormWizard;
use kartik\select2\Select2;
use yii\jui\DatePicker;

$js = <<< JS
    var mybutton = $( '<button id="new"></button>' ).text( 'New Button' )
    .addClass( 'btn btn-info' ).on("click",function(e){
        e.preventDefault();
        alert("hello");
    });
JS;
$this->registerJs($js, yii\web\View::POS_READY);

echo FormWizard::widget([
    'id' => 'my-steps',
    //add your custom buttons
    'toolbarExtraButtons' => new \yii\web\JsExpression('[mybutton]'),
    'theme' => 'dots',
    'formOptions' => [
        'id' => 'my-form',
//        'enableClientValidation'=>true,
        //        'enableAjaxValidation'=>true,
    ],
    'steps' => [
        [
            'model' => Shoots::class,
            'title' => 'Shoots',
            'description' => 'Enter the shoots',
            'fieldConfig' => [
                'disabled' => ['created_at', 'updated_at'],
                'shoot_type' => [
                    'options' => [
                        'prompt' => 'select a value',
                        'value' => ['modeling', 'products'],
                    ],
                ],
                'active' => [
                    'widget' => Select2::class,
                    'options' => [
                        'data' => [0 => 'No', 1 => 'Yes'],
                        'options' => [
                            'class' => 'form-control',
                        ],
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'pluginOptions' => [
                            'allowClear' => true,
                            'placeholder' => 'Select Status',
                        ],
                    ],
                ],
//                'created_at' => [
                //                    'widget' => DatePicker::class,
                //                    'options' => [
                //                        'options' => [
                //                            'placeholder'=>'Select a Date',
                //                        ]
                //                    ],
                //
                //                ],
            ],
        ],
        [
            'model' => Shoots::class,
            'title' => 'Shoots Extra',
            'fieldConfig' => [
                'only' => ['created_at', 'updated_at'],
                'created_at' => [
                    'widget' => DatePicker::class,
                    'options' => [
                        'options' => [
                            'placeholder' => 'Select a Date',
                            'class' => 'form-control',
                        ],
                    ],
                ],
            ],
        ],
        [
            'model' => ShootTag::class,
            'title' => 'Step 2',
        ],
        [
            'model' => \app\models\User::class,
            'title' => 'Step 3',
            'fieldConfig' => [
                'created_at' => false,
            ],
        ],
    ],
]);
//echo DatePicker::widget([
//                        'model' => new Shoots(),
//                        'attribute' => 'updated_at',
//                    ]);
//echo Select2::widget([
//    'name'=>'test',
//    'data' => [0 => 'No', 1 => 'Yes'],
//    'options' => [
//        'class' => 'form-control',
//    ],
//    'theme' => Select2::THEME_BOOTSTRAP,
//    'pluginOptions' => [
//        'allowClear' => true,
//        'placeholder' => 'Select Status',
//    ],
//]);
