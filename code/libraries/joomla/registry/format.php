<?php
/**
 * @version		$Id: format.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla.Framework
 * @subpackage	Registry
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
 * Abstract Format for JRegistry
 *
 * @abstract
 * @package 	Joomla.Framework
 * @subpackage	Registry
 * @since		1.5
 */
class JRegistryFormat extends JObject
{
	/**
	 * Returns a reference to a Format object, only creating it
	 * if it doesn't already exist.
	 *
	 * @static
	 * @param	string	$format	The format to load
	 * @return	object	Registry format handler
	 * @since	1.5
	 */
	function &getInstance($format)
	{
		static $instances;

		if (!isset ($instances)) {
			$instances = array ();
		}

		$format = strtolower(JFilterInput::clean($format, 'word'));
		if (empty ($instances[$format]))
		{
			$class = 'JRegistryFormat'.$format;
			if(!class_exists($class))
			{
				$path    = dirname(__FILE__).DS.'format'.DS.$format.'.php';
				if (file_exists($path)) {
					require_once($path);
				} else {
					JError::raiseError(500,JText::_('Unable to load format class'));
				}
			}

			$instances[$format] = new $class ();
		}
		return $instances[$format];
	}

	/**
	 * Converts an XML formatted string into an object
	 *
	 * @abstract
	 * @access	public
	 * @param	string	$data	Formatted string
	 * @return	object	Data Object
	 * @since	1.5
	 */
	function stringToObject( $data, $namespace='' ) {
		return true;
	}

	/**
	 * Converts an object into a formatted string
	 *
	 * @abstract
	 * @access	public
	 * @param	object	$object	Data Source Object
	 * @return	string	Formatted string
	 * @since	1.5
	 */
	function objectToString( &$object ) {

	}
}