<?php

/**
 * Connection.php
 *
 * DB connection singleton.
 */

namespace ToT\Database;

class Connection extends \MySQLi
{

	/**
	 * hostname
	 *
	 * @type string
	 */
	protected static $hostname;

	/**
	 * username
	 *
	 * @type string
	 */
	protected static $username;

	/**
	 * password
	 *
	 * @type string
	 */
	protected static $password;

	/**
	 * database
	 *
	 * @type string
	 */
	protected static $database;

	/**
	 * db connection singleton
	 *
	 * Keep this static to protect the singleton
	 *
	 * @type Connection
	 */
	protected static $connection;

	/**
	 * Constructor
	 */
	protected function __construct()
	{
		// Pass our configured data onto the MySQLi class.
		parent::__construct(
			self::$hostname,
			self::$username,
			self::$password,
			self::$database
		);
	}

	/**
	 * Set the hostname
	 */
	public static function setHost($host)
	{
		self::$hostname = $host;
		// TODO: close/invalidate any open connections
	}

	/**
	 * Set the username
	 */
	public static function setUser($user)
	{
		self::$username = $user;
		// TODO: close/invalidate any open connections
	}

	/**
	 * Set the password
	 */
	public static function setPassword($password)
	{
		self::$password = $password;
		// TODO: close/invalidate any open connections
	}

	/**
	 * Set the database schema
	 */
	public static function setSchema($schema)
	{
		self::$database = $schema;
		// TODO: close/invalidate any open connections
	}

	/**
	 * Get a connection
	 *
	 * @return Connection
	 */
	public static function getConnection()
	{

		// create the singleton if not already
		if (self::$connection == null) {
			self::$connection = new Connection();
		}
		return self::$connection;
	}
}
