<?php

use yii\db\Migration;

class m250329_075747_d3yii2_d3labels_label_model_record_Id_to_bigint  extends Migration {

    public function safeUp() { 
        $this->execute('
            ALTER TABLE `d3l_label`
              CHANGE `model_record_id` `model_record_id` BIGINT (10) UNSIGNED NOT NULL;
            
                    
        ');
    }

    public function safeDown() {
        echo "m250329_075747_poker_clickhouse_label_model_record_Id_to_bigint cannot be reverted.\n";
        return false;
    }
}
