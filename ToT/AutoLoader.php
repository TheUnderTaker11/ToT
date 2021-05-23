<?php

/**
 * AutoLoader.php
 *
 * A simple mechanism for automatically locating class files for items on-demand.
 *
 * In general, an auto loader needs to keep track of which classes it has seen and found and which
 * it was unable to find.
 */

namespace ToT;

class AutoLoader
{

	/**
	 * Class path list
	 *
	 * List of file directories where this loader will look for classes.
	 *
	 * @type array
	 */
	protected $classPaths;



	/**
	 * Constructor
	 *
	 * Create a new auto loader for the specified class path(s).
	 *
	 * @param array|string $paths
	 */
	public function __construct($paths)
	{

		// init the class paths
		$this->classPaths = [];

		// import the provided path(s)
		if (is_array($paths)) {
			foreach ($paths as $path) {
				$this->addClassPath($path);
			}
		} elseif (is_string($paths)) {
			$this->addClassPath($paths);
		}

		// automatically register as a loader
		spl_autoload_register([$this, 'loadClass']);
	}

	/**
	 * Destructor
	 *
	 * Automatically unregister upon destruction.
	 */
	public function __destruct()
	{
		spl_autoload_unregister([$this, 'loadClass']);
	}



	/**
	 * Add a class path
	 *
	 * Return $this to allow chaining.
	 *
	 * @param string $path
	 * @return \AutoLoader
	 */
	public function addClassPath(string $path)
	{

		// trim any trailing slash
		$path = rtrim($path, '/');

		// confirm the path is a valid filesystem directory
		if (is_dir($path)) {

			// confirm this path is not already registered
			if (!in_array($path, $this->classPaths)) {
				$this->classPaths[] = $path;
			}
		}
		return $this;
	}


	/**
	 * Load a class
	 *
	 * @param string $class
	 * @return string
	 */
	public function loadClass($class)
	{

		// search for the file in the various class paths
		$classFileName = $class;

		// treat underscores as sub-directories
		if (strpos($class, '_') !== false) {
			$classFileName = str_replace('_', '/', $class);
		}
		// treat namespace delimiters as sub-directories
		elseif (strpos($class, '\\') !== false) {
			$classFileName = str_replace('\\', '/', $class);
		}
		foreach ($this->classPaths as $path) {
			$file = $path . '/' . $classFileName . '.php';
			if (is_file($file)) {
				include $file;
				return true;
			}
		}

		// if not found, return false
		return false;
	}
}
