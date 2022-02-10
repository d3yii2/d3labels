<?php

namespace d3yii2\d3labels\components;

use d3yii2\d3labels\logic\D3Label;
use Exception;
use Yii;
use yii\web\Response;

/**
 * Class AttachAction
 * Attach Label to Model
 * @package d3yii2\d3labels\components
 */
class AjaxAttachAction extends BaseAction
{

    /**
     * @var bool attach/deattach for users labels
     */
    public $userLabels = false;
    /**
     * @param int $defId
     * @param int $modelId
     * @return array
     */
    public function run(int $defId, int $modelId): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!Yii::$app->request->isAjax) {
            return ['error' => 'Must be ajax request'];
        }
        $userId = $this->userLabels?Yii::$app->user->id:null;
        try {
            $this->loadModel($modelId);
            if ($label = D3Label::getAttachedLabel($modelId, $defId, $userId)) {
                $label->delete();
            } else {
                D3Label::attach($modelId, $defId, $userId);
            }
        } catch (Exception $err) {
            Yii::error($err->getMessage() . PHP_EOL . $err->getTraceAsString());
            return ['error' => $err->getMessage()];
        }

        return ['response' => 'ok'];
    }
}