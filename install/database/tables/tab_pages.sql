{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}tab_pages`;
CREATE TABLE IF NOT EXISTS `{install:prefix}tab_pages` (
  `id` int(11) NOT NULL auto_increment,
  `tab_name` varchar(255) NOT NULL default '',
  `page_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) {install:mysql_version};

