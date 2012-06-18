{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}category_clicks`;
CREATE TABLE `{install:prefix}category_clicks` (
  `id` int(11) NOT NULL auto_increment,
  `category_id` int(11) NOT NULL default '0',
  `ip` varchar(15) NOT NULL default '',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `category_id` (`category_id`)
) {install:mysql_version};

