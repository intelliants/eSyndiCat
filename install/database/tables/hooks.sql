{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}hooks`;
CREATE TABLE `{install:prefix}hooks` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `plugin` varchar(50) NOT NULL default '',
  `name` varchar(250) NOT NULL default '',
  `type` enum('php','html','smarty','plain') NOT NULL default 'php',
  `code` text,
  `file` text,
  `status` enum('active','inactive') NOT NULL default 'active',
  `order` smallint(5) unsigned NOT NULL default '5',
  PRIMARY KEY  (`id`),
  KEY `status` (`status`)
) {install:mysql_version};

INSERT INTO `{install:prefix}hooks` (`plugin`, `name`, `type`, `code`, `file`, `status`, `order`) VALUES
('comments', 'viewListingAfterMainFieldsDisplay', 'smarty', '<tr>\n	<td><strong>{$lang.comments}:</strong></td>\n	<td>{$total_comments}</td>\n</tr>', '', 'active', 1),
('comments', 'statisticsBlock', 'smarty', '{if isset($num_total_comments)}\n	<tr>\n		<td>{$lang.comments}:</td>\n		<td>{$num_total_comments}</td>\n	</tr>\n{/if}', '', 'active', 2),
('comments', 'bootstrap', 'php', 'global $eSyndiCat, $esynSmarty, $esynConfig;\n				\n$eSyndiCat->setTable("comments");\n$num_total_comments = $eSyndiCat->one("COUNT(*)", "`status` = ''active''");\n$eSyndiCat->resetTable();				\n$esynSmarty->assign(''num_total_comments'', $num_total_comments);\n	\n$sql = "SELECT * FROM `{$eSyndiCat->mPrefix}comments`";\n$sql .= "WHERE `status`=''active''";\n$sql .= "ORDER BY `date` DESC ";\n$sql .= "LIMIT ".$esynConfig->getConfig(''num_latest_comments'');\n$latest_comments = $eSyndiCat->getAll($sql);\n\n$eSyndiCat->factory(''Listing'');\nglobal $esynListing;\nif (!empty($latest_comments))\n{\n	foreach ($latest_comments as $key => $comment)\n	{\n		$latest_comments[$key][''listing''] = $esynListing->getListingById($comment[''listing_id'']);\n	}\n}\n$esynSmarty->assign(''latest_comments'', $latest_comments);', '', 'active', 4),
('comments', 'viewListing', 'php', 'global $eSyndiCat, $esynSmarty, $esynConfig, $esynI18N, $listing;\n\n$eSyndiCat->setTable("comments");\n$comments = $eSyndiCat->all("*", "`listing_id` = :id AND `status` = ''active''", array(''id'' => $listing[''id'']));\n$eSyndiCat->resetTable();\n\nrequire_once(ESYN_HOME . ''plugins'' . ESYN_DS . ''comments'' . ESYN_DS . ''includes'' . ESYN_DS . ''classes'' . ESYN_DS . ''esynRating.php'');\n\n$esynRating = new esynRating();\n\n$rating = $esynRating->getRating($listing[''id'']);\n\n$rating[''voted''] = false;\n\nif($esynRating->isVoted($_SERVER[''REMOTE_ADDR''], $listing[''id'']))\n{\n	$rating[''voted''] = true;\n\n	$rating[''html''] = number_format($rating[''rating''], 2);\n	$rating[''html''] .= ''&nbsp;/&nbsp;'';\n	$rating[''html''] .= $esynConfig->getConfig(''listing_rating_block_max'');\n	$rating[''html''] .= ''&nbsp;('';\n	$rating[''html''] .= $rating[''num_votes''];\n	$rating[''html''] .= ''&nbsp;'';\n	$rating[''html''] .= $rating[''num_votes''] > 1 ? $esynI18N[''votes_cast''] : $esynI18N[''vote_cast''];\n	$rating[''html''] .= '')&nbsp;'';\n	$rating[''html''] .= ''<span style="color: green;">'';\n	$rating[''html''] .= $esynI18N[''thanks_for_voting''];\n	$rating[''html''] .= ''</span>'';\n}\n\n$esynSmarty->assign(''comments'', $comments);\n$esynSmarty->assign(''rating'', $rating);\n$esynSmarty->assign(''total_comments'', count($comments));', '', 'active', 5);

