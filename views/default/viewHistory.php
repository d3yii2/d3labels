<?php

use d3system\yii2\web\D3SystemView;
use eaBlankonThema\widget\ThGridView;
use d3system\widgets\ThBadge;
use d3yii2\d3labels\models\D3lLabelHistory;
use yii2d3\d3persons\dictionaries\UserDictionary;


/**
 * @var D3SystemView $this
 * @var D3lLabelHistory $models
 */

?>

<div class="row" style="padding: 0px 10px;">
    <?= ThGridView::widget([
        'dataProvider' => $models,
        'actionColumnTemplate' => false,
        'tableOptions' => [
            'class' => 'table table-success dataTable table-striped floatThead-table fileList'
        ],
        'columns' => [
            [
                'attribute' => 'label_name',
                'format' => 'raw',
                'value' => static function (D3lLabelHistory $model) {
                    return ThBadge::widget(['type' => $model->definition->collor, 'text' => $model->definition->label]);
                }
            ],
            'action',
            'time',
            [
                'attribute' => 'user',
                'format' => 'raw',
                'value' => static function (D3lLabelHistory $model) {
                    return UserDictionary::getList()[$model->user_id] ?? 'N/A';
                }
            ]
        ]
    ]) ?>
</div>
