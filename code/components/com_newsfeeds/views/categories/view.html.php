<?php
/**
* version $Id: view.html.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @subpackage	Newsfeeds
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Newsfeeds component
 *
 * @static
 * @package		Joomla
 * @subpackage	Newsfeeds
 * @since 1.0
 */
class NewsfeedsViewCategories extends JView
{
	function display($tpl = null)
	{
		global $mainframe;

		// Load the menu object and parameters
		$params	= &$mainframe->getParams();

		$categories =& $this->get('data');

		for($i = 0; $i < count($categories); $i++)
		{
			$category =& $categories[$i];
			$category->link = JRoute::_('index.php?view=category&id='. $category->slug );

			// Prepare category description
			$category->description = JHTML::_('content.prepare', $category->description);
		}
		// Define image tag attributes
		if ($params->get('image') != -1)
		{
			$attribs['align'] = $params->get('image_align');
			$attribs['hspace'] = 6;

			// Use the static HTML library to build the image tag

			$image = JHTML::_('image', 'images/stories/'.$params->get('image'), JText::_('NEWS_FEEDS'), $attribs);
		}

		$menus	= &JSite::getMenu();
		$menu	= $menus->getActive();

		// because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object( $menu )) {
			$menu_params = new JParameter( $menu->params );
			if (!$menu_params->get( 'page_title')) {
				$params->set('page_title',	JText::_( 'Newsfeeds' ));
			}
		} else {
			$params->set('page_title',	JText::_( 'Newsfeeds' ));
		}
		$document	= &JFactory::getDocument();
		$document->setTitle( $params->get( 'page_title' ) );

		$this->assignRef('image',		$image);
		$this->assignRef('params',		$params);
		$this->assignRef('categories',	$categories);

		parent::display($tpl);
	}
}
?>
