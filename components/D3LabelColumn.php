<?php

namespace d3yii2\d3labels\components;

use d3yii2\d3labels\logic\D3DefinitionList;
use d3yii2\d3labels\models\D3lLabel;
use yii\grid\DataColumn;

/**
 * Class D3LabelColumn
 * @package d3yii2\d3labels\components
 */
class D3LabelColumn extends DataColumn
{
    public $model;

    private $dataProviderIds = [];

    private $recordsWithLabels = [];

    /**
     * @param $model
     * @param $key
     * @param $index
     * @return string
     * @throws \Exception
     */
    public function renderDataCellContent($model, $key, $index): string
    {
        if (empty($this->recordsWithLabels[$model->id])) {
            return '';
        }

        $labelItems = \d3yii2\d3labels\logic\D3LabelList::getBadgeItems($this->recordsWithLabels[$model->id]);

        $labelsList = \d3yii2\d3labels\logic\D3LabelList::getAsBadges($labelItems);

        return $labelsList;
    }

    /**
     * Renders the filter cell content.
     * The default implementation simply renders a space.
     * This method may be overridden to customize the rendering of the filter cell (if any).
     * @return string the rendering result
     */
    protected function renderFilterCellContent(): string
    {
        if (empty($this->dataProviderIds)) {
            $this->initLabels();
        }

        $items = D3lLabel::forListbox($this->dataProviderIds);

        $dropdown = \d3yii2\d3labels\logic\D3LabelList::getAsDropdown($items, $this->model);

        return $dropdown;
    }


    private function initLabels()
    {
        $rows = $this->grid->dataProvider->getModels();

        foreach ($rows as $row) {
            $this->dataProviderIds[] = $row->id;
        }

        $recordsWithLabels = D3lLabel::getAllByModelRecordIds($this->dataProviderIds);

        foreach ($recordsWithLabels as $labelModel) {
            if (!isset($this->recordsWithLabels[$labelModel['model_record_id']])) {
                $this->recordsWithLabels[$labelModel['model_record_id']] = [];
            }

            $this->recordsWithLabels[$labelModel['model_record_id']][$labelModel['definition_id']] = $labelModel;
        }
    }
}