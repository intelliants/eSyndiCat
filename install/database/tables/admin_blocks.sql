{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}admin_blocks`;
CREATE TABLE `{install:prefix}admin_blocks` (
  `name` varchar(100) NOT NULL DEFAULT '',
  `plugin` varchar(50) NOT NULL DEFAULT '',
  `title` text NOT NULL,
  `order` float NOT NULL DEFAULT '0'
) {install:mysql_version};

INSERT INTO `{install:prefix}admin_blocks` (`name`, `plugin`, `title`, `order`) VALUES('common', '', 'Common', 1);
INSERT INTO `{install:prefix}admin_blocks` (`name`, `plugin`, `title`, `order`) VALUES('categories', '', 'Categories', 2);
INSERT INTO `{install:prefix}admin_blocks` (`name`, `plugin`, `title`, `order`) VALUES('listings', '', 'Listings', 3);
INSERT INTO `{install:prefix}admin_blocks` (`name`, `plugin`, `title`, `order`) VALUES('plugins', '', 'Plugins', 4);

