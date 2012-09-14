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


/////////////////////
// FUNCTIONS START //
define("C_MINUTE",	1);
define("C_HOUR",	2);
define("C_DOM",		3); // day of month
define("C_MONTH",	4);
define("C_DOW",		5); // day of week
define("C_CMD",		7);
define("C_COMMENT",	8);
define("C_CRONLINE",	20);

/**
 * Parse string that looks like common cron line
 *
 * @param string $aStr string from cron file
 * @return array
 */
function parseCron($aStr)
{
	$regex = "~^([-0-9,/*]+)\\s+([-0-9,/*]+)\\s+([-0-9,/*]+)\\s+([-0-9,/*]+)\\s+([-0-7,/*]+|(-|/|Sun|Mon|Tue|Wed|Thu|Fri|Sat)+)\\s+([^#]*)\\s*(#.*)?$~i";
	if (preg_match($regex, $aStr, $job))
	{
		if ($job[C_DOW][0] != '*' AND !is_numeric($job[C_DOW]))
		{
			$job[C_DOW] = str_replace(
				Array("Sun","Mon","Tue","Wed","Thu","Fri","Sat"),
				Array(0,1,2,3,4,5,6),
				$job[C_DOW]);
		}
		$job[C_CMD] = trim($job[C_CMD]);
		$job[C_COMMENT] = isset($job[C_COMMENT]) ? trim(substr($job[C_COMMENT],1)) : false;
		$job[C_CRONLINE] = $aStr;

		$job["lastScheduled"] = getLastScheduledRunTime($job);
	}
	return $job;
}

function getLastScheduledRunTime($job)
{
	$extjob = Array();
	parseElement($job[C_MINUTE], $extjob[C_MINUTE], 60);
	parseElement($job[C_HOUR], $extjob[C_HOUR], 24);
	parseElement($job[C_DOM], $extjob[C_DOM], 31);
	parseElement($job[C_MONTH], $extjob[C_MONTH], 12);
	parseElement($job[C_DOW], $extjob[C_DOW], 7);

	$dateArr = getdate();
	$minutesAhead = 0;
	while (
		$minutesAhead<525600 AND
		(!$extjob[C_MINUTE][$dateArr["minutes"]] OR
		!$extjob[C_HOUR][$dateArr["hours"]] OR
		(!$extjob[C_DOM][$dateArr["mday"]] OR !$extjob[C_DOW][$dateArr["wday"]]) OR
		!$extjob[C_MONTH][$dateArr["mon"]])
	) {
		if (!$extjob[C_DOM][$dateArr["mday"]] OR !$extjob[C_DOW][$dateArr["wday"]]) {
			incDate($dateArr,1,"mday");
			$minutesAhead+=1440;
			continue;
		}
		if (!$extjob[C_HOUR][$dateArr["hours"]]) {
			incDate($dateArr,1,"hour");
			$minutesAhead+=60;
			continue;
		}
		if (!$extjob[C_MINUTE][$dateArr["minutes"]]) {
			incDate($dateArr,1,"minute");
			$minutesAhead++;
			continue;
		}
	}

	return mktime($dateArr["hours"],$dateArr["minutes"],0,$dateArr["mon"],$dateArr["mday"],$dateArr["year"]);
}

function parseElement($element, &$targetArray, $numberOfElements)
{
	$subelements = explode(",",$element);
	for ($i=0; $i < $numberOfElements; $i++)
	{
		$targetArray[$i] = $subelements[0] == "*";
	}

	for ($i=0;$i<count($subelements);$i++) {
		if (preg_match("~^(\\*|([0-9]{1,2})(-([0-9]{1,2}))?)(/([0-9]{1,2}))?$~",$subelements[$i],$matches)) {
			if ($matches[1] == "*")
			{
				$matches[2] = 0;		// from
				$matches[4] = $numberOfElements;		//to
			}
			elseif (empty($matches[4]))
			{
				$matches[4] = $matches[2];
			}
			if (empty($matches[5]) OR $matches[5][0] != "/")
			{
				$matches[6] = 1;		// step
			}
			for ($j = lTrimZeros($matches[2]); $j <= lTrimZeros($matches[4]); $j += lTrimZeros($matches[6]))
			{
				$targetArray[$j] = TRUE;
			}
		}
	}
}

function lTrimZeros($number) {
	while ($number[0]=='0') {
		$number = substr($number,1);
	}
	return $number;
}

function incDate(&$dateArr, $amount, $unit)
{
	if ($unit=="mday") {
		$dateArr["hours"] = 0;
		$dateArr["minutes"] = 0;
		$dateArr["seconds"] = 0;
		$dateArr["mday"] += $amount;
		$dateArr["wday"] += $amount % 7;
		if ($dateArr["wday"]>6) {
			$dateArr["wday"]-=7;
		}

		$months28 = Array(2);
		$months30 = Array(4,6,9,11);
		$months31 = Array(1,3,5,7,8,10,12);

		if (
			(in_array($dateArr["mon"], $months28) && $dateArr["mday"]==28) ||
			(in_array($dateArr["mon"], $months30) && $dateArr["mday"]==30) ||
			(in_array($dateArr["mon"], $months31) && $dateArr["mday"]==31)
		) {
			$dateArr["mon"]++;
			$dateArr["mday"] = 1;
		}

	} elseif ($unit=="hour") {
		if ($dateArr["hours"]==23) {
			incDate($dateArr, 1, "mday");
		} else {
			$dateArr["minutes"] = 0;
			$dateArr["seconds"] = 0;
			$dateArr["hours"]++;
		}
	} elseif ($unit=="minute") {
		if ($dateArr["minutes"]==59) {
			incDate($dateArr, 1, "hour");
		} else {
			$dateArr["seconds"] = 0;
			$dateArr["minutes"]++;
		}
	}
}


/**
 * Execute cron job
 *
 * @param int $aCronId cron job ID
 */
function exec_cron($aCronId = false)
{
	global $eSyndiCat;
	$eSyndiCat->setTable('cron');
	$job = $eSyndiCat->row('*', '`active`=1 AND `nextrun`<=UNIX_TIMESTAMP() ORDER BY `nextrun` ');
	if (empty($job)) return false;

	$data = parseCron($job['data']);
	$upd = $eSyndiCat->query("
		UPDATE `{$eSyndiCat->mPrefix}cron`
		SET `nextrun`={$data['lastScheduled']}
		WHERE `id`={$job['id']} AND `nextrun`={$job['nextrun']} "
	);

	if ($upd)
	{
		include(ESYN_HOME . $data[C_CMD]);
	}
}
// FUNCTIONS END      /////
///////////////////////////


require('./includes/header-lite.php');

ignore_user_abort(1);
@set_time_limit(0);

$img = base64_decode('R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==');

header('Content-type: image/gif');
echo $img;
flush();

exec_cron();

