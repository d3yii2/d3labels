<?php

use yii\db\Migration;

class m250330_220627_d3yii2_d3labels_labe_history_model_record_id_to_bigint  extends Migration {

    public function safeUp() { 
        $this->execute('
            ALTER TABLE `d3l_label_history`
              CHANGE `model_record_id` `model_record_id` BIGINT (10) UNSIGNED NOT NULL COMMENT \'Model record\';
        ');
        $this->execute('
            ALTER TABLE `d3l_notes`
              CHANGE `model_record_id` `model_record_id` BIGINT (10) UNSIGNED NOT NULL;
        ');
    }

    public function safeDown() {
        echo "m250330_220627_d3yii2_d3labels_labe_history model_record_id_to_bigint cannot be reverted.\n";
        return false;
    }
}
