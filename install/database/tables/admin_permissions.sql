{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}admin_permissions`;
CREATE TABLE `{install:prefix}admin_permissions` (
  `id` int(11) NOT NULL auto_increment,
  `admin_id` int(11) NOT NULL default '0',
  `aco` varchar(30) NOT NULL default '',
  `allow` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) {install:mysql_version};

