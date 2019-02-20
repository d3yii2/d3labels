<?php

namespace d3yii2\d3labels\controllers;

use d3yii2\d3labels\logic\Label;
use unyii2\yii2panel\Controller;
use yii\filters\AccessControl;

class LabelPanelController extends Controller
{

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
                            'index',
                        ],
                        /*'roles' => [
                            '',
                        ],*/
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $labels = Label::getAll();

        return $this->render('index', ['labels' => $labels]);
    }
}