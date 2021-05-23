<?php

/**
 * 
 * @author Austin
 */

namespace ToT;

class Order
{
	/**
	 * Order identification number
	 *
	 * Protect this value to make it immutable.
	 *
	 * @type int
	 */
	protected $id;

	/**
	 * Reservation identification number 
	 *
	 * What reservation this order it related to
	 * Protect this value to make it immutable.
	 *
	 * @type int
	 */
	protected $reservationID;

	/**
	 * User identification number
	 *
	 * What user made this order
	 * Protect this value to make it immutable.
	 *
	 * @type int
	 */
	protected $userID;

	/**
	 * Cost of order
	 *
	 * Running total of all items in order
	 * Protect this value to make it immutable without the appropriate accessors.
	 *
	 * @type decimal
	 */
	protected $cost;

	/**
	 * Order completion flag, set after payment.
	 * @type bool
	 */
	protected $complete;


	/**
	 * Constructor
	 *
	 * @param int $orderID
	 * @param int $reservationID
	 * @param int $userID
	 * @param float $cost
	 * @param bool $complete
	 * @return ToT\Order
	 */
	public function __construct(
		int $orderID = 0,
		int $reservationID = 0,
		int $userID = 0,
		float $cost = 0.0,
		int $complete = 0
	) {
		$this->id           	 = $orderID;
		$this->reservationID     = $reservationID;
		$this->userID            = $userID;
		$this->cost              = $cost;
		$this->complete			 = $complete;
	}

	/**
	 * Get the order id
	 * @return int
	 */
	public function id()
	{
		return $this->id;
	}
	/**
	 * Get the reservation id
	 * @return int
	 */
	public function reservationID()
	{
		return $this->reservationID;
	}
	/**
	 * Get the user id
	 * @return int
	 */
	public function userID()
	{
		return $this->userID;
	}
	/**
	 * get the total cost of order
	 * @return float
	 */
	public function cost()
	{
		return $this->cost;
	}
	/**
	 * increment the cost
	 */
	protected function setCost($cost)
	{
		$this->cost += $cost;
	}

	/**
	 * Get order completion status
	 * @return bool
	 */
	public function complete()
	{
		return $this->complete;
	}

	/**
	 * Set order completion by User ID
	 */
	public function setCompleteForUser($complete, $userID)
	{
		$this->complete = $complete;

		// Grab a copy of the database connection
		$db = Database\Connection::getConnection();
		$select = 'UPDATE `orders`  SET `complete` = ' . $complete . ' WHERE ORDER_ID = ' . $this->id . ' AND USER_ID = ' . $userID;

		// confirm the query worked
		if (($result = $db->query($select)) === false) {
			// TODO: replace a simple error with an exception
			throw new Database\Exception('Delete Item failed: ' . $db->error);
		}
	}

	/**
	 * Set order completion by Reservation
	 */
	public function setCompleteForReservation($complete)
	{
		$this->complete = $complete;

		// Grab a copy of the database connection
		$db = Database\Connection::getConnection();
		$select = 'UPDATE `orders`  SET `complete` = ' . $complete . ' WHERE RESERVATION_ID = ' . $this->reservationID;
		// confirm the query worked
		if (($result = $db->query($select)) === false) {
			// TODO: replace a simple error with an exception
			throw new Database\Exception('Delete Item failed: ' . $db->error);
		}
	}

	/**
	 * Load a order by user id
	 *
	 * @param int $userId
	 * @return mixed
	 */
	public static function getOrderByUserId(int $userId)
	{
		// secure the query by forcing an integer value 
		//TODO MAY NEED TO CHECK RESERVATION ID AS WELL

		return self::loadOrderByQuery(
			'SELECT * FROM `orders` WHERE `USER_ID` = ' . intval($userId) . ';'
		);
	}

	/**
	 * TODO Fix this it is broken for some reason, and I honestly have no idea why
	 * The raw query itself works fine :/
	 */
	public static function getOrderById(int $orderId)
	{
		// SELECT * FROM `orders` WHERE `ORDER_ID` = 1;
		$intOrderID = intval($orderId);
		return self::loadOrderByQuery(
			"SELECT * FROM `orders` WHERE `ORDER_ID`={$intOrderID};"
		);
	}

	/**
	 * Might be a niche function but took like 10 seconds to write so meh.
	 */
	public static function getOrderByReservationIdAndUserId(int $reservationID, int $userID)
	{
		$intReservationID = intval($reservationID);
		$intUserID = intval($userID);
		return self::loadOrderByQuery(
			"SELECT * FROM `orders` WHERE `RESERVATION_ID`={$intReservationID} AND `USER_ID`={$intUserID};"
		);
	}

	/**
	 * Might be a niche function but took like 10 seconds to write so meh.
	 */
	public static function getOrdersByReservationId(int $reservationID)
	{
		$intReservationID = intval($reservationID);
		return self::loadOrdersByQuery(
			"SELECT * FROM `orders` WHERE `RESERVATION_ID`={$intReservationID};"
		);
	}

	/**
	 * loads all items linked with an order
	 * @param int $orderId
	 * @return mixed
	 */
	public static function getMenuItemsByOrderID(int $orderID)
	{
		return MenuItem::getMenuItemsByOrderID($orderID);
	}
	/**
	 * Gets user object off an Order using the order ID
	 * @return User
	 */
	public static function getUserFromOrderID(int $orderID)
	{
		return User::getUserFromOrderID($orderID);
	}

	/**
	 * Gets user object who made this order
	 * @return User
	 */
	public function getUser()
	{
		return User::getUserByID($this->id);
	}

	/**
	 * loads all items for a single user linked with an order 
	 * @param int $orderId
	 * @param int $userId
	 * @return mixed
	 */
	public static function getMenuItemsByOrderIDAndUserID(int $orderID, int $userID)
	{
		return MenuItem::getMenuItemsByOrderIDAndUserID($orderID, $userID);
	}

	protected function updateCost($price)
	{
		// Grab a copy of the database connection
		$db = Database\Connection::getConnection();
		$select = 'UPDATE `orders`  SET `cost` = ' . $price . ' WHERE ORDER_ID = ' . $this->id;

		// confirm the query worked
		if (($result = $db->query($select)) === false) {
			// TODO: replace a simple error with an exception
			throw new Database\Exception('Delete Item failed: ' . $db->error);
		}
	}
	/**
	 * Add an item in the order
	 *
	 * @param int $itemID
	 */
	public function addItem(int $itemID, int $userID)
	{

		// Grab a copy of the database connection
		$db = Database\Connection::getConnection();
		$select = 'INSERT INTO `order_menu_items` (`ORDER_ID`,`ITEM_ID`, `USER_ID`) VALUES (' . $this->id . "," . intval($itemID) . "," . intval($userID) . ')';

		// confirm the query worked
		if (($result = $db->query($select)) === false) {
			// TODO: replace a simple error with an exception
			throw new Database\Exception('Add Iem failed: ' . $db->error);
		}

		// Get the price and add it to the total
		$select = 'SELECT price FROM menu_items WHERE ITEM_ID=' . intval($itemID);

		// confirm the query worked
		if (($result = $db->query($select)) === false) {
			// TODO: replace a simple error with an exception
			throw new Database\Exception('Add Item failed: ' . $db->error);
		}
		$this->setCost($result->fetch_row()[0]);
		$this->updateCost($this->cost());
	}
	/**
	 * Delete an item in the order using it's ID from order_menu_items table
	 *	
	 * @param int $orderMenuItemsID
	 */
	public function deleteItemByOrderMenuItemsID(int $orderMenuItemsID)
	{

		// Grab a copy of the database connection
		$db = Database\Connection::getConnection();

		// Get the specific ID of the item to delete
		$select = 'SELECT ITEM_ID FROM order_menu_items WHERE ID =' . intval($orderMenuItemsID);

		$result = $db->query($select);
		$item = $result->fetch_row()[0];

		// Get the price of the deleted item and subtract it from the total
		$select = 'SELECT price FROM menu_items WHERE ITEM_ID=' . intval($item);
		$result = $db->query($select);

		$this->setCost(-1 * $result->fetch_row()[0]);
		$this->updateCost($this->cost());

		// Delete the item
		$select = 'DELETE FROM `order_menu_items` WHERE `ORDER_ID` =' . $this->id .  ' AND `ID` =' . intval($orderMenuItemsID) . ';';

		// confirm the query worked
		if (($result = $db->query($select)) === false) {
			// TODO: replace a simple error with an exception
			throw new Database\Exception('Delete Item failed: ' . $db->error);
		}
	}

	/**
	 * Delete an item in the order using it's ID from order_menu_items table
	 *	
	 * @param int $itemID
	 */
	public function deleteItem(int $itemID)
	{

		// Grab a copy of the database connection
		$db = Database\Connection::getConnection();

		// Get the price of the deleted item and subtract it from the total
		$select = 'SELECT price FROM menu_items WHERE ITEM_ID=' . intval($itemID);
		$result = $db->query($select);

		$this->setCost(-1 * $result->fetch_row()[0]);
		$this->updateCost($this->cost());

		// Delete the item
		$select = 'DELETE FROM `order_menu_items` WHERE `ORDER_ID` =' . $this->id .  ' AND `ITEM_ID` =' . intval($itemID) . ' LIMIT 1;';

		// confirm the query worked
		if (($result = $db->query($select)) === false) {
			// TODO: replace a simple error with an exception
			throw new Database\Exception('Delete Item failed: ' . $db->error);
		}
	}

	/**
	 * get items in an order
	 *
	 */
	public function getOrderItems()
	{

		// Grab a copy of the database connection
		$db = Database\Connection::getConnection();
		$select = 'SELECT ID FROM `order_menu_items` WHERE `ORDER_ID` = "' . $this->id . '";';

		// confirm the query worked
		if (($result = $db->query($select)) === false) {
			// TODO: replace a simple error with an exception
			throw new Database\Exception('Get order items failed: ' . $db->error);
		}

		return $result->fetch_all();
	}

	/**
	 * Execute a single select
	 *
	 * @param string $query
	 * @return Order
	 */
	protected static function loadOrderByQuery(string $select)
	{
		// use the multi-select to load matching users
		$orders = self::loadOrdersByQuery($select);

		// confirm the result set size
		$count = count($orders);
		if ($count > 1) {
			// We must have just one result
			throw new Database\Exception('Single Lookup Failed: returned ' . count($orders) . ' rows.');
		} elseif (!$count) {
			// No results found
			throw new Database\Exception('Single (Orders) Lookup Failed: no match found.');
		}

		// pop off the single result
		return array_shift($orders);
	}

	protected static function loadOrdersByQuery(string $select)
	{
		// Setup a dummy return value
		$orders = [];

		// Grab a copy of the database connection
		$db = Database\Connection::getConnection();

		// confirm the query worked
		if (($result = $db->query($select)) === false) {
			// TODO: replace a simple error with an exception
			throw new Database\Exception('DB Query Failed: ' . $db->error);
		}

		// load and convert each result object
		while (($data = $result->fetch_object()) !== null) {
			$orders[] = self::objectForRow($data);
		}

		// convert the resulting object
		return $orders;
	}

	/**
	 * Convert a MySQL row object to a User
	 *
	 * @param object
	 * @return Order
	 */
	public static function objectForRow($data)
	{

		// Create the appropriate subclass based on the user type
		$order = new Order(
			$data->ORDER_ID,
			$data->RESERVATION_ID,
			$data->USER_ID,
			$data->cost,
			$data->complete
		);

		return $order;
	}
}
