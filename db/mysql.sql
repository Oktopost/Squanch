CREATE TABLE `HardCache` (
  `Id` varchar(255) NOT NULL,
  `Value` text NOT NULL,
  `EndDate` datetime NOT NULL,
  `Created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `TTL` int(11) NOT NULL,
   PRIMARY KEY (`Id`),
   KEY `k_EndDate` (`EndDate`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;