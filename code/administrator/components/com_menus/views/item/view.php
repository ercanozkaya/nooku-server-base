<?php
/**
* @version		$Id: view.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @subpackage	Menus
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

jimport('joomla.application.component.view');
jimport('joomla.html.pane');

/**
 * @package		Joomla
 * @subpackage	Menus
 * @since 1.5
 */
class MenusViewItem extends JView
{
	var $_name = 'item';

	function edit($tpl = null)
	{
		JRequest::setVar( 'hidemainmenu', 1 );

		global $mainframe;

		$lang =& JFactory::getLanguage();
		$this->_layout = 'form';

		$item = &$this->get('Item');

		// clean item data
		JFilterOutput::objectHTMLSafe( $item, ENT_QUOTES, '' );

		// Set toolbar items for the page
		if (!$item->id) {
			JToolBarHelper::title(  JText::_( 'New' ).' '.JText::_( 'Menu Item' ), 'menu.png' );
		} else {
			JToolBarHelper::title( JText::_( 'Edit' ).' '.JText::_( 'Menu Item' ), 'menu.png' );
		}
		JToolBarHelper::save();
		JToolBarHelper::apply();
		if ($item->id) {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancelItem', 'Close' );
		} else {
			JToolBarHelper::cancel('cancelItem');
		}

		JSubMenuHelper::addEntry(JText::_('Items'), 'index.php?option=com_menus&task=view', true);
		JSubMenuHelper::addEntry(JText::_('Menus'), 'index.php?option=com_menus');
		if(JFactory::getUser()->authorize('com_trash', 'manage')) {
			JSubMenuHelper::addEntry(JText::_('Trash'), 'index.php?option=com_trash&task=viewMenu');
		}

		// Load component language files
		$component		= &$this->get('Component');
		$lang->load($component->option, JPATH_ADMINISTRATOR);

		// Initialize variables
		$urlparams		= $this->get( 'UrlParams' );
		$params			= $this->get( 'StateParams' );
		$sysparams		= $this->get( 'SystemParams' );
		$advanced		= $this->get( 'AdvancedParams' );
		$component		= $this->get( 'ComponentParams' );
		$name			= $this->get( 'StateName' );
		$description	= $this->get( 'StateDescription' );
		$menuTypes 		= MenusHelper::getMenuTypeList();
		$components		= MenusHelper::getComponentList();

		JHTML::_('behavior.tooltip');

		$document = & JFactory::getDocument();
		if ($item->id) {
			$document->setTitle(JText::_( 'Menu Item' ) .': ['. JText::_( 'Edit' ) .']');
		} else {
			$document->setTitle(JText::_( 'Menu Item' ) .': ['. JText::_( 'New' ) .']');
		}

		// Was showing up null in some cases....
		if (!$item->published) {
			$item->published = 0;
		}
		$lists = new stdClass();
		$lists->published = MenusHelper::Published($item);
		$lists->disabled = ($item->type != 'url' ? 'readonly="true"' : '');

		$item->expansion = null;
		if ($item->type != 'url') {
			$lists->disabled = 'readonly="true"';
			$item->linkfield = '<input type="hidden" name="link" value="'.$item->link.'" />';
			if (($item->id) && ($item->type == 'component') && (isset($item->linkparts['option']))) {
				$item->expansion = '&amp;expand='.trim(str_replace('com_', '', $item->linkparts['option']));
			}
		} else {
			$lists->disabled = null;
			$item->linkfield = null;
		}

		$this->assignRef('lists'	, $lists);
		$this->assignRef('item'		, $item);
		$this->assignRef('urlparams', $urlparams);
		$this->assignRef('sysparams', $sysparams);
		$this->assignRef('params'	, $params);
		$this->assignRef('advanced'	, $advanced);
		$this->assignRef('comp'		, $component);
		$this->assignRef('menutypes', $menuTypes);
		$this->assignRef('name'		, $name);
		$this->assignRef('description', $description);

		// Add slider pane
        // TODO: allowAllClose should default true in J!1.6, so remove the array when it does.
		$pane = &JPane::getInstance('sliders', array('allowAllClose' => true));
		$this->assignRef('pane', $pane);

		parent::display($tpl);
	}

	function type($tpl = null)
	{
		JRequest::setVar( 'hidemainmenu', 1 );

		global $mainframe;

		$lang =& JFactory::getLanguage();
		$this->_layout = 'type';

		$item = &$this->get('Item');

		// Set toolbar items for the page
		if (!$item->id) {
			JToolBarHelper::title(  JText::_( 'New' ).' '.JText::_( 'Menu Item' ), 'menu.png' );
		} else {
			JToolBarHelper::title(  JText::_( 'Change Menu Item' ), 'menu.png' );
		}

		// Set toolbar items for the page
		JToolBarHelper::cancel('view');

		// Add scripts and stylesheets to the document
		$document	= & JFactory::getDocument();

		if($lang->isRTL()){
			$document->addStyleSheet(JURI::root(true).'/media/com_menus/css/type_rtl.css');
		} else {
			$document->addStyleSheet(JURI::root(true).'/media/com_menus/css/type.css');
		}
		JHTML::_('behavior.tooltip');

		// Load component language files
		$components	= MenusHelper::getComponentList();
		$n = count($components);
		for($i = 0; $i < $n; $i++)
		{
		    $option = $components[$i]->option == 'com_articles' ? 'com_content' : $components[$i]->option;
			$path   = JPATH_SITE.DS.'components'.DS.$option.DS.'views';
			$components[$i]->legacy = !is_dir($path);

			$lang->load($components[$i]->option, JPATH_ADMINISTRATOR);
		}

		// Initialize variables
		$item			= &$this->get('Item');
		$expansion		= &$this->get('Expansion');
		$component		= &$this->get('Component');
		$name			= $this->get( 'StateName' );
		$description	= $this->get( 'StateDescription' );
		$menuTypes 		= MenusHelper::getMenuTypeList();

		// Set document title
		if ($item->id) {
			$document->setTitle(JText::_( 'Menu Item' ) .': ['. JText::_( 'Edit' ) .']');
		} else {
			$document->setTitle(JText::_( 'Menu Item' ) .': ['. JText::_( 'New' ) .']');
		}

		$this->assignRef('item',		$item);
		$this->assignRef('components',	$components);
		$this->assignRef('expansion',	$expansion);

		parent::display($tpl);
	}
}
