<?php

use d3system\widgets\ThBadge;
use d3yii2\d3labels\components\D3LabelAccess;
use yii\helpers\Html;

/**
 * @var int $systemModelId
 * @var array $labels
 * @var bool $showCode
 */


?>
<table class="table">
    <thead>
    <tr>
        <th scope="col"><?= Yii::t('d3labels', 'Label') ?></th>
        <th scope="col"><?= Yii::t('d3labels', 'Icon') ?></th>
        <th scope="col"><?= Yii::t('d3labels', 'Color') ?></th>
        <?php if($showCode): ?>
            <th scope="col"><?= Yii::t('d3labels', 'Code') ?></th>
        <?php endif; ?>
        <th scope="col"><?= Yii::t('d3labels', 'Look') ?></th>
        <th scope="col"><?= Yii::t('d3labels', 'Action') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($labels as $defRow): ?>
        <tr>
            <td><b><?= $defRow['label'] ?></b></td>
            <td><?= ThBadge::widget(
                    [
                        'type' => $defRow['collor'],
                        'faIcon' => $defRow['icon'],
                        'showText' => true,
                    ])  ?></td>
            <td><?= $defRow['collor'] ?></td>
            <?php if($showCode): ?>
                <td><?= $defRow['code'] ?></td>
            <?php endif; ?>
            <td><?= ThBadge::widget(
                    [
                        'type' => $defRow['collor'],
                        'text' => $defRow['label'],
                        'faIcon' => $defRow['icon'],
                        'showText' => true,
                    ]) ?></td>
            <td>

                <?php
                if($defRow['sys_company_id']){ ?>
                    <?= Html::a(
                        '<span class="glyphicon glyphicon-pencil"></span>',
                        'javascript:void(0)',
                        [
                            'class' => 'edit-item',
                            'title' => Yii::t('crud', 'Edit'),
                            'data-link' => [
                                'definitionId' => $defRow['id'],
                                'modelId' => $systemModelId,
                            ],
                            'style' => 'margin-right:15px;'
                        ]
                    )
                    ?><?= Html::a(
                    '<span class="glyphicon glyphicon-trash"></span>',
                    [
                        D3LabelAccess::ACTION_DEFINITION_DELETE,
                        'definitionId' => $defRow['id'],
                        'modelId' => $systemModelId,
                    ],
                    [
                        'class' => 'delete-item',
                        'title' => Yii::t('d3labels', 'Remove')
                    ]
                ) ?>
                    <?php
                }
                ?></td>
        </tr>
    <?php
    endforeach;
    ?>
    </tbody>
</table>
