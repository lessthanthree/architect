<?php
/**
 *	Rae
 *
 *	Rae ("Record-Analyze-Evolve") is a lightweight profiling and preformance analyzing library used to benchmark and analyze certain aspect of web based applications.
 *
 *	@author Robin Grass <robin@kodlabbet.net>
 *	@link http://kodlabbet.net
 *
 *	@license http://www.opensource.org/licenses/MIT MIT License
 */

/* @namespace Rae */
namespace Rae;

/* Deny direct file access */
if(!defined('RAE_ROOT_PATH')) exit;

/**
 *	File
 *
 *	Collects information on all included files in use.
 *
 *	@package Records
 *
 *	@version 1.0.0
 *
 *	@author Robin Grass <robin@kodlabbet.net>
 */
class File extends Collection implements Record {

	/**
	 *	@staticvar float $microtime Microtime when record store store was created.
	 */
	protected static $microtime;

	/**
	 *	register
	 *
	 *	Creates store and saves microtime when log store was created.
	 *
	 *	@return void
	 */
	public static function register() {
		
		self::createStore(getRecordID(__CLASS__));
		
		self::$microtime = microtime(true);
	
	}

	/**
	 *	analyze
	 *
	 *	Gathers information on all included files.
	 *
	 *	@return void
	 */
	public static function analyze() {

		$store_id = getRecordID(__CLASS__);
		
		$included_file_paths = get_included_files();
		
		$file_sizes = array();
		
		foreach($included_file_paths as $file_path) {
			
			self::assertRecord($store_id, array(
				'name' => basename($file_path),
				'text' => $file_path,
				'size' => filesize($file_path)
			));
			
			$file_sizes[filesize($file_path)] = $file_path;
		
		}
		
		// Get largest file
		$largest_file_size = max(array_keys($file_sizes));
		$largest_file = $file_sizes[$largest_file_size];
		
		// Get smallest file
		$smallest_file_size = min(array_keys($file_sizes));
		$smallest_file = $file_sizes[$smallest_file_size];
		
		// Store largest file
		self::$store[$store_id]['largest_file'] = array(
			'name' => $largest_file,
			'size' => $largest_file_size
		);
		
		// Store smallest file
		self::$store[$store_id]['smallest_file'] = array(
			'name' => $smallest_file,
			'size' => $smallest_file_size
		);

	}

}
?>