<?php
/**
 * @version		$Id: view.html.php 15181 2010-03-04 23:06:32Z ian $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

require_once (JPATH_COMPONENT.DS.'view.php');

/**
 * HTML View class for the Content component
 *
 * @static
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class ContentViewSection extends ContentView
{
	function display($tpl = null)
	{
		global $mainframe, $option;

		// Initialize some variables
		$user		=& JFactory::getUser();
		$document	=& JFactory::getDocument();

		// Get the page/component configuration
		$params = &$mainframe->getParams();

		// Request variables
		$limit		= JRequest::getVar('limit', $params->get('display_num'), '', 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		//parameters
		$intro		= $params->def('num_intro_articles', 	4);
		$leading	= $params->def('num_leading_articles', 	1);
		$links		= $params->def('num_links', 			4);

		$limit	= $intro + $leading + $links;
		JRequest::setVar('limit', (int) $limit);

		// Get some data from the model
		$items		= & $this->get( 'Data');
		$total		= & $this->get( 'Total');
		$categories	= & $this->get( 'Categories' );
		$section	= & $this->get( 'Section' );

		// Create a user access object for the user
		$access					= new stdClass();
		$access->canEdit		= $user->authorize('com_content', 'edit', 'content', 'all');
		$access->canEditOwn		= $user->authorize('com_content', 'edit', 'content', 'own');
		$access->canPublish		= $user->authorize('com_content', 'publish', 'content', 'all');

		//add alternate feed link
		if($params->get('show_feed_link', 1) == 1)
		{
			$link	= '&format=feed&limitstart=';
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$document->addHeadLink(JRoute::_($link.'&type=rss'), 'alternate', 'rel', $attribs);
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$document->addHeadLink(JRoute::_($link.'&type=atom'), 'alternate', 'rel', $attribs);
		}

		$menus	= &JSite::getMenu();
		$menu	= $menus->getActive();

		// because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object( $menu )) {
			$menu_params = new JParameter( $menu->params );
			if (!$menu_params->get( 'page_title')) {
				$params->set('page_title',	$section->title);
			}
		} else {
			$params->set('page_title',	$section->title);
		}
		$document->setTitle( $params->get( 'page_title' ) );

		// Prepare section description
		$section->description = JHTML::_('content.prepare', $section->description);

		for($i = 0; $i < count($categories); $i++)
		{
			$category =& $categories[$i];
			$category->link = JRoute::_(ContentHelperRoute::getCategoryRoute($category->slug, $category->section).'&layout=default');

			// Prepare category description
			$category->description = JHTML::_('content.prepare', $category->description);
		}

		if ($total == 0) {
			$params->set('show_categories', false);
		}

		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit );

		$this->assign('total',			$total);

		$this->assignRef('items',		$items);
		$this->assignRef('section',		$section);
		$this->assignRef('categories',	$categories);
		$this->assignRef('params',		$params);
		$this->assignRef('user',		$user);
		$this->assignRef('access',		$access);
		$this->assignRef('pagination',	$pagination);

		parent::display($tpl);
	}

	function &getItem( $index = 0, &$params)
	{
		global $mainframe;

		// Initialize some variables
		$user		=& JFactory::getUser();
		$dispatcher	=& JDispatcher::getInstance();

		$SiteName	= $mainframe->getCfg('sitename');

		$task		= JRequest::getCmd('task');

		$linkOn		= null;
		$linkText	= null;

		$item =& $this->items[$index];
		$item->text = $item->introtext;

		// Get the page/component configuration and article parameters
		$item->params = clone($params);
		$aparams = new JParameter($item->attribs);

		// Merge article parameters into the page configuration
		$item->params->merge($aparams);

		// Process the content preparation plugins
		JPluginHelper::importPlugin('content');
		$results = $dispatcher->trigger('onPrepareContent', array (& $item, & $item->params, 0));

		// Build the link and text of the readmore button
		if (($item->params->get('show_readmore') && @ $item->readmore) || $item->params->get('link_titles'))
		{
			// checks if the item is a public or registered/special item
			if ($item->access <= $user->get('aid', 0))
			{
				//$item->readmore_link = JRoute::_("index.php?view=article&id=".$item->slug);
				$item->readmore_link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug, $item->sectionid));
				$item->readmore_register = false;
			}
			else
			{
				$item->readmore_link = JRoute::_("index.php?option=com_user&view=login");
				$returnURL = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug, $item->sectionid),false);
				$fullURL = new JURI($item->readmore_link);
				$fullURL->setVar('return', base64_encode($returnURL));
				$item->readmore_link = $fullURL->toString();
				$item->readmore_register = true;
			}
		}

		$item->event = new stdClass();
		$results = $dispatcher->trigger('onAfterDisplayTitle', array (& $item, & $item->params,0));
		$item->event->afterDisplayTitle = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onBeforeDisplayContent', array (& $item, & $item->params, 0));
		$item->event->beforeDisplayContent = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onAfterDisplayContent', array (& $item, & $item->params, 0));
		$item->event->afterDisplayContent = trim(implode("\n", $results));

		return $item;
	}
}
