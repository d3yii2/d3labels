<?php

namespace d3yii2\d3labels\components;

use d3yii2\d3labels\logic\D3Label;
use eaBlankonThema\components\FlashHelper;
use Exception;
use Yii;

/**
 * Class DeleteAction
 * Deletes an existing Label attached to Model
 * @package d3yii2\d3labels\components
 */
class DeleteAction extends BaseAction
{

    /** @var string[] define access fo label deleting ['closed' => ['roleName1','roleName2']  */
    public $labelAccessRoles = [];

    /**
     * @param int $modelId
     * @param int $labelId
     * @param int|null $userId
     * @return Yii\web\Response
     */
    public function run(int $modelId, int $labelId, int $userId = null): yii\web\Response
    {
        if (!$label = D3Label::getAttachedLabel($modelId, $labelId, $userId)) {
            FlashHelper::addInfo(Yii::t('d3labels', 'Can not find attached label'));
            return $this->redirect();
        }
        if ($this->labelAccessRoles && isset($this->labelAccessRoles[$label->definition->code])) {
            $hasAccess = false;
            foreach ($this->labelAccessRoles[$label->definition->code]??[] as $roleName) {
                if (Yii::$app->user->can($roleName)) {
                    $hasAccess = true;
                    break;
                }
            }
            if (!$hasAccess) {
                FlashHelper::addDanger(Yii::t(
                    'd3labels',
                    'You do not have rights for removing label "{labelName}"',
                    ['labelName' => $label->definition->label]
                ));
                return $this->redirect();
            }
        }
        try {
            $this->loadModel($modelId);
            $label->delete();
            FlashHelper::addSuccess(Yii::t('d3labels', 'Label removed successfully'));
        } catch (Exception $err) {
            FlashHelper::addDanger($err->getMessage());
        }

        return $this->redirect();
    }
}