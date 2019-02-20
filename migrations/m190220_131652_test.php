<?php

use yii\db\Migration;

/**
* Class m190220_131652_test*/
class m190220_131652_test extends Migration
{
    /**
    * {@inheritdoc}
    */
    public function safeUp()
    {
        $items = [
            [
                'modelClass' => 'yii2d3\d3emails\models\D3pop3Email',
                'actionClass' => 'yii2d3\d3emails\controllers\EmailController',
                'label' => ['title' => 'Testa labelis', 'icon' => 'icon'],
                'sysCompany' => 1,
            ]
        ];

        foreach ($items as $label) {
            $def = new \d3yii2\d3labels\logic\D3Definition($label['modelClass'], $label['actionClass'], $label['label'], $label['sysCompany']);
            $def->save();
        }

        return true;
    }

    public function safeDown()
    {
        return true;
        echo "m190220_131652_test cannot be reverted.\n";
        return false;
    }

}