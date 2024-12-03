<?php

use cornernote\returnurl\ReturnUrl;
use d3yii2\d3labels\models\D3Note;
use eaBlankonThema\widget\ThButton;
use eaBlankonThema\widget\ThPanel;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

/**
 * @var bool $canEdit
 * @var array $addButtonLink
 * @var array $removeButtonLink
 * @var D3Note[] $attached
 */
$ru = ReturnUrl::getToken();
$headerHtml = '';
if ($canEdit && $addButtonLink) {
    $headerHtml .= ThButton::widget([
        'label' => Yii::t('d3labels', 'Add'),
        'link' => $addButtonLink,
        'icon' => ThButton::ICON_PLUS,
        'type' => ThButton::TYPE_SUCCESS,
        'size' => ThButton::SIZE_SMALL
    ]);
}

$columns = [
    [
        'attribute' => 'time',
        'format'=>'datetime',
        'contentOptions' => ['style' => 'width:150px; white-space: normal;']
    ],
    [
        'attribute' => 'userName',
        'contentOptions' => ['style' => 'width:150px; white-space: normal;']
    ],
    [
        'attribute' => 'notesAsHtml',
        'label' => Yii::t('d3labels', 'Notes'),
        'format' => 'raw',
        'contentOptions' => ['style' => 'width: 50%;'],
    ]
];

if ($canEdit && $removeButtonLink) {
    $columns[] = [
        'class' => ActionColumn::class,
        'template' => '{delete}',
        'urlCreator' =>
            static function (string $action, D3Note $model) use ($removeButtonLink) {
                $removeButtonLink['modelId'] = $model->model_record_id;
                $removeButtonLink['noteId'] = $model->id;
                return Url::toRoute($removeButtonLink);
            }
    ];
}

$bodyHtml = GridView::widget([
    'layout' => '{items}',
    'export' => false,
    'dataProvider' => new ActiveDataProvider([
        'models' => $attached,
        'pagination' => false
    ]),
    'tableOptions' => [
        'class' => 'table table-striped table-success tbody '
    ],
    'columns' => $columns
]);
$headerHtml .= $title ?? Yii::t('d3labels', 'Notes');

echo ThPanel::widget([
    'type' => ThPanel::TYPE_DEFAULT,
    'header' => $headerHtml,
    'body' => $bodyHtml,
]);
