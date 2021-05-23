<?php

/**
 * Linker.php
 *
 * Class to recognize environment and establish the proper URL for our application.
 *
 */

namespace ToT;

class Linker
{

	/**
	 * Derived host
	 */
	protected static $host;

	/**
	 * Derived application path
	 */
	protected static $appPath;



	/**
	 * Combine the appropriate elements to build the URL path.
	 * @return string
	 */
	public static function urlPath()
	{

		// determine if we have previously derived the attributes
		if (!self::$host && !self::$appPath) {
			self::derivePath($_SERVER);
		}

		// assemble the URL
		return self::$host . self::$appPath;
	}

	/**
	 * Derive the elements from the PHP globals.
	 */
	public static function derivePath($data)
	{

		// Assemble the host
		self::$host =
			$data['REQUEST_SCHEME'] .
			'://' .
			$data['SERVER_NAME'];
		if (
			($data['SERVER_PORT'] == 'http'  && $data['SERVER_PORT'] != 80) ||
			($data['SERVER_PORT'] == 'https' && $data['SERVER_PORT'] != 443)
		) {
			self::$host .= ':' . $data['SERVER_PORT'];
		}

		// Assemble the application path
		self::$appPath = dirname($_SERVER['PHP_SELF']) . '/';

		if (self::$appPath == '//') {
			self::$appPath = '/';
		}
	}
}
