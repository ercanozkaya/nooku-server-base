<?php
/**
 * @version		$Id: toolbar.admin.html.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla
 * @subpackage	Admin
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
* @package		Joomla
* @subpackage	Admin
*/
class TOOLBAR_admin
{
	function _SYSINFO() {

		JToolBarHelper::title( JText::_( 'Information' ), 'systeminfo.png' );
	}

	function _CPANEL() {

		JToolBarHelper::title( JText::_( 'Control Panel' ), 'cpanel.png' );
	}

	function _PREVIEW() {

		JToolBarHelper::title( JText::_( 'Preview' ) );
	}

	function _DEFAULT() {
	}
}