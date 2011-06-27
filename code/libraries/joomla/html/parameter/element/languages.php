<?php
/**
* @version		$Id: languages.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla.Framework
* @subpackage	Parameter
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
 * Renders a languages element
 *
 * @package 	Joomla.Framework
 * @subpackage		Parameter
 * @since		1.5
 */

class JElementLanguages extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Languages';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$user	= & JFactory::getUser();

		/*
		 * @TODO: change to acl_check method
		 */
		if(!($user->get('gid') >= 23) && $node->attributes('client') == 'administrator') {
			return JText::_('No Access');
		}


		$client = $node->attributes('client');

		jimport('joomla.language.helper');
		$languages = JLanguageHelper::createLanguageList($value, constant('JPATH_'.strtoupper($client)), true);
		array_unshift($languages, JHTML::_('select.option', '', '- '.JText::_('Select Language').' -'));

		return JHTML::_('select.genericlist',  $languages, ''.$control_name.'['.$name.']', 'class="inputbox"', 'value', 'text', $value, $control_name.$name );
	}
}
