<?php

use d3yii2\d3labels\models\D3Note;
use eaArgonTheme\widget\ThButton;
use eaArgonTheme\widget\ThPanel;

/**
 * @var bool $canEdit
 * @var array $addButtonLink
 * @var array $removeButtonLink
 * @var D3Note[] $attached
 */

$headerHtml = '';
if ($canEdit && $addButtonLink) {
    $headerHtml .= ThButton::widget([
        'label' => Yii::t('d3labels', 'Add'),
        'link' =>$addButtonLink,
        'icon' => ThButton::ICON_PLUS,
        'type' => ThButton::TYPE_PRIMARY,
        'size' => ThButton::SIZE_SMALL
    ]);
}
$headerHtml .= $title ?? Yii::t('d3labels', 'Notes');
$bodyHtml = '';
foreach ($attached as $note) {
    $panelConfig = [
        'header' => trim($note->time . ' ' . ($note->userName ?? '')),
        'body' => $note->notes
    ];
    if ($canEdit && $removeButtonLink) {
        $removeButtonLink['modelId'] = $note->model_record_id;
        $removeButtonLink['noteId'] = $note->id;
        $panelConfig['rightIcon'] = ThButton::ICON_REMOVE;
        $panelConfig['rightIconUrl'] = $removeButtonLink;
        $panelConfig['rightIconType'] = ThButton::TYPE_DANGER;
        $panelConfig['rightIconUrl']['noteId'] = $note->id;
    }
    $bodyHtml .= ThPanel::widget($panelConfig);
}
echo ThPanel::widget([
    'type' => ThPanel::TYPE_DEFAULT,
    'header' => $headerHtml,
    'body' => $bodyHtml,
]);
