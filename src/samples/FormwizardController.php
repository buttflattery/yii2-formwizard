<?php

namespace buttflattery\formwizard\samples;

use yii\web\Controller;
use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class FormwizardController extends Controller {

    public function actionIndex() {
        $shootModel = new \app\models\Shoots();
        $shootTagModel = new \app\models\ShootTag();
        $userModel = new \app\models\User();

        //for ajax validation 
        if( Yii::$app->request->isAjax && Yii::$app->request->isPost ){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validateMultiple([$shootModel, $shootTagModel, $userModel], Yii::$app->request->post());
        }

        if( Yii::$app->request->isPost ){
            echo "<pre>";
            print_r(Yii::$app->request->post());
            var_dump(\yii\base\Model::loadMultiple([$shootModel, $shootTagModel, $userModel], Yii::$app->request->post()));
            echo "here";
            exit;
        }
        return $this->render('index');
    }

    public function actionUser() {
        $model = new \app\models\User();

        if( $model->load(Yii::$app->request->post()) && $model->save() ){
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->renderAjax('/user/create', [
                    'model' => $model,
        ]);
    }

    public function actionTest() {
        return $this->render('test');
    }

}
