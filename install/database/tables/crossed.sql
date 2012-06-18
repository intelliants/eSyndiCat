{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}crossed`;
CREATE TABLE `{install:prefix}crossed` (
  `id` int(8) unsigned NOT NULL auto_increment,
  `category_id` int(8) unsigned NOT NULL default '0',
  `category_title` VARCHAR( 255 ) NOT NULL,
  `crossed_id` int(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `category_id` (`category_id`,`crossed_id`)
) {install:mysql_version};
