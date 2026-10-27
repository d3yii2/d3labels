<?php

use cornernote\returnurl\ReturnUrl;
use d3modules\d3invoices\components\InvInvoiceRef;
use eaBlankonThema\widget\ThExternalLink;
use eaBlankonThema\widget\ThPanel;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var string|null $title
 * @var InvInvoiceRef[] $modelRefs
 */

$ru = ReturnUrl::getToken();

if (!$modelRefs) {
    $bodyHtml = Html::tag(
        'p',
        Html::encode(Yii::t('d3labels', 'No references')),
        ['class' => 'text-muted no-margin']
    );
} else {
    $rows = [];
    foreach ($modelRefs as $modelRef) {
        $rows[] = Html::tag(
            'tr',
            Html::tag(
                'td',
                $modelRef->createIcon() . ' ' . Html::encode($modelRef->createTypeLabel()),
                ['class' => 'text-muted', 'style' => 'width: 1%; white-space: nowrap;']
            )
            . Html::tag(
                'td',
                ThExternalLink::widget([
                    'text' => $modelRef->createLabel(),
                    'url' => $modelRef->createUrl($ru),
                ])
            )
        );
    }
    $bodyHtml = Html::tag(
        'table',
        Html::tag('tbody', implode(PHP_EOL, $rows)),
        ['class' => 'table table-condensed table-hover no-margin']
    );
}

echo ThPanel::widget([
    'type' => ThPanel::TYPE_DEFAULT,
    'leftIcon' => 'external-link',
    'header' => Html::encode($title) . ' ' . Html::tag('span', count($modelRefs), ['class' => 'badge']),
    'body' => $bodyHtml,
]);
