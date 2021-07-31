<?php

use yii\db\Migration;

/**
* Class m210730_153939_addUserTimeCol*/
class m210730_153939_addUserTimeCol extends Migration
{
    /**
    * {@inheritdoc}
    */
    public function safeUp()
    {
        $this->execute("
        ALTER TABLE `d3l_label`
          ADD COLUMN `user_id` INT(11) NULL,
          ADD COLUMN `time` TIMESTAMP NULL;
        ");
    }

    public function safeDown()
    {
        echo "m210730_153939_addUserTimeCol cannot be reverted.\n";
        return false;
    }

}