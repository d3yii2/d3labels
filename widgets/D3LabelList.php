<?php

namespace d3yii2\d3labels\widgets;

use cornernote\returnurl\ReturnUrl;
use d3system\models\SysModels;
use d3yii2\d3labels\models\D3lDefinition;
use d3yii2\d3labels\models\D3lLabel;
use d3yii2\d3labels\widgets\ThBadge;
use eaBlankonThema\widget\ThButton;
use eaBlankonThema\widget\ThExternalLink;
use yii\helpers\Url;
use yii\helpers\Html;
use Yii;

/**
 * D3Label Widget
 */
class D3LabelList extends \yii\base\Widget
{
    public $model;
    public $title;
    public $titleDescription;
    public $titleHtmlOptions = [];
    public $collapsed = false;
    public $tableOptions = [
        'class' => 'table table-striped table-success table-bordered'
    ];
    private $availableLabels = [];
    private $attachedLabels = [];
    private $controllerRoute;

    public function init()
    {
        parent::init();

        $sysModel = SysModels::findOne(['class_name' => get_class($this->model)]);

        if (!$sysModel) {
            return false;
        }

        $definitions = D3lDefinition::findAll(['model_id' => $sysModel->id]);

        if (!empty($definitions)) {
            foreach ($definitions as $def) {
                $this->availableLabels[$def->id] = $def;
            }
        }

        $labels = D3lLabel::findAll(['model_record_id' => $this->model->id]);

        if (!empty($labels)) {
            foreach ($labels as $label) {
                $this->attachedLabels[$label->definition_id] = $label;
            }
        }

        if (!$this->controllerRoute) {
            $this->controllerRoute = Yii::$app->controller->id;
        }
    }

    public function run()
    {
        $modulePath = Yii::$app->getModule('d3labels')->basePath;

        return $this->renderFile(
            $modulePath . '/views/label/list.php',
            [
                'title' => $this->createTitle(),
                'collapsedHtml' => '',
                'table' => $this->createTable(),
                'tableOptions' => $this->tableOptions,
            ]
        );
    }

    public function createTitle()
    {
        if (!$this->title) {
            return '';
        }

        $description = '';
        if ($this->titleDescription) {
            $description = '<p>' . $this->titleDescription . '</p>';
        }
        $titleHtmlOptions = $this->titleHtmlOptions;
        Html::addCssClass($titleHtmlOptions, 'panel-title');

        $collapseIcon = 'fa-angle-up';
        if ($this->collapsed) {
            $collapseIcon = 'fa-angle-down';
        }
        $content = '<div class="panel-heading panel-heading-table-simple">
                    <div class="pull-left">
                        ' . Html::tag('h3', $this->title, $titleHtmlOptions) . '
                        ' . $description . '    
                    </div>
                    <div>
                    <form>';

        $availableLabels = $this->getAvailableForDropdown();

        if($availableLabels) {
            $content .= Html::beginForm([$this->controllerRoute . '/attach-label'], 'get')
                . Html::dropDownList('defId', null, $availableLabels) . ' '
                . Html::hiddenInput('recordId', $this->model->id)
                . Html::hiddenInput('modelId', $this->model->id)
                . ThButton::widget([
                    'label' => Yii::t('d3labels', 'Attach'),
                    'icon' => ThButton::ICON_PLUS,
                    'type' => ThButton::TYPE_SUCCESS,
                    'size' => ThButton::SIZE_SMALL,
                    'submit' => true,
                ]) .
                Html::endForm();
        }

        $content .= '</div>
                    <div class="pull-right">
                        <button class="btn btn-sm" data-action="collapse" data-toggle="tooltip" data-placement="top" data-title="Collapse" data-original-title="" title="">
                            <i class="fa ' . $collapseIcon . '"></i>
                        </button>
                    </div>                    
                    <div class="clearfix"></div>
                </div>';

        return $content;
    }

    public function createTable()
    {
        $html = '
        <tbody>
        ';

        foreach ($this->attachedLabels as $definitionId => $row) {

            if(!isset($this->availableLabels[$definitionId])) {
                continue;
            }

            $label = $this->availableLabels[$definitionId];

            $actions = Html::a(
                '<span class="glyphicon glyphicon-trash"></span>',
                [
                    'remove-label',
                    'labelId' => $row->id,
                    'modelId' => $this->model->id,
                ],
                ['title' => Yii::t('d3labels', 'Remove')]
            );

            $label = ThBadge::widget(['type' => $label->collor, 'text' => $label->label, 'faIcon' => $label->icon]);

            $html .= '
                <tr>
                    <td>' . $label . '</td>
                    <td>' . $actions . '</td>
                </tr>';
        }

        return $html . '</tbody>';
    }

    /**
     * @return array
     */
    public function getAvailableForDropdown(): array
    {
        $items = [];

        foreach ($this->availableLabels as $id => $label) {
            if(!isset($this->attachedLabels[$id])) {
                $items[$id] = $label->label;
            }
        }

        return $items;
    }
}