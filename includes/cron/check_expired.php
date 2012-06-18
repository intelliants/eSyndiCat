<?php

global $esynConfig;

if($esynConfig->getConfig('expiration_period') > 0)
{
	$sql = "SELECT `id`, `url`, `title`, `description`, `email`, `action_expire` ";
	$sql .= "FROM `".ESYN_DBPREFIX."listings` ";
	$sql .= "WHERE `link`.`expire` <> 0";
	$sql .= "AND `date` + INTERVAL `expire` DAY < NOW() ";

	$listings = $esynListing->getAll($sql);

	if(!empty($listings))
	{
		foreach($listings as $listing)
		{
			$action_expire = !empty($listing['action_expire']) ? $listing['action_expire'] : $esynConfig->getConfig('expiration_action');

			if(!empty($action_expire))
			{
				if(in_array($action_expire, array('approval', 'banned', 'suspended')))
				{
					$esynListing->update(array('status' => $action_expire), "`id` = '{$listing['id']}'");
				}
				elseif(in_array($action_expire, array('regular', 'featured', 'partner')))
				{
					$fields = array(
						'sponsored' => '0',
						'partner'	=> '0',
						'featured'	=> '0',
						'plan_id'	=> '0'
					);

					'regular' == $action_expire OR $fields[$action_expire] = '1';

					$esynListing->update($fields, "`id` = '{$listing['id']}'");
				}
				elseif('remove' == $action_expire)
				{
					$esynListing->delete("`id` = '{$listing['id']}'");
				}
			}
		}
		// recount
	}
}

