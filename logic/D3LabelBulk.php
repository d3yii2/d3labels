<?php

namespace d3yii2\d3labels\logic;

use d3yii2\d3labels\dictionaries\D3lDefinitionDictionary;
use Yii;
use yii\base\Component;

/**
 * use for maintenance tasks
 *
 * Class D3LabelMaintenance
 * @package d3yii2\d3labels\logic
 */
class D3LabelBulk extends Component
{
    public string $modelClassName;
    public int $sysCompanyId;
    public int $userId;
    public array $ignoreLabelsByCode = [];

    public function list(): array
    {
        $availableLabels = [];
        foreach ($this->getLabelRawList() as $defId => $defLabel) {
            $availableLabels[$this->createListKey($defId)] = $this->createListlabel($defLabel);
        }
        return $availableLabels;
    }

    private function createListKey(int $id): string
    {
        return 'label-' . $id;
    }

    /**
     * @param string $label
     * @return string
     */
    private function createListlabel(string $label): string
    {
        return Yii::t(
            'd3labels',
            'Add label "{label}"',
            ['label' => $label]
        );
    }

    public function isBulkAction(string $action): bool
    {
        return (bool)($this->list()[$action]??false);
    }

    /**
     * @param string $action
     * @param integer[] $selection
     * @return void
     * @throws \yii\web\NotFoundHttpException
     */
    public function processBulkAction(string $action, array $selection): int
    {
        [$prefix,$labelDefId] = explode('-',$action);
        $cnt = 0;
        foreach ($selection as $modelId) {
            D3Label::attach($modelId, $labelDefId, $this->userId);
            $cnt ++;
        }
        return $cnt;
    }

    public function successMessage(string $action,int $cnt): string
    {
        [$prefix,$labelDefId] = explode('-',$action);
        return Yii::t(
            'd3labels',
            'Label "{labelOfLabel}" added to {cnt} records',
            [
                'cnt' => $cnt,
                'labelOfLabel' => $this->getLabelRawList()[$labelDefId]??'??'
            ]
        );
    }

    public function nothingAddedMessage(string $action): string
    {
        [$prefix,$labelDefId] = explode('-',$action);
        return Yii::t(
            'd3labels',
            'Label "{labelOfLabel}" is not attached to any record',
            [
                'labelOfLabel' => $this->getLabelRawList()[$labelDefId]??'??'
            ]
        );
    }

    /**
     * @return array
     * @throws \d3system\exceptions\D3ActiveRecordException
     */
    private function getLabelRawList(): array
    {
        return D3lDefinitionDictionary::getList(
            $this->sysCompanyId,
            $this->modelClassName,
            $this->ignoreLabelsByCode
        );
    }
}
