<?php
/******************************************************************************
 *
 *	 COMPANY: Intelliants LLC
 *	 PROJECT: eSyndiCat Directory Software
 *	 VERSION: 1.7 [Cushy]
 *	 LISENSE: http://www.esyndicat.com/license.html
 *	 http://www.esyndicat.com/
 *
 *	 This program is a limited version. It does not include the major part of 
 *	 the functionality that comes with the paid version. You can purchase the
 *	 full version here: http://www.esyndicat.com/order.html
 *
 *	 Any kind of using this software must agree to the eSyndiCat license.
 *
 *	 Link to eSyndiCat.com may not be removed from the software pages without
 *	 permission of the eSyndiCat respective owners.
 *
 *	 This copyright notice may not be removed from source code in any case.
 *
 *	 Useful links:
 *	 Installation Manual:	http://www.esyndicat.com/docs/install.html
 *	 eSyndiCat User Forums: http://www.esyndicat.com/forum/
 *	 eSyndiCat Helpdesk:	http://www.esyndicat.com/desk/
 *
 *	 Intelliants LLC
 *	 http://www.esyndicat.com
 *	 http://www.intelliants.com
 *
 ******************************************************************************/


/**
 * esynRating 
 * 
 * @uses eSyndiCat
 * @package 
 * @version $id$
 */
class esynRating extends eSyndiCat
{
	/**
	 * mTable 
	 * 
	 * @var string
	 * @access public
	 */
	var $mTable = 'votes';

	/**
	 * esynRating 
	 * 
	 * @access public
	 * @return void
	 */
	function esynRating()
	{
		parent::eSyndiCat();
	}

	/**
	 * isVoted 
	 *
	 * Checks if a link was already voted
	 * 
	 * @param str $aIp vote ip
	 * @param int $aId link id
	 * @access public
	 * @return void
	 */
	function isVoted($aIp, $aId)
	{
		$config = &esynConfig::instance();
		$period = $config->get("rate_period");

		return $this->exists("`listing_id` = '".$aId."' AND `ip_address` = '".$aIp."' AND (TO_DAYS(NOW()) - TO_DAYS(`date`)) <= ".$period);
	}

	/**
	* Adds vote record
	*
	* @param int $aId link id
	* @param int $aVote vote rank
	*/
	/**
	 * insert 
	 *
	 * Adds vote record
	 * 
	 * @param int $aId link id
	 * @param int $aVote vote rank 
	 * @param str $aIp vote ip 
	 * @access public
	 * @return void
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
	 * getRating
	 *
	 * Return listing rating by id 
	 * 
	 * @param int $aId listing id 
	 * @access public
	 * @return void
	 */
	function getRating($aId)
	{
		$sql = "SELECT `num_votes`, `rating` ";
		$sql .= "FROM `{$this->mPrefix}listings` ";
		$sql .= "WHERE `id` = '{$aId}'";

		return $this->getRow($sql);
	}  
}
