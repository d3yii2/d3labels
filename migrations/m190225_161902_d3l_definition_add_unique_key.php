<?php

use yii\db\Migration;

/**
* Class m190225_161902_d3l_definition_add_unique_key*/
class m190225_161902_d3l_definition_add_unique_key extends Migration
{
    /**
    * {@inheritdoc}
    */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `d3l_definition` ADD UNIQUE( `model_id`, `label`)");
    }

    public function safeDown()
    {
        echo "m190225_161902_d3l_definition_add_unique_key cannot be reverted.\n";
        return false;
    }

}