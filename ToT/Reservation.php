<?php

/**
 * Model reservation objects and provide storage/retrieval from the database.
 *
 * Derived from User class DRyft project by Errol Sayre
 */

namespace ToT;

class Reservation
{

	/**
	 * Reservation ID (PrimaryKey)
	 * @type int
	 */
	protected $reservation_id;

	/**
	 * Starting time for reservations
	 * @type int
	 */
	protected $startHour;

	/**
	 * Number of possible reservation time slots
	 * @type int
	 */
	protected $timeBlocks;

	/**
	 * Interval for possible reservation times in minutes
	 * @type int
	 */
	protected $timeInterval;

	/**
	 * Average reservation length in minutes
	 * @type int
	 */
	protected $averageReservationLength;

	/**
	 * Maximum number of customers
	 * @type int
	 */
	protected $maxCapacity;

	/**
	 * User ID
	 * @type int
	 */
	protected $user_id;

	/**
	 * Reservation Time: Coded as a non negative int to facilitate simple arithmetic
	 * @type int
	 */
	public $reservation_time;

	/**
	 * Reservation Size
	 * @type int
	 */
	public $reservation_size;

	/**
	 * Waiter ID
	 * @type int
	 */
	public $waiter_id;


	/**
	 * Constructor
	 * @return ToT\Reservation
	 */
	public function __construct(
		int $reservation_id = 0,
		int $user_id = 0,
		int $reservation_time = 0,
		int $total_people = 0,
		int $waiter_id = null,
		int $timeBlocks = 26,
		int $timeInterval = 30,
		int $averageReservationLength = 90,
		int $startHour = 8,
		int $maxCapacity = 100
	) {
		$this->reservation_id           = $reservation_id;
		$this->user_id     = $user_id;
		$this->reservation_time    = $reservation_time;
		$this->total_people     = $total_people;
		$this->waiter_id = $waiter_id;
		$this->timeBlocks  = $timeBlocks;
		$this->timeInterval = $timeInterval;
		$this->averageReservationLength = $averageReservationLength;
		$this->startHour = $startHour;
		$this->maxCapacity = $maxCapacity;
	}


	//Get
	/**
	 * @return int
	 */
	public function resID()
	{
		return $this->reservation_id;
	}
	/**
	 * @return int
	 */
	public function useID()
	{
		return $this->user_id;
	}
	/**
	 * @return int
	 */
	public function resTime()
	{
		return $this->reservation_time;
	}
	/**
	 * @return int
	 */
	public function totalPeople()
	{
		return $this->total_people;
	}
	/**
	 * @return int
	 */
	public function waiterID()
	{
		return $this->waiter_id;
	}

	/**
	 * @return int
	 */
	public function timeInterval()
	{
		return $this->timeInterval;
	}

	/**
	 * @return int
	 */
	public function timeBlocks()
	{
		return $this->timeBlocks;
	}

	/**
	 * @return int
	 */
	public function averageReservationLength()
	{
		return $this->averageReservationLength;
	}

	/**
	 * @return int
	 */
	public function startHour()
	{
		return $this->startHour;
	}

	/**
	 * @return int
	 */
	public function maxCapacity()
	{
		return $this->maxCapacity;
	}




	/**
	 */
	public function setUseID($userID)
	{
		$this->user_id = $userID;
	}
	/**
	 */
	public function setTotalPeople($tp)
	{
		$this->total_people = $tp;
	}
	/**
	 */
	public function setResTime($time)
	{
		$this->reservation_time = $time;
	}

	/**
	 * Get total cost of a reservation
	 */
	public function getTotalReservationCost()
	{
		// Grab a copy of the database connection
		$db = Database\Connection::getConnection();

		$select = 'SELECT SUM(cost) FROM `orders` WHERE `RESERVATION_ID` = ' . $this->resID() . ' AND `complete` = 0 ;';


		// confirm the query worked
		if (($result = $db->query($select)) === false) {
			// TODO: replace a simple error with an exception
			throw new Database\Exception('DB Query Failed (getTotalReservationCost): ' . $db->error);
		}
		return ($result->fetch_row());
	}


	/**
	 * Converts a reservation time block back to normal Hour:Minute format
	 */
	public function convertToStandardTime($time)
	{
		return ($this->startHour + ($time * ($this->timeInterval) / 60));
	}

	/**
	 * Converts the current time into a timeblock (ceiling)
	 * returns 0 if before the opening time
	 * returns max+1 if after closing time
	 */
	public function currentTimeToTimeBlock()
	{
		date_default_timezone_set('America/Chicago');
		$hour = date("H");
		$min = 0;
		if (date("i") < $this->timeInterval) {
			$min = .5;
		} else {
			$min = 1;
		}

		$timeblock = (1 + (($hour + $min - $this->startHour) / (($this->timeInterval) / 60)));
		if ($timeblock < 1) {
			return 0;
		} elseif ($timeblock > $this->timeBlocks) {
			return $this->timeBlocks + 1;
		} else {
			return $timeblock;
		}
	}

	/**
	 * returns true if below the defined delete threshold, could use a more descriptive name
	 */
	public function deleteThreshold()
	{
		$deletionThreshold = 2;
		date_default_timezone_set('America/Chicago');
		$currentHour = intval(date("H"));
		if ($this->convertToStandardTime($this->reservation_time) - $currentHour > $deletionThreshold) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 */
	public function newReservation(int $userID, int $resTime, int $partySize)
	{
		$this->user_id = $userID;
		$this->reservation_time = $resTime;
		$this->total_people = $partySize;
		$this->save();
	}

	/**
	 * Store the object to the database.
	 *
	 * @return boolean
	 */
	public function save()
	{
		// insert/update the record in the database
		// get a reference to the database

		$db = Database\Connection::getConnection();

		// determine if this is an insert or update
		if ($this->resID() != 0) {
			$query = 'UPDATE `reservations`' . PHP_EOL
				. 'SET ' . PHP_EOL
				. '  `USER_ID` = "'              . intval($this->user_id)     . '",' . PHP_EOL
				. '  `reservation_time` = "'     . intval($this->reservation_time)         . '",' . PHP_EOL
				. '  `total_people` = "'         . intval($this->total_people) . '",' . PHP_EOL
				. '  `WAITER_ID` = "'            . intval($this->waiter_id)    . '"' . PHP_EOL
				. 'WHERE `RESERVATION_ID` = '    . intval($this->reservation_id) . ';';
			if ($db->query($query) === false) {
				throw new Database\Exception('Unable to save reservation: ' . $db->error . PHP_EOL . '<pre>' . $query . '</pre>');
			}
		} else {

			$query = 'INSERT INTO `reservations` (' . PHP_EOL
				. '  `USER_ID`,' . PHP_EOL
				. '  `reservation_time`,' . PHP_EOL
				. '  `total_people`,' . PHP_EOL
				. '  `WAITER_ID`' . PHP_EOL
				. ') VALUES (' . PHP_EOL
				. '  "' . intval($this->user_id)     . '",' . PHP_EOL
				. '  "' . intval($this->reservation_time)         . '",' . PHP_EOL
				. '  "' . intval($this->total_people) . '",' . PHP_EOL
				. '  "' . intval($this->waiter_id)     . '"' . PHP_EOL
				. ');';
			if ($db->query($query) !== false) {
				// try to read the reservation id back
				$this->reservation_id = $db->insert_id;
			} else {
				throw new Database\Exception('Unable to insert reservation: ' . $db->error . PHP_EOL . '<pre>' . $query . '</pre>');
			}
		}

		return true;
	}



	/**
	 * Load a reservation by reservation id
	 *
	 * @param int $reservationID
	 * @return mixed
	 */
	public static function getReservationByReservationID(int $reservationID)
	{

		$select = 'SELECT * FROM `reservations` WHERE `RESERVATION_ID` = "'
			. intval($reservationID) . '";';

		return self::loadReservationByQuery($select);
	}

	/**
	 * Load a reservation by user id
	 *
	 * @param int $userID
	 * @return mixed
	 */
	public static function getReservationByUserID(int $userID)
	{

		$select = 'SELECT * FROM `reservations` WHERE `USER_ID` = "'
			. intval($userID) . '";';

		return self::loadReservationByQuery($select);
	}

	/**
	 * Load reservations by wait id
	 *
	 * @param int $userID
	 * @return mixed
	 */
	public static function getReservationsByWaiterID(int $waitID)
	{

		$select = 'SELECT * FROM `reservations` WHERE `WAITER_ID` = "'
			. intval($waitID) . '";';

		return self::loadReservationsByQuery($select);
	}

	/**
	 * Check DB to see if a reservation exists for some user
	 *
	 * @param int $userID
	 * @return boolean
	 */
	public static function reservationExistenceByUserID(int $userID)
	{
		// Grab a copy of the database connection
		$db = Database\Connection::getConnection();

		$select = 'SELECT * FROM `reservations` WHERE `USER_ID` = "'
			. intval($userID) . '";';

		// confirm the query worked
		if (($result = $db->query($select)) === false) {
			// TODO: replace a simple error with an exception
			throw new Database\Exception('DB Query Failed (Reservations): ' . $db->error);
		}

		if ($result->fetch_row() == null) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Add user to a reservation
	 *
	 * @param int $reservationID
	 * @param int $userID
	 */
	public static function addUserToReservation(int $reservationID, int $userID)
	{

		// Grab a copy of the database connection
		$db = Database\Connection::getConnection();

		$select = 'INSERT INTO `reservation_users` (RESERVATION_ID, USER_ID) VALUES ('
			. intval($reservationID) . ',' . intval($userID) . ');';

		// confirm the query worked
		if (($result = $db->query($select)) === false) {
			// TODO: replace a simple error with an exception
			throw new Database\Exception('DB Query Failed (Reservations): ' . $db->error);
		}
	}

	/**
	 * Delete a reservation by user id
	 *
	 * @param int $userID
	 * @return mixed
	 */
	public static function deleteReservationByUserID(int $userID)
	{

		// Grab a copy of the database connection
		$db = Database\Connection::getConnection();

		$select = 'DELETE FROM `reservations` WHERE `USER_ID` = "'
			. intval($userID) . '";';

		// confirm the query worked
		if (($result = $db->query($select)) === false) {
			// TODO: replace a simple error with an exception
			throw new Database\Exception('Delete Reservation failed: ' . $db->error);
		}
	}

	/**
	 * Load all reservations from the database
	 *
	 * @return array
	 */
	public static function getReservations()
	{
		return self::loadReservationsByQuery(
			'SELECT * FROM `reservations` ORDER BY RESERVATION_ID;'
		);
	}

	/**
	 * Get number of patrons during the specificed time
	 *
	 * @return array
	 */
	public static function getCapacity($resTime)
	{

		// Grab a copy of the database connection
		$db = Database\Connection::getConnection();

		if ($resTime == 1) {

			$select = 'SELECT SUM(`total_people`) FROM `reservations` WHERE `reservation_time` = 1;';

			if (($result = $db->query($select)) === false) {
				throw new Database\Exception('DB Query Failed (Reservations): ' . $db->error);
			}


			return $result->fetch_row();
		} elseif ($resTime == 2) {

			$select = 	'SELECT SUM(`total_people`) FROM `reservations` WHERE `reservation_time` BETWEEN 1 AND 2 ;';

			if (($result = $db->query($select)) === false) {
				throw new Database\Exception('DB Query Failed (Reservations): ' . $db->error);
			}

			return $result->fetch_row();
		} else {

			$select = 	'SELECT SUM(`total_people`) FROM `reservations` WHERE `reservation_time` BETWEEN ' . ($resTime - 2) . ' AND ' . $resTime . ';';

			if (($result = $db->query($select)) === false) {
				throw new Database\Exception('DB Query Failed (Reservations): ' . $db->error);
			}

			return $result->fetch_row();
		}
	}

	/**
	 * Load all unassigned reservations from the database
	 *
	 * @return array
	 */
	public static function getUnassignedReservations()
	{
		return self::loadReservationsByQuery(
			'SELECT * FROM `reservations` WHERE `WAITER_ID` IS NULL ORDER BY RESERVATION_ID;'
		);
	}

	/**
	 * Load all assigned reservations from the database
	 *
	 * @return array
	 */
	public static function getAssignedReservations()
	{
		return self::loadReservationsByQuery(
			'SELECT * FROM `reservations` WHERE `WAITER_ID` IS NOT NULL ORDER BY RESERVATION_ID;'
		);
	}

	/**
	 * Assign a reservation to a waiter              
	 *
	 * @param int $waitID
	 * @param int $reservationID
	 * 
	 */
	public static function assignReservation($waitID, $reservationID) //create order for every customer
	{
		if ($waitID == NULL) {
			$db = Database\Connection::getConnection();
			$db->query('UPDATE reservations SET `WAITER_ID` = NULL WHERE `RESERVATION_ID` = ' . $reservationID . ';');

			$db->query('DELETE FROM `orders` WHERE `RESERVATION_ID` = ' . $reservationID . ';');
			
		} else {
			$res = self::getReservationByReservationID($reservationID);
			$res->waiter_id = $waitID;
			$res->save();

			$db = Database\Connection::getConnection();
			$result = $db->query('SELECT `USER_ID` FROM `reservation_users` WHERE `RESERVATION_ID` = ' . $reservationID . ';');

			//load all customers linked to a reservation
			 
			//for every customer create an order
			$customers = $result->fetch_row();
			foreach($customers as $customer){
				$db->query('INSERT INTO `orders` (`RESERVATION_ID` , `USER_ID`, `cost`,`complete`) VALUES  
				('. $reservationID .','.$customer.',0,0);');
			}
		}
	}

	/**
	 * Execute a single select
	 *
	 * @param string $query
	 * @return reservation
	 */
	protected static function loadReservationByQuery(string $select)
	{
		// use the multi-select to load matching users
		$reservations = self::loadReservationsByQuery($select);

		// confirm the result set size
		$count = count($reservations);
		if ($count > 1) {
			// We must have just one result
			throw new Database\Exception('Single Reservation Lookup Failed: returned ' . count($reservations) . ' rows.');
		} elseif (!$count) {
			// No results found
			throw new Database\Exception('Single Reservation Lookup Failed: no match found.');
		}

		// pop off the single result
		return array_shift($reservations);
	}

	/**
	 * Load multiple users from a query
	 *
	 * @param int $query
	 * @return array
	 */
	protected static function loadReservationsByQuery(string $select)
	{
		// Setup a dummy return value
		$reservations = [];

		// Grab a copy of the database connection
		$db = Database\Connection::getConnection();

		// confirm the query worked
		if (($result = $db->query($select)) === false) {
			// TODO: replace a simple error with an exception
			throw new Database\Exception('DB Query Failed (Reservations): ' . $db->error);
		}

		// load and convert each result object
		while (($data = $result->fetch_object()) !== null) {
			$reservations[] = self::objectForRow($data);
		}

		// convert the resulting object
		return $reservations;
	}

	/**
	 * Convert a MySQL row object to a Reservation
	 *
	 * @param object
	 * @return reservation
	 */
	public static function objectForRow($data)
	{
		$reservation = new Reservation(
			$data->RESERVATION_ID,
			$data->USER_ID,
			$data->reservation_time,
			$data->total_people,
			$data->WAITER_ID
		);

		return $reservation;
	}
}
