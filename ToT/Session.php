<?php

/**
 * Session.php
 *
 * Model the current user's session.
 */

namespace ToT;

class Session
{

	/**
	 * Session user object key
	 */
	const SESSION_USER_KEY = 'AuthenticatedUser';

	/**
	 * instance
	 *
	 * A reference to the singleton instance.
	 */
	protected static $instance;

	/**
	 * Current user
	 */
	protected $user;



	/**
	 * Constructor
	 *
	 * Protect this to make the singleton.
	 */
	protected function __construct()
	{

		// set some defaults
		$this->user = null;
		$this->reservationSize = null;

		// start the PHP session
		session_start();
		session_regenerate_id(true);

		// look for a logged-in user
		if (array_key_exists(self::SESSION_USER_KEY, $_SESSION)) {
			$this->user = $_SESSION[self::SESSION_USER_KEY];
		}
	}
	public function __destruct()
	{
		// Save the current user to the session
		$_SESSION[self::SESSION_USER_KEY] = $this->user;
	}

	/**
	 * Get user
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * Setup session
	 */
	public function setupSession(User $user)
	{
		$this->user = $user;
	}

	/**
	 * Get the session
	 */
	public static function getSession()
	{
		if (!self::$instance) {
			self::$instance = new Session();
		}
		return self::$instance;
	}

	/**
	 * Destroy the session
	 */
	public static function destroy()
	{
		session_start();
		$_SESSION = [];
		session_destroy();
		self::$instance = null;
	}
}
