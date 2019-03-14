<?php

namespace d3yii2\d3labels\components;

use cornernote\returnurl\ReturnUrl;
use d3system\exceptions\D3Exception;
use Yii;
use yii\base\Action;
use yii\web\NotFoundHttpException;

/**
 * Class BaseAction
 * @package d3yii2\d3labels\components
 * @property string $modelName
 * @property string $view
 * @property array $viewParams
 * @property object $model
 * @property string $returnURL
 */
class BaseAction extends Action
{
    public $modelName;
    public $view;
    public $viewParams = [];

    protected $model;
    protected $returnURL;

    /**
     * Set the return URL if exists
     */
    public function init()
    {
        $this->returnURL = ReturnUrl::getUrl();

        parent::init();
    }

    /**
     * @param int $id
     * @throws NotFoundHttpException
     */
    protected function loadModel(int $id)
    {
        if (!$this->model = $this->modelName::findOne($id)) {
            throw new NotFoundHttpException(Yii::t('d3files',
                'The requested model does not exist: ' . $this->modelName));
        }
    }

    /**
     * Redirect to return URL
     * @return Yii\web\Response
     * @throws D3Exception
     */
    protected function redirect(): yii\web\Response
    {
        if (!$this->returnURL) {
            throw new D3Exception('Return URL not set');
        }

        return $this->controller->redirect($this->returnURL);
    }

    /**
     * Load the View from current controller instance
     * @return string
     */
    protected function loadView()
    {
        return $this->controller->render($this->view, $this->viewParams);
    }
}