{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}accounts`;
CREATE TABLE `{install:prefix}accounts` (
  `id` int(8) NOT NULL auto_increment,
  `username` varchar(30) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `email` varchar(50) NOT NULL default '',
  `nemail` varchar(50) NOT NULL default '',
  `sec_key` varchar(32) NOT NULL default '',
  `status` enum('approval', 'active', 'banned', 'unconfirmed') NOT NULL default 'active',
  `date_reg` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `status` (`status`)
) {install:mysql_version};

