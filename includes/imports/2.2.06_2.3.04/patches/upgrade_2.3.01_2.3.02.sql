INSERT INTO `{prefix}config` (`group_name`, `name`, `value`, `multiple_values`, `type`, `description`, `plugin`, `editor`, `order`) VALUES ('admin_panel', 'check_updates', '1', '''1'',''0''', 'radio', 'Check for updates', '', '0', '6');
DELETE FROM `{prefix}config` WHERE `name` = 'caching';
UPDATE `{prefix}config` SET `value` = '2.3.02' WHERE `name` = 'version';
