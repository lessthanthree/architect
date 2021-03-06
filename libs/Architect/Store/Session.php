<?php
/**
 *	Architect Framework
 *
 *	Architect Framework is a light-weight and scalable object oriented web applications framework built for PHP 5.3 and later.
 *	Architect focuses on handling common tasks and processes used to quickly develop small, medium and large scale applications.
 *
 *	@author Robin Grass <robin@kodlabbet.net>
 *	@link http://architect.kodlabbet.net/
 *
 *	@license http://www.opensource.org/licenses/lgpl-2.1.php LGPL
 */

/* @namespace Store */
namespace Architect\Store;

/* Deny direct file access */
if(!defined('ARCH_ROOT_PATH')) exit;

/**
 *	Session
 *
 *	Class used to handle session store.
 *
 *	@package Store
 *
 *	@version 1.0.0
 *
 *	@author Robin Grass <robin@kodlabbet.net>
 */
class Session extends \Architect\Store\Object {

	/**
	 *	@var bool $secure_sockets_layer_transfer Defines whether sessions should be transfered over HTTPS.
	 */
	protected $secure_sockets_layer_transfer = false;

	/**
	 *	@var bool $secure_sockets_layer_transfer Defines whether sessions should be transfered over HTTP, not accessable publicly.
	 */
	protected $http_transfer = false;

	/**
	 *	Constructor
	 *
	 *	Sets default session storage options.
	 *
	 *	@return void
	 */
	public function __construct($lifetime = 3600, $store_path = '/') {

		$this->lifetime = $lifetime;

		$this->store_path = $store_path;

		$this->domain = "." . $_SERVER["SERVER_NAME"];

	}

	/**
	 *	transferOverSecureSocketsLayer
	 *
	 *	Activates SSL security option for cookies.
	 *
	 *	@return void
	 */
	public function transferOverSecureSocketsLayer() {

		$this->secure_sockets_layer_transfer = true;

	}

	/**
	 *	transferOverHTTPOnly
	 *
	 *	Activates HTTP transfer only, if set to true, cookies are only accessable via HTTP.
	 *
	 *	@return void
	 */
	public function transferOverHTTPOnly() {

		$this->http_transfer = true;

	}

	/**
	 *	activate
	 *
	 *	Activates session store.
	 *
	 *	@return void
	 */
	public function activate() {

		session_set_cookie_params(
			$this->lifetime,
			$this->store_path,
			$this->domain,
			$this->secure_sockets_layer_transfer,
			$this->http_transfer
		);

		session_start();

	}

	/**
	 *	generateKey
	 *
	 *	Returns hashed string for storage keys.
	 *
	 *	@param string $key
	 *
	 *	@return string
	 */
	protected function generateKey($key) {

		$key_hash = af_hash($key, $this->fingerprint);

		return $key_hash;

	}

	/**
	 *	has
	 *
	 *	Returns boolean whether data entry exists in data store or not.
	 *
	 *	@param string $key Data entry key.
	 *
	 *	@return bool
	 */
	public function has($key) {

		if(array_key_exists($this->generateKey($key), $_SESSION) === true) {

			return true;

		}

		return false;

	}

	/**
	 *	read
	 *
	 *	Returns entry data, if exists, otherwise null.
	 *
	 *	@param string $key Data entry key.
	 *	@param bool $return_expire_time Optional parameter, if set to true method returns expire time instead of data.
	 *
	 *	@return mixed
	 */
	public function read($key, $return_expire_time = false) {

		if($this->has($key) === true) {

			// Get entry
			$entry = $_SESSION[$this->generateKey($key)];

			// Uncompress entry if compressed
			if($this->shouldCompress() === true) {

				$entry = gzuncompress($entry);

			}

			// Unserialize entry
			$entry = unserialize($entry);

			// Validate entry lifetime
			if(time() > $entry->expires) {

				// Delete entry
				$this->delete($key);

				return null;

			}

			if($return_expire_time === true) {

				return $entry->expires;

			}

			return unserialize($entry->data);

		}

		return null;

	}

	/**
	 *	write
	 *
	 *	Writes a new data entry, and overwrite existing data.
	 *
	 *	@param string $key Data entry key.
	 *	@param mixed $data Data entry.
	 *
	 *	@return void
	 */
	public function write($key, $data) {

		$entry = (object) array(

			'data' => serialize($data),

			'expires' => time() + $this->lifetime

		);

		// Serialize entry
		$entry = serialize($entry);

		// Compress entry if possible
		if($this->shouldCompress() === true) {

			$entry = gzcompress($entry);

		}

		// Save entry
		$_SESSION[$this->generateKey($key)] = $entry;

	}

	/**
	 *	touch
	 *
	 *	Refreshes expire time of data entry.
	 *
	 *	@return void
	 */
	public function touch($key) {

		if($this->has($key) === true) {

			// Get entry data
			$data = $this->read($key);

			// Update expire time to current data
			$this->write($key, $data);

		}

	}

	/**
	 *	delete
	 *
	 *	Deletes an existing data entry.
	 *
	 *	@param string $key Data entry key.
	 *
	 *	@return void
	 */
	public function delete($key) {

		if($this->has($key) === true) {

			unset($_SESSION[$this->generateKey($key)]);

		}

	}

	/**
	 *	flush
	 *
	 *	Deletes expired data entries.
	 *
	 *	@return void
	 */
	public function flush() {

		foreach($_SESSION as $key => $entry) {

			// Uncompress entry if compressed
			if($this->shouldCompress() === true) {

				$entry = gzuncompress($entry);

			}

			// Validate entry lifetime
			if(time() > $entry->expires) {

				// Delete entry
				unset($_SESSION[$key]);

			}

		}

	}

}
?>