{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}plugins`;
CREATE TABLE `{install:prefix}plugins` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `author` varchar(75) NOT NULL default '',
  `contributor` varchar(75) NOT NULL default '',
  `title` varchar(150) NOT NULL default '',
  `status` enum('inactive','active','incomplete') NOT NULL default 'active',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `version` varchar(15) NOT NULL default '',
  `summary` text NOT NULL,
  `htaccess` text,
  `uninstall_code` text,
  `uninstall_sql` text,
  PRIMARY KEY  (`id`)
) {install:mysql_version};

INSERT INTO `{install:prefix}plugins` (`id`, `name`, `author`, `contributor`, `title`, `status`, `version`, `summary`, `htaccess`, `uninstall_code`, `uninstall_sql`) VALUES
(NULL, 'kcaptcha', 'Sergey Ten', 'Intelliants LLC', 'KCaptcha', 'active', '1.0', 'Plugin will add the CAPTCHA to all pages where it needs.', '', 'global $esynConfig;\n\n				$current_value = $esynConfig->getConfig(''captcha_name'');\n\n				$esynConfig->setTable(''config'');\n				$current_values = $esynConfig->one("`multiple_values`", "`name` = ''captcha_name''");\n				$esynConfig->resetTable();\n\n				$values = explode('','', $current_values);\n\n				if(!empty($values))\n				{\n					foreach($values as $key => $value)\n					{\n						if(''kcaptcha'' == $value)\n						{\n							unset($values[$key]);\n						}\n					}\n				}\n\n				$updated_values = join('','', $values);\n\n				$esynConfig->setTable(''config'');\n				$esynConfig->update(array(''multiple_values'' => $updated_values), "`name` = ''captcha_name''");\n				$esynConfig->resetTable();', ''),
(NULL, 'comments', 'Sergey Ten', 'Intelliants LLC', 'Comments', 'active', '2.0', 'The plugin allows visitors adding comments to listings.', NULL, NULL, 'a:3:{i:0;a:2:{s:5:"query";s:30:"DROP TABLE `{prefix}comments`;";s:8:"external";b:0;}i:1;a:2:{s:5:"query";s:27:"DROP TABLE `{prefix}votes`;";s:8:"external";b:0;}i:2;a:2:{s:5:"query";s:101:"ALTER TABLE `{prefix}listings` DROP `num_votes`, DROP `rating`, DROP `min_rating`, DROP `max_rating`;";s:8:"external";b:0;}}');
