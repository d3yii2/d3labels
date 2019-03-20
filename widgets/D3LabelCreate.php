<?php

namespace d3yii2\d3labels\widgets;

use d3system\widgets\ThBadge;
use d3yii2\d3labels\logic\D3Definition;
use d3yii2\d3labels\models\D3lDefinition;
use d3yii2\d3labels\models\D3lLabelForm;
use Yii;
use yii\helpers\Html;

/**
 * Class D3LabelCreate
 * @package d3yii2\d3labels\widgets
 * @property object $model
 * @property yii\web\Controller
 * @property string $returnURLToken
 */
class D3LabelCreate extends \yii\base\Widget
{
    public $modelClass;
    public $controller;
    public $returnURLToken;

    private $definition;

    /**
     * @return bool|void
     */
    public function init()
    {
        parent::init();

        $this->definition = new D3Definition($this->modelClass);
    }


    /**
     * @return string
     */
    public function run(): string
    {
        $modulePath = Yii::$app->getModule('d3labels')->basePath;

        $formModel = new D3lLabelForm();

        $formModel->modelClass = $this->modelClass;

        $formModel->labels[] = new D3lDefinition();

        return $this->renderFile(
            $modulePath . '/views/label/_create.php',
            [
                'model' => $formModel,
                'controller' => $this->controller,
                'returnURLToken' => $this->returnURLToken,
                'labelsList' => $this->renderFile(
                    $modulePath . '/views/label/list.php',
                    [
                        'title' => Yii::t('d3labels', 'Existing Labels'),
                        'collapsedHtml' => '',
                        'table' => $this->getTable(),
                        'tableOptions' => [],
                    ]
                )
            ]
        );
    }

    /**
     * Get the Labels table content
     * @return string
     * @throws \Exception
     */
    public function getTable(): string
    {
        $html = '
        <tbody>
        ';

        $labels = $this->definition->getAllByModel();

        $systemModelId = $this->definition->getSystemModelId();

        foreach ($labels as $definitionId => $def) {

            $params = [
                'd3labelsdefinitionremove',
                'definitionId' => $def->id,
                'modelId' => $systemModelId,
            ];

            $actions = Html::a(
                '<span class="glyphicon glyphicon-trash"></span>',
                $params,
                ['title' => Yii::t('d3labels', 'Remove')]
            );

            $label = ThBadge::widget(
                [
                    'type' => $def->collor,
                    'text' => $def->label,
                    'faIcon' => $def->icon,
                    'showText' => true,
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