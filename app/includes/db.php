<?php
$db_user = "root";
$db_password = "1";
$db_host = "db";
$db_name = "pinewood";
$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);

function db() {
    global $mysqli;
    return @$mysqli;
}

$sql = "CREATE TABLE IF NOT EXISTS `groups` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(30) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4";
db()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `heats` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `racers` varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
db()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `racers` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `groupid` int(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4";
db()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `results` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `heatid` int(11) NOT NULL,
    `racerid` int(11) NOT NULL,
    `points` int(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4";
db()->query($sql);
