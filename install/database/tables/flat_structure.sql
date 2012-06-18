{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}flat_structure`;
CREATE TABLE `{install:prefix}flat_structure` (
  `parent_id` int(8) NOT NULL default '0',
  `category_id` int(8) NOT NULL default '0',
  KEY `parent_id` (`parent_id`),
  KEY `category_id` (`category_id`)
) {install:mysql_version};


INSERT INTO `{install:prefix}flat_structure` (`parent_id`, `category_id`) VALUES
(2, 2),
(0, 2),
(3, 3),
(0, 3),
(4, 4),
(0, 4),
(5, 5),
(0, 5),
(6, 6),
(0, 6),
(7, 7),
(0, 7),
(8, 8),
(0, 8),
(9, 9),
(0, 9),
(10, 10),
(0, 10),
(11, 11),
(0, 11),
(12, 12),
(0, 12),
(13, 13),
(0, 13),
(14, 14),
(0, 14),
(15, 15),
(0, 15),
(16, 16),
(0, 16);

