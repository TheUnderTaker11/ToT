<?php

/**
 * ToT Constants
 *
 * List of constants for use in the ToT system.
 */

namespace ToT;

class Constants
{
	// Environments
	const DEVELOPMENT     = 'Dev';
	const PRODUCTION      = 'Prod';
	const HOST_TURING     = 'turing';

	// DB elements
	const DB_DEV_USER     = 'root';
	const DB_DEV_PASSWORD = '';
	const DB_DEV_HOST     = 'localhost';
	const DB_DEV_SCHEMA   = 'tot';

	// Production DB elements
	const DB_PROD_USER     = 'tot';
	const DB_PROD_PASSWORD = 'totolemiss2021';
	const DB_PROD_HOST     = 'localhost';
	const DB_PROD_SCHEMA   = 'tot';

	// Clay Working Copy items
	const CLAY_ENVIRONMENT = 'Clay';
	const CLAY_USER        = 'cabellou';
	const CLAY_DB_USER     = 'cabellou';
	const CLAY_DB_PASSWORD = 'cabelloudb';
	const CLAY_DB_HOST     = self::DB_PROD_HOST;
	const CLAY_DB_SCHEMA   = 'cabellou';

	// User types
	const USER_TYPE_CUSTOMER      = 'Customer';
	const USER_TYPE_WAITER 		= 'Waiter';
	const USER_TYPE_HOST      	= 'Host';
	const USER_TYPE_MANAGER 	= 'Manager';

	const PARAM_ACTION = "action";

	const ACTION_ADD_MENU_ITEM_TO_ORDER = "addmenuitemtoorder";


	const PARAM_RESERVATION_ID = "reservationid";
	const PARAM_ORDER_ID = "orderid";
	const PARAM_ITEM_ID = "itemid";

	const TAX_RATE = 1;


	/**
	 * Memoize the determined environment
	 */
	protected static $environment;

	/**
	 * Identify the environment
	 */
	public static function environment()
	{
		if (!self::$environment) {
			self::$environment = self::DEVELOPMENT;

			// determine if we're on turing
			if (php_uname('n') == self::HOST_TURING) {
				// determine if this is Clay's account
				if (strpos(dirname(__FILE__), self::CLAY_USER) !== false) {
					// this is still a dev environment, but should use separate credentials
					self::$environment = self::CLAY_ENVIRONMENT;
				} else {
					// this is production
					self::$environment = self::PRODUCTION;
				}
			}
		}
		return self::$environment;
	}

	/**
	 * Is this a development environment
	 */
	public static function isDevelopment()
	{
		return self::environment() == self::DEVELOPMENT;
	}

	/**
	 * Is this Clay's dev environment
	 */
	public static function isClaysEnvironment()
	{
		return self::environment() == self::CLAY_ENVIRONMENT;
	}

	/**
	 * Is this a production environment
	 */
	public static function isProduction()
	{
		return self::environment() == self::PRODUCTION;
	}
}
