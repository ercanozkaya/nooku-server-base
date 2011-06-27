<?php
/**
* @version		$Id: callback.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla.Framework
* @subpackage	Cache
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Joomla! Cache callback type object
 *
 * @package		Joomla.Framework
 * @subpackage	Cache
 * @since		1.5
 */
class JCacheCallback extends JCache
{
	/**
	 * Executes a cacheable callback if not found in cache else returns cached output and result
	 *
	 * Since arguments to this function are read with func_get_args you can pass any number of arguments to this method
	 * as long as the first argument passed is the callback definition.
	 *
	 * The callback definition can be in several forms:
	 * 	- Standard PHP Callback array <http://php.net/callback> [recommended]
	 * 	- Function name as a string eg. 'foo' for function foo()
	 * 	- Static method name as a string eg. 'MyClass::myMethod' for method myMethod() of class MyClass
	 *
	 * @access	public
	 * @return	mixed	Result of the callback
	 * @since	1.5
	 */
	function call()
	{
		// Get callback and arguments
		$args		= func_get_args();
		$callback	= array_shift($args);

		return $this->get( $callback, $args );
	}

	/**
	 * Executes a cacheable callback if not found in cache else returns cached output and result
	 *
	 * @access	public
	 * @param	mixed	Callback or string shorthand for a callback
	 * @param	array	Callback arguments
	 * @return	mixed	Result of the callback
	 * @since	1.5
	 */
	function get( $callback, $args, $id=false )
	{
		// Normalize callback
		if (is_array( $callback )) {
			// We have a standard php callback array -- do nothing
		} elseif (strstr( $callback, '::' )) {
			// This is shorthand for a static method callback classname::methodname
			list( $class, $method ) = explode( '::', $callback );
			$callback = array( trim($class), trim($method) );
		} elseif (strstr( $callback, '->' )) {
			/*
			 * This is a really not so smart way of doing this... we provide this for backward compatability but this
			 * WILL!!! disappear in a future version.  If you are using this syntax change your code to use the standard
			 * PHP callback array syntax: <http://php.net/callback>
			 *
			 * We have to use some silly global notation to pull it off and this is very unreliable
			 */
			list( $object_123456789, $method ) = explode('->', $callback);
			global $$object_123456789;
			$callback = array( $$object_123456789, $method );
		} else {
			// We have just a standard function -- do nothing
		}

		if (!$id) {
			// Generate an ID
			$id = $this->_makeId($callback, $args);
		}

		// Get the storage handler and get callback cache data by id and group
		$data = parent::get($id);
		if ($data !== false) {
			$cached = unserialize( $data );
			$output = $cached['output'];
			$result = $cached['result'];
		} else {
			ob_start();
			ob_implicit_flush( false );

			$result = call_user_func_array($callback, $args);
			$output = ob_get_contents();

			ob_end_clean();

			$cached = array();
			$cached['output'] = $output;
			$cached['result'] = $result;
			// Store the cache data
			$this->store(serialize($cached), $id);
		}

		echo $output;
		return $result;
	}

	/**
	 * Generate a callback cache id
	 *
	 * @access	private
	 * @param	callback	$callback	Callback to cache
	 * @param	array		$args	Arguments to the callback method to cache
	 * @return	string	MD5 Hash : function cache id
	 * @since	1.5
	 */
	function _makeId($callback, $args)
	{
		if(is_array($callback) && is_object($callback[0])) {
			$vars = get_object_vars($callback[0]);
			$vars[] = strtolower(get_class($callback[0]));
			$callback[0] = $vars;
		}
		return md5(serialize(array($callback, $args)));
	}
}
