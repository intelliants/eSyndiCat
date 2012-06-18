{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}related`;
CREATE TABLE `{install:prefix}related` (
  `id` int(8) NOT NULL auto_increment,
  `category_id` int(8) NOT NULL default '0',
  `related_id` int(8) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `category_id` (`category_id`),
  KEY `related_id` (`related_id`)
) {install:mysql_version};

