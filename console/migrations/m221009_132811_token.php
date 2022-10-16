<?php
namespace console\migrations;

class m221009_132811_token
{

    public function up($db)
    {
       $db->query("CREATE TABLE IF NOT EXISTS `token` (
            `id` int(10) NOT NULL AUTO_INCREMENT,
            `token` varchar(255) NOT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    public function down($db)
    {
       $db->query("DROP TABLE `token`");
    }

}