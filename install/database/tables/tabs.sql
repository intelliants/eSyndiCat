{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}tabs`;
CREATE TABLE IF NOT EXISTS `{install:prefix}tabs` (
  `name` varchar(255) NOT NULL default '',
  `sticky` enum('1','0') NOT NULL default '1',
  `order` float NOT NULL default '0',
  PRIMARY KEY  (`name`)
) {install:mysql_version};

INSERT INTO `{install:prefix}tabs` VALUES
('help', '1', 1),
('search', '1', 2);

