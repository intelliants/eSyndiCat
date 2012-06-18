{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}actions`;
CREATE TABLE `{install:prefix}actions` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR( 255 ) NOT NULL ,
	`url` varchar(255) NOT NULL default '',
	`plugin` varchar(50) NOT NULL default '',
	`order` FLOAT NOT NULL,
	PRIMARY KEY (`id`)
) {install:mysql_version};

INSERT INTO `{install:prefix}actions` (`id`, `name`, `url`, `order`) VALUES
(1, 'action_favorite', '', 1),
(2, 'report_listing', '', 2),
(3, 'visit_website', '', 3);
