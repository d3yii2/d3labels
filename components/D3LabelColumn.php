<?php

namespace d3yii2\d3labels\components;

use d3system\dictionaries\SysModelsDictionary;
use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3labels\dictionaries\D3lDefinitionDictionary;
use d3yii2\d3labels\logic\D3LabelList;
use d3yii2\d3labels\models\D3lLabel;
use Exception;
use yii\grid\DataColumn;

/**
 * Class D3LabelColumn
 * @package d3yii2\d3labels\components
 * @property object $model
 * @property array $badgeRenderOptions
 * @property array $dataProviderIds
 * @property array $recordsWithLabels
 */
class D3LabelColumn extends DataColumn
{
    public $model;
    public $modelClass;
    public $badgeRenderOptions = [];
    public $filterListboxOptions = [];

    /** @var int */
    public $sysCompanyId;

    private $dataProviderIds = [];
    private $recordsWithLabels = [];

    /**
     * Set the initial properties on class init
     */
    public function init(): void
    {
        $this->initLabels();

        parent::init();
    }

    /**
     * Read all the records containing attached labels into $this->recordsWithLabels array
     */
    private function initLabels(): void
    {
        $rows = $this->grid->dataProvider->getModels();

        foreach ($rows as $row) {
            $this->dataProviderIds[] = $row->id;
        }

        $recordsWithLabels = D3lLabel::getAllByModelRecordIds($this->dataProviderIds, $this->modelClass);

        foreach ($recordsWithLabels as $labelModel) {
            if (!isset($this->recordsWithLabels[$labelModel['model_record_id']])) {
                $this->recordsWithLabels[$labelModel['model_record_id']] = [];
            }

            $this->recordsWithLabels[$labelModel['model_record_id']][$labelModel['definition_id']] = $labelModel;
        }
    }

    /**
     * Render the labels inside grid data cell
     * @param $model
     * @param $key
     * @param $index
     * @return string
     * @throws Exception
     */
    public function renderDataCellContent($model, $key, $index): string
    {
        if (empty($this->recordsWithLabels[$model->id])) {
            return '';
        }

        $labelItems = D3LabelList::getBadgeItems($this->recordsWithLabels[$model->id]);

        return D3LabelList::getAsBadges($labelItems, $this->badgeRenderOptions);
    }

    /**
     * Renders the filter cell content.
     * The default implementation simply renders a space.
     * This method may be overridden to customize the rendering of the filter cell (if any).
     * @return string the rendering result
     */
    protected function renderFilterCellContent(): string
    {
        $items = D3lDefinitionDictionary::getList($this->sysCompanyId, $this->modelClass);
        return D3LabelList::getAsDropdown($items, $this->filterListboxOptions, $this->model);
    }
}