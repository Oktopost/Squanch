CREATE DATABASE IF NOT EXISTS `squanch_cache`;

USE `squanch_cache`;

CREATE TABLE IF NOT EXISTS `HardCache` (
    `Id` varchar(255) NOT NULL,
    `Bucket` varchar(255) NOT NULL,
    `Value` longtext NOT NULL,
    `EndDate` datetime NOT NULL,
    `Created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `Modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `TTL` int(11) NOT NULL,

    PRIMARY KEY (`Id`,`Bucket`) USING BTREE,
    
    KEY `k_EndDate` (`EndDate`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `SoftCache` (
    `Id` varchar(255) NOT NULL,
    `Bucket` varchar(255) NOT NULL,
    `Value` longtext NOT NULL,
    `EndDate` datetime NOT NULL,
    `Created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `Modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `TTL` int(11) NOT NULL,

    PRIMARY KEY (`Id`,`Bucket`) USING BTREE,
    
    KEY `k_EndDate` (`EndDate`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;