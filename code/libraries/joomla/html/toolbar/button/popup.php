<?php
/**
* @version		$Id: popup.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla.Framework
* @subpackage	HTML
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
 * Renders a popup window button
 *
 * @package 	Joomla.Framework
 * @subpackage		HTML
 * @since		1.5
 */
class JButtonPopup extends JButton
{
	/**
	 * Button type
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'Popup';

	function fetchButton( $type='Popup', $name = '', $text = '', $url = '', $width=640, $height=480, $top=0, $left=0 )
	{
		JHTML::_('behavior.modal');

		$text	= JText::_($text);
		$class	= $this->fetchIconClass($name);
		$doTask	= $this->_getCommand($name, $url, $width, $height, $top, $left);

		$html	= "<a class=\"modal\" href=\"$doTask\" rel=\"{handler: 'iframe', size: {x: $width, y: $height}}\">\n";
		$html .= "<span class=\"$class\" title=\"$text\">\n";
		$html .= "</span>\n";
		$html	.= "$text\n";
		$html	.= "</a>\n";

		return $html;
	}

	/**
	 * Get the button id
	 *
	 * Redefined from JButton class
	 *
	 * @access		public
	 * @param		string	$name	Button name
	 * @return		string	Button CSS Id
	 * @since		1.5
	 */
	function fetchId($name)
	{
		return $this->_parent->_name.'-'."popup-$name";
	}

	/**
	 * Get the JavaScript command for the button
	 *
	 * @access	private
	 * @param	object	$definition	Button definition
	 * @return	string	JavaScript command string
	 * @since	1.5
	 */
	function _getCommand($name, $url, $width, $height, $top, $left)
	{
		if (substr($url, 0, 4) !== 'http') {
			$url = JURI::base().$url;
		}

		return $url;
	}
}
