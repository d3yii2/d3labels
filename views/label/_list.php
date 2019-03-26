<?php

use yii\helpers\Html;
use \d3system\widgets\ThBadge;
?>
<table class="table">
    <thead>
    <tr>
        <th scope="col"><?= Yii::t('d3labels', 'Label') ?></th>
        <th scope="col"><?= Yii::t('d3labels', 'Icon') ?></th>
        <th scope="col"><?= Yii::t('d3labels', 'Collor') ?></th>
        <th scope="col"><?= Yii::t('d3labels', 'Look') ?></th>
        <th scope="col"><?= Yii::t('blankonthema', 'Action') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($labels as $definitionId => $def): ?>
        <tr>
            <td><b><?= $def->label ?></b></td>
            <td><?= $def->icon ?></td>
            <td><?= $def->collor ?></td>
            <td><?= ThBadge::widget(
                    [
                        'type' => $def->collor,
                        'text' => $def->label,
                        'faIcon' => $def->icon,
                        'showText' => true,
                    ]) ?></td>
            <td><?= Html::a(
                    '<span class="glyphicon glyphicon-trash"></span>',
                    [
                        'd3labelsdefinitionremove',
                        'definitionId' => $def->id,
                        'modelId' => $systemModelId,
                    ],
                    [
                            'class' => 'delete-item',
                            'title' => Yii::t('d3labels', 'Remove')
                    ]
                ) ?></td>
        </tr>
    <?php
    endforeach;
    ?>
    </tbody>
</table>