<?php
/**
* @version		$Id: pathway.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla.Framework
* @subpackage	Application
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
 * Class to manage the site application pathway
 *
 * @package 	Joomla
 * @since		1.5
 */
class JPathwaySite extends JPathway
{
	/**
	 * Class constructor
	 */
	function __construct($options = array())
	{
		//Initialise the array
		$this->_pathway = array();

		$menu   =& JSite::getMenu();

		if($item = $menu->getActive())
		{
			$menus	= $menu->getMenu();
			$home	= $menu->getDefault();

			if(is_object($home) && ($item->id != $home->id))
			{
				foreach($item->tree as $menupath)
				{
					$url  = '';
					$link = $menu->getItem($menupath);

					switch($link->type)
					{
						case 'menulink' :
						case 'url' :
							$url = $link->link;
							break;
						case 'separator' :
							$url = null;
							break;
						default      :
							$url = 'index.php?Itemid='.$link->id;
					}

					$this->addItem( $menus[$menupath]->name, $url);

				} // end foreach
			}
		} // end if getActive
	}
}