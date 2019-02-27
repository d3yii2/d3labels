<?php

namespace d3yii2\d3labels\widgets;

use cornernote\returnurl\ReturnUrl;
use d3system\models\SysModels;
use d3yii2\d3labels\models\D3lDefinition;
use d3yii2\d3labels\models\D3lLabel;
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
        return '<div class="panel-heading panel-heading-table-simple">
                    <div class="pull-left">
                        ' . Html::tag('h3', $this->title, $titleHtmlOptions) . '
                        ' . $description . '    
                    </div>
                    <div class="pull-right">
                        <button class="btn btn-sm" data-action="collapse" data-toggle="tooltip" data-placement="top" data-title="Collapse" data-original-title="" title="">
                            <i class="fa ' . $collapseIcon . '"></i>
                        </button>
                    </div>                    
                    <div class="clearfix"></div>
                </div>';

    }

    public function createTable()
    {
        $html = '
        <thead>
            <tr>
                <th>' . Yii::t('d3labels', 'Icon') . '</th>
                <th>' . Yii::t('d3labels', 'Title') . '</th>
                <th>' . Yii::t('d3labels', 'Color') . '</th>
                <th>' . Yii::t('d3labels', 'Status') . '</th>
                <th>' . Yii::t('d3labels', 'Action') . '</th>
            </tr>     
        </thead>
        <tbody>
        ';
        foreach ($this->availableLabels as $row) {

            $isAttached = isset($this->attachedLabels[$row->id]);

            $status = $isAttached ? 'Attached' : 'Removed';

            $returnUrl = ReturnUrl::getUrl(Yii::$app->request->getUrl());

            $actions = !$isAttached
                ? ThExternalLink::widget([
                    'text' => Yii::t('d3labels', 'Attach'),
                    'url' => Url::to(
                        [
                            $this->controllerRoute . '/attach-label',
                            'defId' => $row->id,
                            'recordId' => $this->model->id,
                            'modelId' => $this->model->id,
                        ])
                ])
                : ThExternalLink::widget([
                    'text' => Yii::t('d3labels', 'Remove'),
                    'url' => Url::to(
                        [
                            $this->controllerRoute . '/remove-label',
                            'labelId' => $this->attachedLabels[$row->id]->id,
                            'modelId' => $this->model->id,
                        ]
                    )
                ]);

            $html .= '
                <tr>
                    <td>' . $row->icon . '</td>
                    <td>' . $row->label . '</td>
                    <td>' . $row->collor . '</td>
                    <td>' . Yii::t('d3labels', $status) . '</td>
                    <td>' . $actions . '</td>
                </tr>';
        }

        return $html . '</tbody>';
    }
}