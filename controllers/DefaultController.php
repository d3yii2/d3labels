<?php

namespace d3yii2\d3labels\controllers;

use d3yii2\d3labels\models\D3lLabelHistory;
use eaBlankonThema\yii2\web\LayoutController;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;

class DefaultController extends LayoutController
{
    /**
    * @var boolean whether to enable CSRF validation for the actions in this controller.
    * CSRF validation is enabled only when both this property and [[Request::enableCsrfValidation]] are true.
    */
    public $enableCsrfValidation = false;

    /**
    * specify route for identifing active menu item
    */
    public $menuRoute = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                [
                    'allow' => true,
                    'actions' => [
                        'view-history',
                    ],
                    'roles' => [
                        '@' //authorised user
                    ],
                ],
              ],
            ],
        ];
    }

    public function actionViewHistory($model_record_id)
    {
        $models = new ActiveDataProvider(
            [
                'models' => D3lLabelHistory::findAll(['model_record_id' => $model_record_id]),
                'pagination' => false
            ]);


        if (\Yii::$app->request->isAjax) {
            return $this->renderAjax('viewHistory', ['models' => $models]);
        }

        return $this->render('viewHistory', ['models' => $models]);
    }


}
