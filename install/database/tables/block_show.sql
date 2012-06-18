{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}block_show`;
CREATE TABLE `{install:prefix}block_show` (
  `id` int(11) NOT NULL auto_increment,
  `page` varchar(100) NOT NULL default '0',
  `block_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `page` (`page`,`block_id`)
) {install:mysql_version};

INSERT INTO `{install:prefix}block_show` (`id`, `page`, `block_id`) VALUES
(NULL, 'index', 3),
(NULL, 'view_listing', 5),
(NULL, 'view_listing', 7);

