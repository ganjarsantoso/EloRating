<?php

/**
 * handles the elo rating system to use in a facemash-like website
 * @author Ganjar Santoso
 * @link http://www.twitter.com/ganjarsantoso
 * @link https://github.com/ganjarsantoso/EloRating
 * @license http://opensource.org/licenses/MIT MIT License
 */

class EloRating
{
	/**
	 * @var array $PlayerStats Temporary player status datas on an array
	 */
	public $PlayerStats = array();
	/**
	 * @var array $PlayerStatsA Player A datas on an array
	 */
	public $PlayerStatsA = array('nama' => '', 'URLfoto' => '', 'EloPoint' => 0, 'P' => 0, 'W' => 0, 'D' => 0, 'L' => 0);
	/**
	 * @var array $PlayerStatsB Player B datas on an array
	 */
	public $PlayerStatsB = array('nama' => '', 'URLfoto' => '', 'EloPoint' => 0, 'P' => 0, 'W' => 0, 'D' => 0, 'L' => 0);
	/**
	 * @var int $randomID_A Player A ID
	 */
	public $randomID_A = 0;
	/**
	 * @var int $randomID_B Player B ID
	 */
	public $randomID_B = 0;
	/**
	 * @var object @db_connection Database connection
	 */		
	public $db_connection = null;
	/**
	 * @var boolean $user_submitted The user submitted status
	 */
	public $user_submitted = false;
	/**
	 * @var string $DB_host Database host connection
	 */
	protected $DB_host = 'localhost';
	/**
	 * @var string $DB_name Database name connection
	 */
	protected $DB_name = 'elorating';
	/**
	 * @var string $DB_user Database user connection
	 */
	protected $DB_user = 'root';
	/**
	 * @var string $DB_pass Database password connection
	 */
	protected $DB_pass = '';
	
	/**
     * the function "__construct()" automatically starts whenever an object of this class is created,
     * you know, when you do "$elo = new EloRating();"
     */
	public function __construct()
	{
		// check the possibility action:
		// 1. User submitted the form by clicking between the pics or the button
		// 2. User just open the web and not doing anything
		
		// if user submitted the form (clicking pics or button)
		if (isset($_POST['pilihan1']) || isset($_POST['pilihan2']) || isset($_POST['pilihan3'])) {
			// set user submitted to true
			$this->user_submitted = true;
			
			// normalize the temporary data
			$this->resetTempPlayerStats();
			
			// check if IDs are ready to process
			if (isset($_POST['id_A']) && isset($_POST['id_B'])) {
				// collect player A datas from database
				$this->getPlayerStats($_POST['id_A']);
				$this->PlayerStatsA = array_replace($this->PlayerStatsA, $this->PlayerStats);
				
				// normalize the temporary data
				$this->resetTempPlayerStats();
				
				// collect player B datas from database
				$this->getPlayerStats($_POST['id_B']);
				$this->PlayerStatsB = array_replace($this->PlayerStatsB, $this->PlayerStats);
				
				// if picture 1 clicked
				if (isset($_POST['pilihan1'])) {
					$this->resetTempPlayerStats();
					$this->getPlayerStats($_POST['id_A']);
					$this->updatePlayerStats($_POST['id_A'], $this->ELORating($this->PlayerStatsA['EloPoint'], 1), 1, 1, 0, 0);
					
					$this->resetTempPlayerStats();
					$this->getPlayerStats($_POST['id_B']);
					$this->updatePlayerStats($_POST['id_B'], $this->ELORating($this->PlayerStatsB['EloPoint'], 0), 1, 0, 0, 1);
				// if picture 2 clicked
				} elseif (isset($_POST['pilihan2'])) {
					$this->resetTempPlayerStats();
					$this->getPlayerStats($_POST['id_A']);
					$this->updatePlayerStats($_POST['id_A'], $this->ELORating($this->PlayerStatsA['EloPoint'], 0), 1, 0, 0, 1);
					
					$this->resetTempPlayerStats();
					$this->getPlayerStats($_POST['id_B']);
					$this->updatePlayerStats($_POST['id_B'], $this->ELORating($this->PlayerStatsB['EloPoint'], 1), 1, 1, 0, 0);
				// if button clicked
				} elseif (isset($_POST['pilihan3'])) {
					$this->resetTempPlayerStats();
					$this->getPlayerStats($_POST['id_A']);
					$this->updatePlayerStats($_POST['id_A'], $this->ELORating($this->PlayerStatsA['EloPoint'], 0.5), 1, 0, 1, 0);
					
					$this->resetTempPlayerStats();
					$this->getPlayerStats($_POST['id_B']);
					$this->updatePlayerStats($_POST['id_B'], $this->ELORating($this->PlayerStatsB['EloPoint'], 0.5), 1, 0, 1, 0);
				}
			}
		// if user doing something else other than submitted form
		} else {
			// set user submitted to false
			$this->user_submitted = false;			
		}
	}
	
	/**
	 * Get expectation value for each player
	 * the expectation value lies between 0 - 1
	 * see: http://en.wikipedia.org/wiki/Elo_rating_system
	 */
	private function getEkspektasi() {
		$Qa = exp(($this->PlayerStatsA['EloPoint']/400)*log(10));
		$Qb = exp(($this->PlayerStatsB['EloPoint']/400)*log(10));
		
		return $Qa/($Qa+$Qb);
	}
	
	/**
	 * A constant K factor for different rating point
	 * this values based on USCF K factor
	 * see: http://www.uschess.org/content/view/12201/141/
	 */
	private function Kfactor($CurrentRatePlayer) 
	{
		if ($CurrentRatePlayer >= 2360) {
			$K = 14.81;
		} elseif(($CurrentRatePlayer >= 2340) && ($CurrentRatePlayer < 2360)) {
			$K = 15.15;
		} elseif(($CurrentRatePlayer >= 2320) && ($CurrentRatePlayer < 2340)) {
			$K = 15.61;
		} elseif(($CurrentRatePlayer >= 2300) && ($CurrentRatePlayer < 2320)) {
			$K = 16.09;
		} elseif(($CurrentRatePlayer >= 2280) && ($CurrentRatePlayer < 2300)) {
			$K = 16.59;
		} elseif(($CurrentRatePlayer >= 2260) && ($CurrentRatePlayer < 2280)) {
			$K = 17.11;
		} elseif(($CurrentRatePlayer >= 2240) && ($CurrentRatePlayer < 2260)) {
			$K = 17.64;
		} elseif(($CurrentRatePlayer >= 2220) && ($CurrentRatePlayer < 2240)) {
			$K = 18.18;
		} elseif(($CurrentRatePlayer >= 2200) && ($CurrentRatePlayer < 2220)) {
			$K = 18.73;
		} elseif(($CurrentRatePlayer >= 2150) && ($CurrentRatePlayer < 2200)) {
			$K = 20.14;
		} elseif(($CurrentRatePlayer >= 2100) && ($CurrentRatePlayer < 2150)) {
			$K = 21.59;
		} elseif(($CurrentRatePlayer >= 2050) && ($CurrentRatePlayer < 2100)) {
			$K = 23.05;
		} elseif(($CurrentRatePlayer >= 2000) && ($CurrentRatePlayer < 2050)) {
			$K = 24.53;
		} elseif(($CurrentRatePlayer >= 1950) && ($CurrentRatePlayer < 2000)) {
			$K = 26.02;
		} elseif(($CurrentRatePlayer >= 1900) && ($CurrentRatePlayer < 1950)) {
			$K = 27.5;
		} elseif(($CurrentRatePlayer >= 1850) && ($CurrentRatePlayer < 1900)) {
			$K = 28.97;
		} elseif(($CurrentRatePlayer >= 1800) && ($CurrentRatePlayer < 1850)) {
			$K = 30.43;
		} elseif(($CurrentRatePlayer >= 1750) && ($CurrentRatePlayer < 1800)) {
			$K = 31.88;
		} elseif(($CurrentRatePlayer >= 1700) && ($CurrentRatePlayer < 1750)) {
			$K = 33.32;
		} elseif(($CurrentRatePlayer >= 1650) && ($CurrentRatePlayer < 1700)) {
			$K = 34.74;
		} elseif(($CurrentRatePlayer >= 1600) && ($CurrentRatePlayer < 1650)) {
			$K = 36.14;
		} elseif(($CurrentRatePlayer >= 1550) && ($CurrentRatePlayer < 1600)) {
			$K = 37.53;
		} elseif(($CurrentRatePlayer >= 1500) && ($CurrentRatePlayer < 1550)) {
			$K = 38.89;
		} elseif(($CurrentRatePlayer >= 1450) && ($CurrentRatePlayer < 1500)) {
			$K = 40.24;
		} elseif(($CurrentRatePlayer >= 1400) && ($CurrentRatePlayer < 1450)) {
			$K = 41.58;
		} elseif(($CurrentRatePlayer >= 1350) && ($CurrentRatePlayer < 1400)) {
			$K = 42.89;
		} elseif(($CurrentRatePlayer >= 1300) && ($CurrentRatePlayer < 1350)) {
			$K = 44.18;
		} elseif(($CurrentRatePlayer >= 1250) && ($CurrentRatePlayer < 1300)) {
			$K = 45.46;
		} elseif(($CurrentRatePlayer >= 1200) && ($CurrentRatePlayer < 1250)) {
			$K = 46.71;
		} elseif(($CurrentRatePlayer >= 1150) && ($CurrentRatePlayer < 1200)) {
			$K = 47.95;
		} elseif(($CurrentRatePlayer >= 1100) && ($CurrentRatePlayer < 1150)) {
			$K = 49.17;
		} elseif(($CurrentRatePlayer >= 1050) && ($CurrentRatePlayer < 1100)) {
			$K = 50.38;
		} elseif(($CurrentRatePlayer >= 1000) && ($CurrentRatePlayer < 1050)) {
			$K = 51.56;
		} elseif(($CurrentRatePlayer >= 950) && ($CurrentRatePlayer < 1000)) {
			$K = 52.73;
		} elseif(($CurrentRatePlayer >= 900) && ($CurrentRatePlayer < 950)) {
			$K = 53.88;
		} elseif(($CurrentRatePlayer >= 800) && ($CurrentRatePlayer < 900)) {
			$K = 56.13;
		} elseif(($CurrentRatePlayer >= 700) && ($CurrentRatePlayer < 800)) {
			$K = 58.32;
		} elseif(($CurrentRatePlayer >= 600) && ($CurrentRatePlayer < 700)) {
			$K = 60.45;
		} elseif(($CurrentRatePlayer >= 500) && ($CurrentRatePlayer < 600)) {
			$K = 62.51;
		} elseif(($CurrentRatePlayer >= 400) && ($CurrentRatePlayer < 500)) {
			$K = 64.51;
		} elseif(($CurrentRatePlayer >= 300) && ($CurrentRatePlayer < 400)) {
			$K = 66.46;
		} else {
			$K = 68.36;
		}
		return $K;
	}
	
	/**
	 * Calculate the elo point for every player
	 */
	private function ELORating($CurrentRatePlayer, $WinLose) 
	{
		return $this->Kfactor($CurrentRatePlayer)*($WinLose - $this->getEkspektasi());
	}
    /**
     * Checks if database connection is opened. If not, then this method tries to open it.
     */
    public function databaseConnection()
    {
        // if connection already exists
        if ($this->db_connection != null) {
            return true;
        } else {
            try {
                // Generate a database connection, using the PDO connector
                // see http://net.tutsplus.com/tutorials/php/why-you-should-be-using-phps-pdo-for-database-access/
                $this->db_connection = new PDO('mysql:host='. $this->DB_host .';dbname='. $this->DB_name . ';charset=utf8', $this->DB_user, $this->DB_pass);
                return true;
            } catch (PDOException $e) {
                die('Connection problem. ERROR detail: ' . $e->getMessage());
            }
        }
		return false;
    }
	
	/**
	 * Access the database to get player data
	 */
	public function getPlayerStats($member_id)
	{
		// if database connection opened
		if ($this->databaseConnection()) {
			// database query, getting all the info of the user selected
			$query_user = $this->db_connection->prepare('SELECT * FROM members WHERE id = :id');
			$datas = array(':id' => $member_id);
			$query_user->execute($datas);
			$row = $query_user->fetch();
			
			$replacement = array('nama' => $row['nama'], 'URLfoto' => $row['URLfoto'], 'EloPoint' => $row['EloPoint'], 'P' => $row['P'], 'W' => $row['W'], 'D' => $row['D'], 'L' => $row['L']);
			
			// add to temporary player data
			$this->PlayerStats = array_replace($this->PlayerStats, $replacement);
		} else {
			return false;
		}
	}
	
	/**
	 * Normalize temporary player data
	 */
	public function resetTempPlayerStats()
	{
		$this->PlayerStats = array('nama' => '', 'URLfoto' => '', 'EloPoint' => 0, 'P' => 0, 'W' => 0, 'D' => 0, 'L' => 0);
	}
	
	/**
	 * Access the database to update the player data
	 */
	private function updatePlayerStats($member_id, $PlayerRating, $P, $W, $D, $L)
	{
		// if database connection opened
		if ($this->databaseConnection()) {
			// update the data
			$PR = $this->PlayerStats['EloPoint'] + $PlayerRating;
			$P0 = $this->PlayerStats['P'] + $P;
			$W0 = $this->PlayerStats['W'] + $W;
			$D0 = $this->PlayerStats['D'] + $D;
			$L0 = $this->PlayerStats['L'] + $L;
			
			// database query, execute all to the database
			$query_user = $this->db_connection->prepare('UPDATE members SET EloPoint = :EloPoint, P = :P, W = :W, D = :D, L = :L WHERE id = :id');
			$datas = array(':EloPoint' => $PR, ':P' => $P0, ':W' => $W0, ':D' => $D0, ':L' => $L0, ':id' => $member_id);
			$query_user->execute($datas);
		} else {
			return false;
		}
	}
	
	/**
	 * Access the database to add another player/member
	 */
	public function addMember($nama_member, $url_foto)
	{
		// if database connection opened
		if ($this->databaseConnection()) {
			// database query, adding a new player and all player infos
			$query_user = $this->db_connection->prepare('INSERT INTO members (id, nama, URLfoto, EloPoint, P, W, D, L) value (:id, :nama, :URLfoto, :EloPoint, :P, :W, :D, :L)');
			$datas = array(':id' => null, ':nama' => $nama_member, ':URLfoto' => $url_foto, ':EloPoint' => 0, ':P' => 0, ':W' => 0, ':D' => 0, ':L' => 0);
			$query_user->execute($datas);
		} else {
			return false;
		}
	}
	
	/**
	 * Count how many player/members have been joined
	 */
	public function countMembers()
	{
		if ($this->databaseConnection()) {
			$query_user = $this->db_connection->query('SELECT * FROM members');
			return count($query_user->fetchAll());
		} else {
			return false;
		}
	}
	
	/**
	 * Check if the user has submitted the form
	 */
	public function isUserSubmitted()
	{
		return $this->user_submitted;
	}
	
	/**
	 * Get the random ID
	 */
	public function getRandomID()
	{
		return rand(1, $this->countMembers());
	}
	
	/**
	 * Just for fun function
	 * it show the beauty level based on elo point
	 */
	public function beautyLevel($elopoint)
	{
		if (($elopoint >=0) && ($elopoint < 200)) {
			$level = 'biasa ajah';
		} elseif (($elopoint >= 200) && ($elopoint < 500)) {
			$level = 'not bad lah';
		} elseif (($elopoint >= 500) && ($elopoint < 800)) {
			$level = 'lumayan';
		} elseif (($elopoint >= 800) && ($elopoint < 1200)) {
			$level = 'manis euy';
		} elseif (($elopoint >= 1200) && ($elopoint < 1500)) {
			$level = 'cantik';
		} elseif (($elopoint >= 1500) && ($elopoint < 1800)) {
			$level = 'gilaaaa';
		} elseif (($elopoint >= 1800) && ($elopoint < 2100)) {
			$level = 'dewaaa';
		} elseif (($elopoint >= 2100) && ($elopoint < 2400)) {
			$level = 'bidadari surga';
		} elseif (($elopoint >= 2400) && ($elopoint < 2800)) {
			$level = 'ohh god!';
		} elseif (($elopoint >= 2800) && ($elopoint < 3000)) {
			$level = 'perfecto!';
		} elseif ($elopoint >= 3000) {
			$level = 'alien!';
		} else {
			$level = 'mau muntah!';
		}
		return $level;
	}
}
		
