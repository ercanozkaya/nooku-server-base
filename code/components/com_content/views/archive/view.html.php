<?php
/**
 * @version		$Id: view.html.php 14401 2010-01-26 14:10:00Z louis $
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
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class ContentViewArchive extends ContentView
{
	function display($tpl = null)
	{
		global $mainframe, $option;

		if (empty( $layout ))
		{
			// degrade to default
			$layout = 'list';
		}

		// Initialize some variables
		$user		=& JFactory::getUser();
		$pathway	=& $mainframe->getPathway();
		$document	=& JFactory::getDocument();

		// Get the page/component configuration
		$params = &$mainframe->getParams('com_articles');

		// Request variables
		$task 		= JRequest::getCmd('task');
		$limit		= $mainframe->getUserStateFromRequest('com_content.'.$this->getLayout().'.limit', 'limit', $params->get('display_num', 20), 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');
		$month		= JRequest::getInt( 'month' );
		$year		= JRequest::getInt( 'year' );
		$filter		= JRequest::getString( 'filter' );
		JRequest::setVar('limit', (int) $limit);

		// Get some data from the model
		$state = & $this->get( 'state' );
		$items = & $this->get( 'data'  );
		$total = & $this->get( 'total' );

		// Add item to pathway
		$pathway->addItem(JText::_('Archive'), '');

		$params->def('filter',			1);
		$params->def('filter_type',		'title');

		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		$menus	= &JSite::getMenu();
		$menu	= $menus->getActive();

		// because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object( $menu )) {
			$menu_params = new JParameter( $menu->params );
			if (!$menu_params->get( 'page_title')) {
				$params->set('page_title',	JText::_( 'Archives' ));
			}
		} else {
			$params->set('page_title',	JText::_( 'Archives' ));
		}
		$document->setTitle( $params->get( 'page_title' ) );

		$form = new stdClass();
		// Month Field
		$months = array(
			JHTML::_('select.option',  null, JText::_( 'Month' ) ),
			JHTML::_('select.option',  '01', JText::_( 'JANUARY_SHORT' ) ),
			JHTML::_('select.option',  '02', JText::_( 'FEBRUARY_SHORT' ) ),
			JHTML::_('select.option',  '03', JText::_( 'MARCH_SHORT' ) ),
			JHTML::_('select.option',  '04', JText::_( 'APRIL_SHORT' ) ),
			JHTML::_('select.option',  '05', JText::_( 'MAY_SHORT' ) ),
			JHTML::_('select.option',  '06', JText::_( 'JUNE_SHORT' ) ),
			JHTML::_('select.option',  '07', JText::_( 'JULY_SHORT' ) ),
			JHTML::_('select.option',  '08', JText::_( 'AUGUST_SHORT' ) ),
			JHTML::_('select.option',  '09', JText::_( 'SEPTEMBER_SHORT' ) ),
			JHTML::_('select.option',  '10', JText::_( 'OCTOBER_SHORT' ) ),
			JHTML::_('select.option',  '11', JText::_( 'NOVEMBER_SHORT' ) ),
			JHTML::_('select.option',  '12', JText::_( 'DECEMBER_SHORT' ) )
		);
		$form->monthField	= JHTML::_('select.genericlist',   $months, 'month', 'size="1" class="inputbox"', 'value', 'text', $month );

		// Year Field
		$years = array();
		$years[] = JHTML::_('select.option',  null, JText::_( 'Year' ) );
		for ($i=2000; $i <= 2010; $i++) {
			$years[] = JHTML::_('select.option',  $i, $i );
		}
		$form->yearField	= JHTML::_('select.genericlist',   $years, 'year', 'size="1" class="inputbox"', 'value', 'text', $year );
		$form->limitField	= $pagination->getLimitBox();

		$this->assign('filter' 		, $filter);
		$this->assign('year'  		, $year);
		$this->assign('month' 		, $month);

		$this->assignRef('form',		$form);
		$this->assignRef('items',		$items);
		$this->assignRef('params',		$params);
		$this->assignRef('user',		$user);
		$this->assignRef('pagination',	$pagination);

		parent::display($tpl);
	}
}
?>
