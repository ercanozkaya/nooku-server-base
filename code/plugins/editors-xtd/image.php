<?php
/**
 * @version		$Id: image.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla
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

jimport( 'joomla.plugin.plugin' );

/**
 * Editor Image buton
 *
 * @package Editors-xtd
 * @since 1.5
 */
class plgButtonImage extends JPlugin
{
	/**
	 * Display the button
	 *
	 * @return array A two element array of ( imageName, textToInsert )
	 */
	function onDisplay($name)
	{
		global $mainframe;
		$params =& JComponentHelper::getParams('com_files');
		
		//Find out who has permission to upload and change the acl to let them.
		$acl = & JFactory::getACL();
		switch ($params->get('allowed_media_usergroup')) 
		{
			case '1':
				$acl->addACL( 'com_files', 'upload', 'users', 'publisher' );
				break;
			case '2':
				$acl->addACL( 'com_files', 'upload', 'users', 'publisher' );
				$acl->addACL( 'com_files', 'upload', 'users', 'editor' );
				break;
			case '3': 
				$acl->addACL( 'com_files', 'upload', 'users', 'publisher' );
				$acl->addACL( 'com_files', 'upload', 'users', 'editor' );				
				$acl->addACL( 'com_files', 'upload', 'users', 'author' );
				break;								
			case '4':
				$acl->addACL( 'com_files', 'upload', 'users', 'publisher' );				
				$acl->addACL( 'com_files', 'upload', 'users', 'editor' );
				$acl->addACL( 'com_files', 'upload', 'users', 'author' );
				$acl->addACL( 'com_files', 'upload', 'users', 'registered' );
				break;
		}
	
		//Make sure the user is authorized to view this page
		$user = & JFactory::getUser();
		if (!$user->authorize( 'com_files', 'popup' )) {
			return;
		}
		$doc 		=& JFactory::getDocument();
		$template 	= $mainframe->getTemplate();

		$link = 'index.php?option=com_files&amp;view=images&amp;tmpl=component&amp;e_name='.$name;

		JHTML::_('behavior.modal');

		$button = new JObject();
		$button->set('modal', true);
		$button->set('link', JRoute::_($link));
		$button->set('text', JText::_('Image'));
		$button->set('name', 'image');
		$button->set('options', "{handler: 'iframe', size: {x: 800, y: 400}}");

		return $button;
	}
}
