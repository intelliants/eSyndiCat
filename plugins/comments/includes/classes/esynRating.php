<?php
//##copyright##

class esynRating extends eSyndiCat
{
	var $mTable = 'votes';

	function esynRating()
	{
		parent::eSyndiCat();
	}

	/**
	* Checks if a link was already voted
	*
	* @param str $aIp vote ip
	* @param int $aId link id
	*
	* @return int
	*/
	function isVoted($aIp, $aId)
	{
		return $this->exists("`listing_id` = '".$aId."' AND `ip_address` = '".$aIp."' AND (TO_DAYS(NOW()) - TO_DAYS(`date`)) <= " . $this->mConfig['rate_period']);
	}

	/**
	* Adds vote record
	*
	* @param int $aId link id
	* @param int $aVote vote rank
	*/
	function insert($aId, $aVote, $aIp)
	{
		$sql = "INSERT INTO `{$this->mPrefix}votes` ";
		$sql .= "(`listing_id`, `vote_value`, `ip_address`, `date`) ";
		$sql .= "VALUES ('{$aId}', '{$aVote}', '{$aIp}', NOW())";
		$this->query($sql);

        // Update link rating info
        $sql = 'SELECT COUNT(`vote_value`) `num_votes`, AVG(`vote_value`) `rating`, MIN(`vote_value`) `min_rating`, MAX(`vote_value`) `max_rating` ';
        $sql .= 'FROM `'.$this->mPrefix.'votes` ';
        $sql .= 'WHERE `listing_id` = \''.$aId.'\'';
        $vote = $this->getRow($sql);

        $sql = 'UPDATE `'.$this->mPrefix.'listings` SET `num_votes` = \''.$vote['num_votes'].'\', `rating` = \''.$vote['rating'].'\', `min_rating` = \''.$vote['min_rating'].'\', `max_rating` = \''.$vote['max_rating'].'\' ';
        $sql .= 'WHERE `id` = \''.$aId.'\'';
        $this->query($sql);
	}
	/**
	* Return listing rating by id
	*
	* @param int $aId listing id
	*/
	function getRating($aId)
	{
		$sql = "SELECT `num_votes`, `rating` ";
		$sql .= "FROM `{$this->mPrefix}listings` ";
		$sql .= "WHERE `id` = '{$aId}'";

		return $this->getRow($sql);
	}  
}
