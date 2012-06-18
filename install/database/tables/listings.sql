{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}listings`;
CREATE TABLE `{install:prefix}listings` (
  `id` int(8) unsigned NOT NULL auto_increment,
  `account_id` int(8) NOT NULL default '0',
  `category_id` int(11) NOT NULL default '0',
  `plan_id` int(11) NOT NULL default '0',
  `transaction_id` int(11) NOT NULL default '0',
  `moved_from` int(8) NOT NULL default '-1',
  `domain` varchar(150) NOT NULL default '',
  `url` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `email` varchar(100) NOT NULL default '',
  `reciprocal` varchar(100) NOT NULL default '',
  `recip_valid` enum('0','1') NOT NULL default '0',
  `ip_address` varchar(15) NOT NULL default '',
  `listing_header` int(4) NOT NULL default '0',
  `status` enum('approval','banned','suspended','active') NOT NULL default 'approval',
  `pagerank` tinyint(2) NOT NULL default '-1',
  `rank` tinyint(3) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `clicks` int(10) unsigned NOT NULL default '0',
  `partner` enum('0','1') NOT NULL default '0',
  `partner_start` datetime NOT NULL default '0000-00-00 00:00:00',
  `featured` enum('0','1') NOT NULL default '0',
  `featured_start` datetime NOT NULL default '0000-00-00 00:00:00',
  `sponsored` enum('0','1') NOT NULL default '0',
  `sponsored_start` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(200) NOT NULL default '',
  `expire` int(4) NOT NULL default '0',
  `action_expire` varchar(20) NOT NULL default '',
  `last_check_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `cron_cycle` enum('0','1') NOT NULL default '0',
  `fav_accounts_set` text NOT NULL,
  `meta_description` text NOT NULL,
  `meta_keywords` text NOT NULL,
  `num_votes` int(11) NOT NULL default '0',
  `rating` float NOT NULL default '0',
  `min_rating` int(11) NOT NULL default '0',
  `max_rating` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `category_id` (`category_id`),
  KEY `date` (`date`),
  KEY `recip_valid` (`recip_valid`),
  KEY `listing_header` (`listing_header`),
  KEY `status` (`status`),
  KEY `featured` (`featured`),
  KEY `partner` (`partner`),
  FULLTEXT KEY `url` (`url`,`description`)
) {install:mysql_version};


INSERT INTO `{install:prefix}listings` (`account_id`, `category_id`, `moved_from`, `domain`, `url`, `description`, `email`, `listing_header`, `status`, `pagerank`, `date`, `title`, `featured`, `featured_start`) VALUES
(0, 0, -1, 'www.esyndicat.com', 'http://www.esyndicat.com', 'eSyndiCat is a full featured software that can be used as an addition to your existing site or as a stand-alone platform. Using eSyndiCat Directory Software your website can achieve top rank and take the leading positions in the most popular search engines! You can choose both free and paid versions.', 'no-reply@esyndicat.com', 200, 'active', 5, NOW(), 'eSyndicat Directory Script', '1', NOW());

INSERT INTO `{install:prefix}listings` (`account_id`, `category_id`, `moved_from`, `domain`, `url`, `description`, `email`, `listing_header`, `status`, `pagerank`, `date`, `title`, `featured`, `featured_start`) VALUES
(0, 14, -1, 'www.subrion.com', 'http://www.subrion.com', 'Subrion CMS unites the functionality of articles script, auto classifieds script, realty classifieds script, and web directory script all in one package. Subrion''s highly scalable set of key features makes it a powerful platform for web sites.', 'no-reply@subrion.com', 200, 'active', 5, NOW(), 'Subrion CMS', '1', NOW());

INSERT INTO `{install:prefix}listings` (`account_id`, `category_id`, `moved_from`, `domain`, `url`, `description`, `email`, `listing_header`, `status`, `pagerank`, `date`, `title`, `partner`, `partner_start`) VALUES
(0, 14, -1, 'www.subrion.com', 'http://www.subrion.com/product/autos.html', 'Subrion Auto Classifieds Script is the best choice for your online auto sales business. Number of advanced features, easy customization, and powerful administration panel makes this classifieds software perfectly suitable for auto classifieds portal or for auto auction website. SEO friendly, written in PHP with MySQL it doesn\'t need any specific knowledge to operate.', 'no-reply@subrion.com', 200, 'active', 5, NOW(), 'Subrion Auto Classified', '1', NOW());

