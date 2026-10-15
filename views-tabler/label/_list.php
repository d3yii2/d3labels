<?php

use ea\themeBs5\widget\Icon;
use d3yii2\d3labels\components\D3LabelAccess;
use yii\helpers\Html;

/**
 * @var int $systemModelId
 * @var array $labels
 * @var bool $showCode
 */


?>
<table class="table table-vcenter card-table">
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
            <td><b><?= Html::encode($defRow['label']) ?></b></td>
            <td><?= $this->render('_badge',
                    [
                        'type' => $defRow['collor'],
                        'faIcon' => $defRow['icon'],
                        'showText' => true,
                    ])  ?></td>
            <td><?= $defRow['collor'] ?></td>
            <?php if($showCode): ?>
                <td><?= Html::encode($defRow['code']) ?></td>
            <?php endif; ?>
            <td><?= $this->render('_badge',
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
                        '<i class="ti ti-pencil"></i>',
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
                    '<i class="ti ti-trash"></i>',
                    [
                        D3LabelAccess::ACTION_DEFINITION_DELETE,
                        'definitionId' => $defRow['id'],
                        'modelId' => $systemModelId,
                    ],
                    [
                        'class' => 'delete-item',
                        'data-confirm' => Yii::t('d3labels', 'Label will be deleted'),
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
