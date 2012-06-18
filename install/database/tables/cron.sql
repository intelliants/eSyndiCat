{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}cron`;
CREATE TABLE `{install:prefix}cron` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL default '',
  `nextrun` int(10) unsigned NOT NULL default '0',
  `data` varchar(250) NOT NULL default '',
  `active` tinyint(1) NOT NULL default '1',
  `extras` varchar(30) default NULL,
  PRIMARY KEY  (`id`)
) {install:mysql_version};

INSERT INTO `{install:prefix}cron` (`id`, `name`, `nextrun`, `data`, `active`, `extras`) VALUES
(NULL, 'check_listings', 0, '0 */3 * * * includes/cron/check.php', 1, NULL),
(NULL, 'check_expired', 0, '10 0 * * * includes/cron/check_expired.php', 1, NULL),
(NULL, 'backup', 0, '5 0 * * * includes/cron/backup.php', 1, NULL);
