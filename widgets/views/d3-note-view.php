<?php

use d3yii2\d3labels\models\D3Note;
use eaBlankonThema\widget\ThButton;
use eaBlankonThema\widget\ThPanel;

/**
 * @var bool $canEdit
 * @var array $addButtonLink
 * @var D3Note[] $attached
 */

$headerHtml = '';
if ($canEdit && $addButtonLink) {
    $headerHtml .= ThButton::widget([
        'label' => Yii::t('d3labels', 'Add'),
        'link' =>$addButtonLink,
        'icon' => ThButton::ICON_PLUS,
        'type' => ThButton::TYPE_SUCCESS,
        'size' => ThButton::SIZE_SMALL
    ]);
}
$headerHtml .= $title ?? Yii::t('d3labels', 'Notes');
$bodyHtml = '';
foreach ($attached as $note) {
    $bodyHtml .= ThPanel::widget([
        'header' => trim($note->time . ' ' . ($note->userName??'')),
        'body' => $note->notes
    ]);
}
echo ThPanel::widget([
    'type' => ThPanel::TYPE_DEFAULT,
    'header' => $headerHtml,
    'body' => $bodyHtml,
]);
