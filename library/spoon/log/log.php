<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package		log
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @author 		Tijs Verkoyen <tijs@spoon-library.be>
 * @author		Dave Lens <dave@spoon-library.be>
 * @since		1.0.0
 */


/** SpoonFile class */
require_once 'spoon/filesystem/filesystem.php';


/**
 * This exception is used to handle log related exceptions.
 *
 * @package		log
 *
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.be>
 * @since		1.0.0
 */
class SpoonLogException extends SpoonException {}


/**
 * This base class provides methods used to log data.
 *
 * @package		log
 *
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.be>
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		1.0.0
 */
class SpoonLog
{
	// expressed in KB
	const MAX_FILE_SIZE = 500;


	/**
	 * Log path
	 *
	 * @var	string
	 */
	private static $logPath;


	/**
	 * Get the log path
	 *
	 * @return	string
	 */
	public static function getPath()
	{
		if(self::$logPath === null) return (string) str_replace('/spoon/log/log.php', '', __FILE__);
		return self::$logPath;
	}


	/**
	 * Set the logpath
	 *
	 * @return	void
	 * @param	string $path
	 */
	public static function setPath($path)
	{
		self::$logPath = (string) $path;
	}


	/**
	 * Write an error
	 *
	 * @return	void
	 * @param	string $message
	 * @param	string[optional] $type
	 */
	public static function write($message, $type = 'error')
	{
		// milliseconds
		list($milliseconds) = explode(' ', microtime());
		$milliseconds = round($milliseconds * 1000, 0);

		// redefine var
		$message = date('Y-m-d H:i:s') .' '. $milliseconds .'ms | '. $message . "\n";
		$type = SpoonFilter::getValue($type, array('error', 'custom'), 'error');

		// file
		$file = self::getPath() .'/'. $type .'.log';

		// rename if needed
		if((int) @filesize($file) >= (self::MAX_FILE_SIZE * 1024))
		{
			// start new log file
			SpoonDirectory::move($file, $file .'.'. date('Ymdhis'));
		}

		// write content
		SpoonFile::setContent($file, $message, true, true);
	}
}

?>