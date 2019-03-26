<?php

namespace d3yii2\d3labels\widgets;

use d3system\widgets\ThBadge;
use d3yii2\d3labels\logic\D3LabelList as LabelLogic;
use Yii;
use yii\helpers\Html;

/**
 * Class D3LabelList
 * @package d3yii2\d3labels\widgets
 * @property object $model
 * @property string $title
 * @property string $titleDescription
 * @property array $titleHtmlOptions
 * @property bool $collapsed
 * @property array $tableOptions
 * @property bool $headerIconsWithText
 * @property bool $gridIconsWithText
 * @property string $returnURLToken
 * @property \d3yii2\d3labels\logic\D3LabelList $d3LabelList
 * @property string $controllerRoute $controllerRoute
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
    public $headerIconsWithText = true;
    public $gridIconsWithText = false;
    public $returnURLToken;
    public $returnURL;

    private $d3LabelList;
    private $controllerRoute;

    /**
     * @return bool|void
     */
    public function init()
    {
        parent::init();

        $this->d3LabelList = new LabelLogic($this->model, $this->returnURLToken);

        if (!$this->title) {
            $this->title = Yii::t('d3labels', 'Labels');
        }

        if (!$this->controllerRoute) {
            $this->controllerRoute = Yii::$app->controller->id;
        }
    }

    /**
     * Render the table with available labels for the model
     * @return string
     * @throws \Exception
     */
    public function run(): string
    {
        $modulePath = Yii::$app->getModule('d3labels')->basePath;

        return $this->renderFile(
            $modulePath . '/views/label/_attached-list.php',
            [
                'title' => $this->createTitle(),
                'collapsedHtml' => '',
                'table' => $this->createTable(),
                'tableOptions' => $this->tableOptions,
            ]
        );
    }

    /**
     * Get the Header content for Labels table
     * @return string
     * @throws \Exception
     */
    public function createTitle(): string
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

        $nonAttachedLabels = $this->d3LabelList->getNonAttached();

        if ($nonAttachedLabels) {
            $items = LabelLogic::getBadgeItems($nonAttachedLabels, 'd3labelsattach', $this->model->id);

            $renderOptions = $this->headerIconsWithText ? ['iconsWithText' => true] : [];
            $content .= LabelLogic::getAsBadges($items, $renderOptions);
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

    /**
     * Get the Labels table content
     * @return string
     * @throws \Exception
     */
    public function createTable(): string
    {
        $html = '
        <tbody>
        ';

        $available = $this->d3LabelList->getAvailable();
        $attached = $this->d3LabelList->getAttached();

        foreach ($attached as $definitionId => $row) {

            if (!isset($available[$definitionId])) {
                continue;
            }

            $label = $available[$definitionId];

            $params = [
                'd3labelsremove',
                'labelId' => $row->id,
                'modelId' => $this->d3LabelList->model->id,
            ];

           // $params['returnURL'] = Yii::$app->urlManager->createAbsoluteUrl(Yii::$app->request);

            $actions = Html::a(
                '<span class="glyphicon glyphicon-trash"></span>',
                $params,
                ['title' => Yii::t('d3labels', 'Remove')]
            );

            $label = ThBadge::widget(
                [
                    'type' => $label->collor,
                    'text' => $label->label,
                    'faIcon' => $label->icon,
                    'showText' => $this->gridIconsWithText,
                ]
            );

            $html .= '
                <tr>
                    <td>' . $label . '</td>
                    <td>' . $actions . '</td>
                </tr>';
        }

        return $html . '</tbody>';
    }
}