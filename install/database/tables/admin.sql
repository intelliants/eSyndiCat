{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}admins`;
CREATE TABLE `{install:prefix}admins` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `username` varchar(50) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `fullname` varchar(100) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `submit_notif` enum('0','1') NOT NULL default '0',
  `payment_notif` enum('0','1') NOT NULL default '0',
  `super` enum('0','1') NOT NULL default '1',
  `status` enum('inactive','active') NOT NULL default 'active',
  `confirmation` varchar(32) NOT NULL default '',
  `date_reg` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_visited` datetime NOT NULL default '0000-00-00 00:00:00',
  `state` text NOT NULL,
  PRIMARY KEY  (`id`)
) {install:mysql_version};

INSERT INTO `{install:prefix}admins` (`id`, `username`, `password`, `fullname`, `email`, `submit_notif`, `payment_notif`, `super`, `status`, `confirmation`, `date_reg`, `last_visited`) VALUES
(1, '{install:admin_username}', '{install:admin_password}', 'Administrator', '{install:email}', '1', '1', '1', 'active', '', NOW(), NOW());
