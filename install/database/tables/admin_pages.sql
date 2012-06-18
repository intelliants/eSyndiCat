{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}admin_pages`;
CREATE TABLE `{install:prefix}admin_pages` (
  `id` int(11) NOT NULL auto_increment,
  `block_name` varchar(100) NOT NULL default '',
  `title` varchar(50) NOT NULL default '',
  `file` varchar(255) NOT NULL default '',
  `attr` varchar(100) NOT NULL default '',
  `params` varchar(255) NOT NULL default '',
  `aco` varchar(30) NOT NULL default '',
  `menus` set('main','header') default NULL,
  `plugin` varchar(50) NOT NULL default '',
  `order` float NOT NULL default '0',
  `header_order` float NOT NULL default '0',
  PRIMARY KEY  (`id`)
) {install:mysql_version};

INSERT INTO `{install:prefix}admin_pages` (`id`, `block_name`, `title`, `file`, `attr`, `params`, `aco`, `menus`, `plugin`, `order`, `header_order`) VALUES
(NULL, 'common', 'Admin Panel', 'index', '', '', 'index', 'main', '', 1, 0),
(NULL, 'common', 'Configuration', 'configuration', '', '', 'configuration', 'main', '', 2, 0),
(NULL, 'common', 'Manage Database', 'database', '', '', 'database', 'main', '', 3, 0),
(NULL, 'common', 'Language Manager', 'language', '', '', 'language', 'main', '', 4, 0),
(NULL, 'common', 'Manage Admins', 'admins', '', '', 'admins', 'main', '', 5, 0),
(NULL, 'common', 'Manage Accounts', 'accounts', '', '', 'accounts', 'main', '', 6, 0),
(NULL, 'common', 'Manage Templates', 'templates', '', '', 'templates', 'main', '', 7, '0'),
(NULL, 'common', 'Manage Blocks', 'blocks', '', '', 'blocks', 'main', '', 8, 0),
(NULL, 'common', 'Visual Manage', 'visual', 'target="_blank"', '', 'visual', 'main', '', 9, 0),
(NULL, 'categories', 'Manage Categories', 'categories', '', '', 'categories', 'main', '', 1, 0),
(NULL, 'categories', 'Create Category', 'suggest-category', '', '', 'create_category', 'main,header', '', 2, 4),
(NULL, 'categories', 'Browse Categories', 'browse', '', '', 'browse', 'main,header', '', 3, 5),
(NULL, 'categories', 'Category Icons', 'category-icons', '', '', 'category_icons', 'main', '', 4, 0),
(NULL, 'listings', 'Create Listing', 'suggest-listing', '', '', 'create_listing', 'main,header', '', 1, 1),
(NULL, 'listings', 'Manage Listings', 'listings', '', '', 'listings', 'main,header', '', 2, 2),
(NULL, 'listings', 'Manage Comments', '', '', '', 'comments', 'main', 'comments', 3, 9),
(NULL, '', 'divider', '', '', '', '', 'header', '', 3, 3),
(NULL, '', 'Update Version', 'update', '', '', '', NULL, '', 0, 0),
(NULL, '', 'Sitemap', 'sitemap', '', '', 'sitemap', 'header', '', 0, 7),
(NULL, '', 'PHP Info', 'index&amp;info', '', '', 'phpinfo', 'header', '', 0, 8),
(NULL, '', 'divider', '', '', '', '', 'header', '', '0', '5'),
(NULL, '', 'Order Change', 'order-change', '', '', 'order_change', '', '', 0, 0);


