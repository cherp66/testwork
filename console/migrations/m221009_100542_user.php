<?php
namespace console\migrations;

class m221009_100542_user
{
    public function up($db)
    {
        $db->query("CREATE TABLE IF NOT EXISTS `user` (
            `id` int(10) NOT NULL AUTO_INCREMENT,
            `login` varchar(30) NOT NULL,
            `password` varchar(100) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    public function down($db)
    {
       $db->query("DROP TABLE `user`");
    }

}