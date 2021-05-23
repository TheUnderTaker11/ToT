<?php

/**
 * bootstrap.php
 *
 * Setup the environment elements required for operation.
 *
 * @author Errol Sayre
 */

namespace ToT;

require_once('ToT/AutoLoader.php');

// set the autoloader to use the current directory
$loader = new AutoLoader(dirname(__FILE__));

// Get access to our constants class
$constants = new Constants();

// Setup the database configuration for the current environment
if ($constants->isDevelopment()) {
	Database\Connection::setHost($constants::DB_DEV_HOST);
	Database\Connection::setUser($constants::DB_DEV_USER);
	Database\Connection::setPassword($constants::DB_DEV_PASSWORD);
	Database\Connection::setSchema($constants::DB_DEV_SCHEMA);
} elseif ($constants->isClaysEnvironment()) {
	Database\Connection::setHost($constants::CLAY_DB_HOST);
	Database\Connection::setUser($constants::CLAY_DB_USER);
	Database\Connection::setPassword($constants::CLAY_DB_PASSWORD);
	Database\Connection::setSchema($constants::CLAY_DB_SCHEMA);
} elseif ($constants->isProduction()) {
	Database\Connection::setHost($constants::DB_PROD_HOST);
	Database\Connection::setUser($constants::DB_PROD_USER);
	Database\Connection::setPassword($constants::DB_PROD_PASSWORD);
	Database\Connection::setSchema($constants::DB_PROD_SCHEMA);
}
