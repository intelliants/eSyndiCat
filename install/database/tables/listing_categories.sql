{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}listing_categories`;
CREATE TABLE `{install:prefix}listing_categories` (
  `listing_id` int(8) NOT NULL default '0',
  `category_id` int(8) NOT NULL default '0',
  KEY `listing_id` (`listing_id`,`category_id`)
) {install:mysql_version};

