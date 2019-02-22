<?php

namespace d3yii2\d3labels\controllers;

use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3labels\logic\D3Definition;
use d3yii2\d3labels\logic\D3Label;
use d3yii2\d3labels\logic\Label;
use d3yii2\d3labels\models\D3lLabel;
use eaBlankonThema\components\FlashHelper;
use unyii2\yii2panel\Controller;
use yii\filters\AccessControl;
use Exception;
use Yii;

class LabelController extends Controller
{
    const STATE_SUCCESS = 'success';
    const STATE_FAILED = 'failed';

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
                            'list',
                            'attach',
                            'remove',
                        ],
                        /*'roles' => [
                            '',
                        ],*/
                    ],
                ],
            ],
        ];
    }

    /**
     * @param int $defId
     * @param int $recordId
     * @return false|string
     */
    public function actionAttach(int $defId, int $recordId)
    {
        try {
            $def = D3Definition::loadDefinition($defId);

            //@FIXME - $recordId - jāizdomā, kā noteikt vai modeļa ieraksts pastāv

            D3Label::attach($recordId, [$def]);

            $msg = Yii::t('d3labels', 'Label attached  sucessfully');

            $this->redirectIfRu(self::STATE_SUCCESS, $msg);

            return json_encode(['state' => self::STATE_SUCCESS, 'msg' => $msg]);
        } catch (Exception $err) {

            $this->redirectIfRu(self::STATE_FAILED, $err->getMessage());

            return json_encode(['state' => self::STATE_FAILED, 'msg' => $err->getMessage()]);
        }
    }

    /**
     * @param $state
     * @param $msg
     * @return \yii\web\Response
     */
    public function redirectIfRu(string $state, string $msg)
    {
        $returnUrl = Yii::$app->request->get('ru');

        if ($returnUrl) {
            if (self::STATE_SUCCESS === $state) {
                FlashHelper::addSuccess($msg);
            } else {
                FlashHelper::addDanger($msg);
            }
            return $this->redirect($returnUrl);
        }
    }

    /**
     * @param int $defId
     * @param int $recordId
     * @return false|string
     * @throws \Throwable
     */
    public function actionRemove(int $id)
    {
        try {

            $label = D3Label::loadLabel($id);

            if (!$label->delete()) {
                throw new D3ActiveRecordException($label, Yii::t('d3labels', 'Cannot delete Label record'));
            }

            $msg = Yii::t('d3labels', 'Label removed sucessfully');

            $this->redirectIfRu(self::STATE_SUCCESS, $msg);

            return json_encode(['state' => self::STATE_SUCCESS, 'msg' => $msg]);
        } catch (Exception $err) {

            $this->redirectIfRu(self::STATE_FAILED, $err->getMessage());

            return json_encode(['state' => self::STATE_FAILED, 'msg' => $err->getMessage()]);
        }
    }
}