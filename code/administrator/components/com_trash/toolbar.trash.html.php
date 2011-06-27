<?php
/**
* @version		$Id: toolbar.trash.html.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @subpackage	Trash
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
* @subpackage	Trash
*/
class TOOLBAR_Trash 
{
	function _DEFAULT() 
	{
		$task	= JRequest::getCmd('task', 'viewMenu');
		$return	= JRequest::getCmd('return', 'viewContent', 'post');

		JToolBarHelper::title( JText::_( 'Trash Manager' ), 'trash.png' );
		JToolBarHelper::custom('restoreconfirm','restore.png','restore_f2.png', 'Restore', true);
		JToolBarHelper::custom('deleteconfirm','delete.png','delete_f2.png', 'Delete', true);
	}

	function _RESTORE() 
	{
		JToolBarHelper::title( JText::_( 'Restore Items' ), 'restoredb.png' );
		JToolBarHelper::cancel();
	}

	function _DELETE() 
	{
		JToolBarHelper::title( JText::_( 'Delete Items' ), 'delete_f2.png' );
		JToolBarHelper::cancel();
	}
}