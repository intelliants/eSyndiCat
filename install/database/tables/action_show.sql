{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}action_show`;
CREATE TABLE `{install:prefix}action_show` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`page` VARCHAR( 100 ) NOT NULL ,
	`action_name` VARCHAR(255) NOT NULL default '',
	PRIMARY KEY (`id`)
) {install:mysql_version};

INSERT INTO `{install:prefix}action_show` (`id`, `page`, `action_name`) VALUES
(1, 'view_listing', 'action_favorite'),
(2, 'view_listing', 'report_listing'),
(3, 'view_listing', 'visit_website');
