<?php

use ea\themeBs5\widget\Icon;
use yii\helpers\Html;

$colors = ['success' => 'success', 'info' => 'info', 'warning' => 'warning', 'danger' => 'danger',
    'primary' => 'primary', 'lilac' => 'purple', 'inverse' => 'dark', 'teals' => 'teal'];
?>
<?= Html::tag('span', Icon::tag($faIcon ?? null) . ' ' . Html::encode($text ?? ''), [
    'class' => 'badge bg-' . ($colors[$type ?? 'default'] ?? 'secondary') . '-lt',
]) ?>
