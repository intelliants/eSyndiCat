{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}categories`;
CREATE TABLE `{install:prefix}categories` (
  `id` int(8) unsigned NOT NULL auto_increment,
  `account_id` int(8) NOT NULL default '0',
  `parent_id` int(8) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `page_title` varchar(255) NOT NULL default '',
  `icon` varchar(255) NOT NULL default '',
  `description` text,
  `status` enum('approval','active') NOT NULL default 'approval',
  `meta_description` tinytext,
  `meta_keywords` tinytext,
  `path` varchar(250) NOT NULL default '',
  `order` smallint(10) NOT NULL default '0',
  `locked` enum('0','1') NOT NULL default '0',
  `hidden` enum('0','1') NOT NULL default '0',
  `unique_tpl` enum('0','1') NOT NULL default '0',
  `level` smallint(6) NOT NULL default '1',
  `num_cols` tinyint(4) NOT NULL default '0',
  `num_neighbours` tinyint(4) NOT NULL default '0',
  `num_listings` int(8) NOT NULL default '0',
  `num_all_listings` int(8) NOT NULL default '0',
  `clicks` INT NOT NULL default '0',
  `no_follow` tinyint(1) NOT NULL default '0',
  `confirmation` enum('0','1') NOT NULL default '0',
  `confirmation_text` text,
  PRIMARY KEY  (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `status` (`status`),
  KEY `path` (`path`),
  KEY `order` (`order`)
) {install:mysql_version} PACK_KEYS=0;


INSERT INTO `{install:prefix}categories` (`id`, `account_id`, `parent_id`, `title`, `description`, `status`, `meta_description`, `meta_keywords`, `path`, `order`, `locked`, `unique_tpl`, `level`, `num_cols`, `num_neighbours`, `num_listings`, `num_all_listings`, `no_follow`) VALUES
(0, 0, -1, 'ROOT', '', 'active', '', '', '', 0, '0', '0', 0, 0, 0, 3, 3, 0);

INSERT INTO `{install:prefix}categories` (`id`, `parent_id`, `title`, `description`, `status`, `path`, `level`, `num_listings`, `num_all_listings`) VALUES
(2, 0, 'Sports', 'Sports Directory', 'active', 'sports', 0, 0, 0),
(3, 0, 'Shopping', 'Shopping Directory', 'active', 'shopping', 0, 0, 0),
(4, 0, 'Society', 'Society Directory', 'active', 'society', 0, 0, 0),
(5, 0, 'Misc', 'Miscellaneous and Everything Else Directory', 'active', 'misc', 0, 0, 0),
(6, 0, 'Science', 'Science Directory', 'active', 'science', 0, 0, 0),
(7, 0, 'Reference', 'Reference Directory', 'active', 'reference', 0, 0, 0),
(8, 0, 'Regional', 'Regional Directory', 'active', 'regional', 0, 0, 0),
(9, 0, 'News', 'News Directory', 'active', 'news', 0, 0, 0),
(10, 0, 'Home', 'Home Directory', 'active', 'home', 0, 0, 0),
(11, 0, 'Kids and Teens', 'Kids and Teen Directory', 'active', 'kids-and-teens', 0, 0, 0),
(12, 0, 'Games', 'Games Directory', 'active', 'games', 0, 0, 0),
(13, 0, 'Health', 'Health Directory', 'active', 'health', 0, 0, 0),
(14, 0, 'Computers', 'Computers Directory', 'active', 'computers', 0, 2, 2),
(15, 0, 'Arts', 'Arts Directory', 'active', 'arts', 0, 0, 0),
(16, 0, 'Business', 'Business Directory', 'active', 'business', 0, 0, 0);

UPDATE `{install:prefix}categories` SET `id` = '0' WHERE `parent_id` = '-1';

