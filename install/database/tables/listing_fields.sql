{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}listing_fields`;
CREATE TABLE `{install:prefix}listing_fields` (
  `id` int(4) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `type` enum('text','textarea','combo','radio','checkbox','storage','image','number','pictures') NOT NULL default 'text',
  `length` varchar(255) NOT NULL default '',
  `values` text,
  `default` varchar(255) NOT NULL default '',
  `order` double NOT NULL default '0',
  `tooltip` text NOT NULL default '',
  `editor` enum('0','1') NOT NULL default '0',
  `required` enum('1','0') NOT NULL default '0',
  `adminonly` enum('0','1') NOT NULL default '0',
  `pages` set('suggest','edit','view') NOT NULL default '',
  `image_height` int(4) NOT NULL default '0',
  `image_width` int(4) NOT NULL default '0',
  `thumb_width` int(4) NOT NULL default '0',
  `thumb_height` int(4) NOT NULL default '0',
  `resize_mode` int(4) NOT NULL default '1001',
  `file_prefix` varchar(50) NOT NULL default '',
  `searchable` tinyint(1) NOT NULL default '0',
  `file_types` text,
  `section_key` varchar(30) NOT NULL default '',
  `show_as` enum('checkbox','radio','combo') NOT NULL default 'checkbox',
  `any_meta` varchar(50) NOT NULL default '',
  `recursive` tinyint(1) NOT NULL default '0',
  `instead_thumbnail` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) {install:mysql_version};

INSERT INTO `{install:prefix}listing_fields` (`id`, `name`, `type`, `length`, `values`, `default`, `order`, `required`, `adminonly`, `pages`, `thumb_width`,  `thumb_height`, `image_height`, `image_width`, `file_prefix`, `searchable`, `file_types`, `section_key`, `show_as`, `any_meta`, `recursive`) VALUES
(1, 'url', 'text', '150', '', 'http://', 2, '1', '0', 'suggest,edit,view', 0, 0, 0, 0, '', 0, '', '', 'checkbox', '', 1),
(2, 'title', 'text', '200', '', '', 1, '0', '0', 'suggest,edit,view', 0, 0, 0, 0, '', 1, '', '', 'checkbox', '', 1),
(3, 'description', 'textarea', '50,500', '', '', 3, '0', '0', 'suggest,edit,view', 0, 0, 0, 0, '', 1, '', '', 'checkbox', '', 1),
(4, 'email', 'text', '100', '', '', 5, '1', '0', 'suggest,edit', 0, 0, 0, 0, '', 0, '', '', 'checkbox', '', 1),
(5, 'reciprocal', 'text', '100', '', 'http://', 4, '0', '0', 'suggest,edit', 0, 0, 0, 0, '', 0, '', '', 'checkbox', '', 1),
(6, 'meta_description', 'textarea', '0,150', '', '', 6, '0', '1', '', 0, 0, 0, 0, '', 0, '', '', 'checkbox', '', 1),
(7, 'meta_keywords', 'textarea', '0,256', '', '', 7, '0', '1', '', 0, 0, 0, 0, '', 0, '', '', 'checkbox', '', 1);

