<?php

use yii\db\Migration;

/**
* Class m220701_125918_alterTables*/
class m220701_125918_alterTables extends Migration
{
    /**
    * {@inheritdoc}
    */
    public function safeUp()
    {
        $this->addColumn('d3l_label', 'notes', $this->string(255)->append('CHARACTER SET utf8 COLLATE utf8_general_ci'));
        $this->renameTable('d3_label_history', 'd3l_label_history');
        $this->addColumn('d3l_label_history', 'notes', $this->string(255)->append('CHARACTER SET utf8 COLLATE utf8_general_ci'));
    }

    public function safeDown()
    {
        echo "m220701_125918_alterTables cannot be reverted.\n";
        return false;
    }

}