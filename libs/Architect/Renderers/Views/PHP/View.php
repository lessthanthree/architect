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

/* @namespace PHP */
namespace Architect\Renderers\Views\PHP;

/* Deny direct file access */
if(!defined('ARCH_ROOT_PATH')) exit;

/**
 *	View
 *
 *	Simple PHP view class.
 *
 *	@package Renderers
 *	@subpackage Views
 *	@subpackage PHP
 *
 *	@version 1.0.0
 *
 *	@author Robin Grass <robin@kodlabbet.net>
 */
class View extends \Architect\Renderers\Views\ViewAbstract {

	/**
	 *	__construct
	 *
	 *	Register stream handler and set default include path.
	 *
	 *	@return void
	 */
	public function __construct() {

		$this->setStreamWrapper('phpview', '\Architect\Renderers\Views\PHP\Stream');

		$this->setIncludePath(ARCH_VIEWS_PATH);

	}

	/**
	 *	setVariables
	 *
	 *	Set view variables.
	 *
	 *	@param array $variables Associative array with variables to register.
	 *
	 *	@return void
	 */
	public function setVariables(array $variables) {

		// Iterate through each variables and set them
		foreach($variables as $variable => $data) {

			// Only set variable if key is string
			if(is_string($variable) === true) {

				$this->variables[$variable] = $data;

			}

		}

	}

	/**
	 *	Getter
	 *
	 *	@param string $variable Variable name.
	 *
	 *	@return mixed
	 */
	public function __get($variable) {

		return $this->variables[$variable];

	}

	/**
	 *	Setter
	 *
	 *	@param string $variable Variable name.
	 *	@param int|float|string $data Variable data.
	 *
	 *	@return void
	 */
	public function __set($variable, $data) {

		$this->variables[$variable] = $data;

	}

	/**
	 *	render
	 *
	 *	Renders a PHP based view.
	 *
	 *	@throws Exceptions\ViewException
	 *
	 *	@return string
	 */
	public function render() {

		// Throw exception if view tries to render itself within a loop
		if($this->view_file === $this->previous_view_file) {

			throw new Exceptions\ViewException(
				"Could not render view file.",
				"File \"{$this->view_file}\" is already rendered.",
				__METHOD__, Exceptions\ViewException::INVALID_ARGUMENT_EXCEPTION
			);

		}

		// Set previous view file
		$this->previous_view_file = $this->view_file;

		// Capture output buffer
		@ob_start();

		// Import view file
		$this->import($this->view_file);

		// Get and clear buffer
		$rendered_view = ob_get_clean();

		// Return rendered view
		return $rendered_view;

	}

}
?>