<?php

namespace d3yii2\d3labels\components;

use yii\base\Action;
use yii\web\NotFoundHttpException;

/**
 * Class D3LabelAction
 * @package d3yii2\d3labels\components
 */
class D3LabelAction extends Action
{
    public $modelName;
    public $view;

    protected $model;

    /**
     * Set the JSON response format on Action init if the View not specified
     */
    public function init()
    {
        if (!$this->view) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        parent::init();
    }

    /**
     * @param $id
     */
    protected function loadModel($id)
    {
        if (!$this->model = $this->modelName::findOne($id)) {
            throw new NotFoundHttpException(Yii::t('d3files',
                'The requested model does not exist: ' . $this->modelName));
        }
    }

    /**
     * Load the View from current controller instance
     * @return string
     */
    protected function loadView()
    {
        if ($this->view) {
            return $this->controller->render($this->view, [
                'model' => $this->model,
            ]);
        }
    }
}