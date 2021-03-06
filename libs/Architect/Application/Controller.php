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

/* @namespace Application */
namespace Architect\Application;

/* Deny direct file access */
if(!defined('ARCH_ROOT_PATH')) exit;

/**
 *	Controller
 *
 *	Controllers are building blocks for the Model-View-Controller pattern. Each controller must contain declared method {@see Controller::index} and {@see Controller::error}.
 *
 *	@package Application
 *
 *	@version 1.0.0
 *
 *	@author Robin Grass <robin@kodlabbet.net>
 */
interface Controller {

	/**
	 *	index
	 *
	 *	Default method called by a router, see {@see Architect\Delegation\Router} for more info.
	 *
	 *	@return void
	 */
	public function index();

	/**
	 *	error
	 *
	 *	Error callback called when route does not exist, or Controller method does not exist.
	 *
	 *	@return void
	 */
	public function error();

}
?>