<?php

use ea\themeBs5\widget\ThAlertList;
?>
<?= ThAlertList::widget() ?>
<?= $this->render('_form', ['model' => $model, 'showCode' => $showCode]) ?>
