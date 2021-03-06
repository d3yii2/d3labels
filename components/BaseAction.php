<?php

namespace d3yii2\d3labels\components;

use Yii;
use yii\base\Action;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

/**
 * Class BaseAction
 * @package d3yii2\d3labels\components
 * @property ActiveRecord|string $modelName
 * @property string $view
 * @property array $viewParams
 * @property object $model
 * @property string $returnURL
 */
class BaseAction extends Action
{
    /** @var string */
    public $modelName;

    /** @var array */
    public $returnUrl;

    /** @var ActiveRecord */
    protected $model;


    /**
     * @param int $id
     * @throws NotFoundHttpException
     */
    protected function loadModel(int $id): void
    {
        if(method_exists($this->controller,'findModel')){
            $this->model = $this->controller->findModel($id);
        }else{
            $this->model = $this->modelName::findOne($id);
        }
        if (!$this->model) {
            throw new NotFoundHttpException('The requested model does not exist: ' . $this->modelName);
        }
    }

    /**
     * Redirect to return URL
     * @return Yii\web\Response
     */
    protected function redirect(): yii\web\Response
    {
        return $this->controller->redirect(Yii::$app->request->referrer);
    }

}