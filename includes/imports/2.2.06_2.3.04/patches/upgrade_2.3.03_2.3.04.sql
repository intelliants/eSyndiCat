UPDATE `{prefix}config` SET `multiple_values` = ''''',''SSL'',''TLS''' WHERE `name` = 'smtp_secure_connection';
INSERT INTO `{prefix}language` (`id`, `key`, `value`, `lang`, `category`, `code`) VALUES (NULL, 'gzip_compress', 'Compress to gzip', 'English', 'admin', 'en');
INSERT INTO `{prefix}language` (`id`, `key`, `value`, `lang`, `category`, `code`) VALUES (NULL, 'instead_thumbnail', 'Use image as listing thumbnail', 'English', 'admin', 'en');
ALTER TABLE `{prefix}listing_fields` ADD `instead_thumbnail` TINYINT( 1 ) NOT NULL DEFAULT '0';
UPDATE `{prefix}config` SET `value` = '2.3.04' WHERE `name` = 'version';
