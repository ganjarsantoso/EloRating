<?php

if (version_compare(PHP_VERSION, '5.3.0', '<')) {
    exit('Sorry, this script does not run on a PHP version smaller than 5.3.0 !');
}

require_once('class/elorating.class.php');


$elo = new EloRating();

if ($elo->isUserSubmitted() == true) {
	include('views/top.php');
} else {
	$elo->resetTempPlayerStats();
	
	if ($elo->countMembers() == 0) {
		header('location:addmember.php');
	} else {
		while ($elo->randomID_A == $elo->randomID_B) {
			$elo->randomID_A = $elo->getRandomID();
			$elo->randomID_B = $elo->getRandomID();
		}
		
		$elo->getPlayerStats($elo->randomID_A);
		$elo->PlayerStatsA = array_replace($elo->PlayerStatsA, $elo->PlayerStats);
		
		$elo->resetTempPlayerStats();
		
		$elo->getPlayerStats($elo->randomID_B);
		$elo->PlayerStatsB = array_replace($elo->PlayerStatsB, $elo->PlayerStats);
	}

	include('views/form.php');
}