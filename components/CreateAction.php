<?php

namespace d3yii2\d3labels\components;

use d3yii2\d3labels\logic\D3Definition;
use eaBlankonThema\components\FlashHelper;
use Exception;
use Yii;

/**
 * Class CreateAction
 * @package d3yii2\d3labels\components
 * @property object $model
 */
class CreateAction extends BaseAction
{

    public $sysCompanyId;
    /**
     * @return Yii\web\Response
     */
    public function run(): yii\web\Response
    {
        try {

            $def = new D3Definition($this->modelName);

            $def->loadFromForm();
            if($this->sysCompanyId ) {
                if (is_callable($this->sysCompanyId)) {
                    $def->setCompanyId(call_user_func($this->sysCompanyId));
                } else {
                    $def->setCompanyId($this->sysCompanyId);
                }
            }
            $def->save();

            $msg = Yii::t('d3labels', 'Labels created successfully');
            FlashHelper::addSuccess($msg);
        } catch (Exception $err) {
            FlashHelper::addDanger($err->getMessage());
        }

        return $this->redirect();
    }
}