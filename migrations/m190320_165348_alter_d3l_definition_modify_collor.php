<?php

use yii\db\Migration;

/**
* Class m190320_165348_alter_d3l_definition_modify_collor*/
class m190320_165348_alter_d3l_definition_modify_collor extends Migration
{
    /**
    * {@inheritdoc}
    */
    public function safeUp()
    {
        $this->execute("alter table d3l_definition modify collor char(10) null comment 'Collor'");
    }

    public function safeDown()
    {
        echo "m190320_165348_alter_d3l_definition_modify_collor cannot be reverted.\n";
        return false;
    }

}