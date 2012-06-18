ALTER TABLE `{prefix}blocks` ADD `collapsed` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `collapsible` ;
UPDATE `{prefix}config` SET `value` = '# SECTION 1\r\n# correct urls for yahoo bot\r\nRewriteCond %{REQUEST_URI} !\\..+$\r\nRewriteCond %{REQUEST_URI} !/$\r\nRewriteRule (.*) %{REQUEST_URI}/ [R=301,L]\r\n\r\n# mod_rewrite rules for plugins\r\nRewriteRule ^mod/(.*)/$ controller.php?plugin=$1 [QSA,L]\r\nRewriteRule ^mod/(.*)/(.*)/$ controller.php?plugin=$1&file=$2 [QSA,L]\r\nRewriteRule ^mod/(.*)/(.*).html$ controller.php?plugin=$1&file=$2 [QSA,L]\r\n\r\n# mod_rewrite rules for view account page\r\nRewriteRule ^accounts/$ accounts.php [QSA,L]\r\nRewriteRule ^accounts/(.*)/$ accounts.php?alpha=$1 [QSA,L]\r\nRewriteRule ^accounts/(.*).html$ view-account.php?account=$1 [QSA,L]\r\n\r\n' WHERE `name` = 'htaccessfile_1';
INSERT INTO `{prefix}config` (`group_name`, `name`, `value`, `multiple_values`, `type`, `description`, `plugin`, `editor`, `order`) VALUES ('general', 'site_watermark', '', '', 'image', 'Site Watermark', '', '0', 3.11);
INSERT INTO `{prefix}config` (`group_name`, `name`, `value`, `multiple_values`, `type`, `description`, `plugin`, `editor`, `order`) VALUES ('general', 'site_watermark_position', 'bottom_right', 'top_left,top_center,top_right,middle_left,middle_center,middle_right,bottom_left,bottom_center,bottom_right', 'select', 'Site Watermark Position', '', '0', 3.12);
INSERT INTO `{prefix}config` (`group_name`, `name`, `value`, `multiple_values`, `type`, `description`, `plugin`, `editor`, `order`) VALUES ('general', 'lowercase_urls', '0', '''1'',''0''', 'radio', 'Lowercase case URLs', '', '0', 21);
UPDATE `{prefix}config` SET `value` = 'left,right,top,center,bottom,user1,user2,verybottom,verytop,topbanner' WHERE `name` = 'esyndicat_block_positions';
UPDATE `{prefix}language` SET `category` = 'common' WHERE `key` = 'account_email_exists';
UPDATE `{prefix}language` SET `value` = '<p>Below is the information you submitted so far. You will be able to extend and edit this information via your member account.</p><p><span style=\"color: #F00; font-weight: bold;\">IMPORTANT!</span> Your account password has been sent to the following email address:</p>' WHERE `key` = 'thankyou_head';
UPDATE `{prefix}language` SET `key` = 'wrong_image_type' WHERE `key` = 'wrong_site_logo_image_type';
UPDATE `{prefix}language` SET `value` = 'Only following image types are allowed (<i>{types}</i>).' WHERE `key` = 'wrong_image_type';

INSERT INTO `{prefix}language` (`id`, `key`, `value`, `lang`, `category`, `code`) VALUES (NULL, 'view_image', 'View Image', 'English', 'admin', 'en');
INSERT INTO `{prefix}language` (`id`, `key`, `value`, `lang`, `category`, `code`) VALUES (NULL, 'image_not_found', 'Image not found', 'English', 'admin', 'en');
INSERT INTO `{prefix}language` (`id`, `key`, `value`, `lang`, `category`, `code`) VALUES (NULL, 'uploading_image', 'Uploading Image', 'English', 'admin', 'en');
INSERT INTO `{prefix}language` (`id`, `key`, `value`, `lang`, `category`, `code`) VALUES (NULL, 'select_image', 'Select Image', 'English', 'admin', 'en');
INSERT INTO `{prefix}language` (`id`, `key`, `value`, `lang`, `category`, `code`) VALUES (NULL, 'image', 'Image', 'English', 'admin', 'en');
INSERT INTO `{prefix}language` (`id`, `key`, `value`, `lang`, `category`, `code`) VALUES (NULL, 'upload_image_error', 'Upload Image Error', 'English', 'admin', 'en');
INSERT INTO `{prefix}language` (`id`, `key`, `value`, `lang`, `category`, `code`) VALUES (NULL, 'collapsed', 'Collapsed', 'English', 'admin', 'en');
UPDATE `{prefix}config` SET `value` = '2.3.03' WHERE `name` = 'version';
