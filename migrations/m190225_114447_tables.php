<?php

use yii\db\Migration;

/**
 * Class m190225_114447_tables*/
class m190225_114447_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("     
            CREATE TABLE `d3l_definition` (
              `id` smallint(10) UNSIGNED NOT NULL,
              `sys_company_id` smallint(5) UNSIGNED DEFAULT NULL,
              `model_id` tinyint(3) UNSIGNED NOT NULL COMMENT 'Model',
              `label` varchar(20) DEFAULT NULL COMMENT 'Label',
              `collor` char(10) CHARACTER SET latin1 DEFAULT NULL COMMENT 'Collor',
              `icon` varchar(20) CHARACTER SET latin1 DEFAULT NULL COMMENT 'Icon',
              `action_class` text CHARACTER SET latin1 COMMENT 'Action Class',
              `action_method` varchar(256) CHARACTER SET latin1 DEFAULT NULL COMMENT 'Action Method'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
            CREATE TABLE `d3l_label` (
              `id` int(10) UNSIGNED NOT NULL,
              `definition_id` smallint(5) UNSIGNED NOT NULL,
              `model_record_id` int(10) UNSIGNED NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
            
            CREATE TABLE `d3l_time_bomb` (
              `id` int(10) UNSIGNED NOT NULL,
              `definition_id` smallint(5) UNSIGNED NOT NULL,
              `record_id` int(10) UNSIGNED NOT NULL,
              `explode_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `status` enum('Active','Canceled','Exploded') NOT NULL DEFAULT 'Active'
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
            
            CREATE TABLE `d3_label_history` (
              `id` int(10) UNSIGNED NOT NULL,
              `definition_id` smallint(10) UNSIGNED NOT NULL COMMENT 'Definition',
              `model_record_id` int(10) UNSIGNED NOT NULL COMMENT 'Model record',
              `action` enum('Added','Droped','Canceled','Exploded') NOT NULL COMMENT 'Action',
              `model_id` int(10) UNSIGNED NOT NULL COMMENT 'Label/Time Bomb record',
              `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Time',
              `user_id` int(11) DEFAULT NULL COMMENT 'User'
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

            ALTER TABLE `d3l_definition`
              ADD PRIMARY KEY (`id`),
              ADD UNIQUE KEY `model_id` (`model_id`,`label`),
              ADD KEY `model_id` (`model_id`);
            
            ALTER TABLE `d3l_label`
              ADD PRIMARY KEY (`id`),
              ADD UNIQUE KEY `definition_id` (`definition_id`,`model_record_id`),
              ADD KEY `definition_id` (`definition_id`);
            
            ALTER TABLE `d3l_time_bomb`
              ADD PRIMARY KEY (`id`),
              ADD KEY `definition_id` (`definition_id`);
            
            ALTER TABLE `d3_label_history`
              ADD PRIMARY KEY (`id`),
              ADD KEY `definition_id` (`definition_id`);
            
            ALTER TABLE `d3l_definition`
              MODIFY `id` smallint(10) UNSIGNED NOT NULL AUTO_INCREMENT;
            ALTER TABLE `d3l_label`
              MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
            ALTER TABLE `d3l_time_bomb`
              MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
            ALTER TABLE `d3_label_history`
              MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
            
            ALTER TABLE `d3l_definition`
              ADD CONSTRAINT `d3l_definition_ibfk_1` FOREIGN KEY (`model_id`) REFERENCES `sys_models` (`id`);
            
            ALTER TABLE `d3l_label`
              ADD CONSTRAINT `d3l_label_ibfk_1` FOREIGN KEY (`definition_id`) REFERENCES `d3l_definition` (`id`);
            
            ALTER TABLE `d3l_time_bomb`
              ADD CONSTRAINT `d3l_time_bomb_ibfk_1` FOREIGN KEY (`definition_id`) REFERENCES `d3l_definition` (`id`);
            
            ALTER TABLE `d3_label_history`
              ADD CONSTRAINT `d3_label_history_ibfk_1` FOREIGN KEY (`definition_id`) REFERENCES `d3l_definition` (`id`);
       ");
    }

    public function safeDown()
    {
        echo "m190225_114447_tables cannot be reverted.\n";
        return false;
    }

}