{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}field_categories`;
CREATE TABLE `{install:prefix}field_categories` (
  `id` int(11) NOT NULL auto_increment,
  `field_id` INT( 11 ) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) {install:mysql_version};

INSERT INTO `{install:prefix}field_categories` (`id`,`field_id`, `category_id`) VALUES 
(NULL, '1', '0'),
(NULL, '2', '0'),
(NULL, '3', '0'),
(NULL, '4', '0'),
(NULL, '5', '0');

