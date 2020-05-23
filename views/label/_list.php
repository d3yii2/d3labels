<?php
/**
 * @var int $systemModelId
 */

use d3system\widgets\ThBadge;
use d3yii2\d3labels\components\D3LabelAccess;
use yii\helpers\Html;

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
    foreach ($labels as $defRow): ?>
        <tr>
            <td><b><?= $defRow['label'] ?></b></td>
            <td><?= $defRow['icon'] ?></td>
            <td><?= $defRow['collor'] ?></td>
            <td><?= ThBadge::widget(
                    [
                        'type' => $defRow['collor'],
                        'text' => $defRow['label'],
                        'faIcon' => $defRow['icon'],
                        'showText' => true,
                    ]) ?></td>
            <td><?= Html::a(
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
                ) ?></td>
        </tr>
    <?php
    endforeach;
    ?>
    </tbody>
</table>
