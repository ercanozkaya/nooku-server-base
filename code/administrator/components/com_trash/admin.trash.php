<?php
/**
* @version		$Id: admin.trash.php 18162 2010-07-16 07:00:47Z ian $
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

/*
 * Make sure the user is authorized to view this page
 */
$user = & JFactory::getUser();
if (!$user->authorize( 'com_trash', 'manage' )) {
	$mainframe->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
}

require_once( JApplicationHelper::getPath( 'admin_html' ) );

if($task == 'viewMenu')
{
	JSubMenuHelper::addEntry(JText::_('Items'), 'index.php?option=com_menus&task=view');
	JSubMenuHelper::addEntry(JText::_('Menus'), 'index.php?option=com_menus');
	if(JFactory::getUser()->authorize('com_trash', 'manage')) {
		JSubMenuHelper::addEntry(JText::_('Trash'), 'index.php?option=com_trash&task=viewMenu', true);
	}
}

$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );
$mid = JRequest::getVar( 'mid', array(0), 'post', 'array' );

JArrayHelper::toInteger($cid, array(0));
JArrayHelper::toInteger($mid, array(0));

switch ($task)
{
	case 'deleteconfirm':
		viewdeleteTrash( $cid, $mid, $option );
		break;

	case 'delete':
		deleteTrash( $cid, $option );
		break;

	case 'restoreconfirm':
		viewrestoreTrash( $cid, $mid, $option );
		break;

	case 'restore':
		restoreTrash( $cid, $option );
		break;

	case 'viewMenu':
		viewTrashMenu( $option );
		break;

	case 'viewContent':
		viewTrashContent( $option );
		break;

	default:
		$return = JRequest::getCmd( 'return', 'viewContent', 'post' );
		if ( $return == 'viewMenu' ) {
			viewTrashMenu( $option );
		} else {
			viewTrashContent( $option );
		}
		break;
}


/**
* Compiles a list of trash items
*/
function viewTrashContent( $option )
{
	global $mainframe;

	$db					=& JFactory::getDBO();
	$filter_order		= $mainframe->getUserStateFromRequest( "$option.viewContent.filter_order",		'filter_order',		'sectname', 'cmd' );
	$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.viewContent.filter_order_Dir",	'filter_order_Dir',	'',			'word' );
	$search				= $mainframe->getUserStateFromRequest( "$option.search",						'search', 			'',			'string' );
	if (strpos($search, '"') !== false) {
		$search = str_replace(array('=', '<'), '', $search);
	}
	$search = JString::strtolower($search);

	$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
	$limitstart = $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

	$where[] = 'c.state = -2';

	if ($search) {
		$where[] = 'LOWER(c.title) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
	}

	$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

	// ensure filter_order has a valid value
	if (!in_array($filter_order, array('c.title', 'c.id', 'sectname', 'catname'))) {
		$filter_order = 'sectname';
	}

	if (!in_array(strtoupper($filter_order_Dir), array('ASC', 'DESC'))) {
		$filter_order_Dir = '';
	}

	$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', s.name, cc.name, c.title';

	// get the total number of content
	$query = 'SELECT count(c.id)'
	. ' FROM #__content AS c'
	. ' LEFT JOIN #__categories AS cc ON cc.id = c.catid'
	. ' LEFT JOIN #__sections AS s ON s.id = cc.section AND s.scope = "content"'
	. ' LEFT JOIN #__groups AS g ON g.id = c.access'
	. ' LEFT JOIN #__users AS u ON u.id = c.checked_out'
	. $where
	;
	$db->setQuery( $query );
	$total = $db->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );

	// Query articles
	$query = 'SELECT c.title, c.id, c.sectionid, c.catid, g.name AS groupname, cc.title AS catname, s.title AS sectname'
	. ' FROM #__content AS c'
	. ' LEFT JOIN #__categories AS cc ON cc.id = c.catid'
	. ' LEFT JOIN #__sections AS s ON s.id = cc.section AND s.scope="content"'
	. ' LEFT JOIN #__groups AS g ON g.id = c.access'
	. ' LEFT JOIN #__users AS u ON u.id = c.checked_out'
	. $where
	. $orderby
	;
	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$contents = $db->loadObjectList();

	for ( $i = 0; $i < count($contents); $i++ ) {
		if ( ( $contents[$i]->sectionid == 0 ) && ( $contents[$i]->catid == 0 ) ) {
			$contents[$i]->sectname = JText::_('UNCATEGORIZED');
		}
	}

	// table ordering
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;

	// search filter
	$lists['search']= $search;

	HTML_trash::showListContent( $option, $contents, $pageNav, $lists );
}

/**
* Compiles a list of trash items
*/
function viewTrashMenu( $option )
{
	global $mainframe;

	$db					=& JFactory::getDBO();
	$filter_order		= $mainframe->getUserStateFromRequest( "$option.viewMenu.filter_order",		'filter_order',		'm.menutype',	'cmd' );
	$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.viewMenu.filter_order_Dir",	'filter_order_Dir',	'',				'word' );
	$limit				= $mainframe->getUserStateFromRequest( "limit",								'limit',			$mainframe->getCfg('list_limit'), 'int' );
	$limitstart 		= $mainframe->getUserStateFromRequest( "$option.viewMenu.limitstart",		'limitstart', 		0,				'int' );
	$search				= $mainframe->getUserStateFromRequest( "$option.search",					'search',			'',				'string' );
	if (strpos($search, '"') !== false) {
		$search = str_replace(array('=', '<'), '', $search);
	}
	$search = JString::strtolower($search);

	$where[] = 'm.published = -2';

	if ($search) {
		$where[] = 'LOWER(m.name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
	}

	$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

	// ensure filter_order has a valid value
	if (!in_array($filter_order, array('m.name', 'm.id', 'm.menutype', 'm.type'))) {
		$filter_order = 'm.menutype';
	}

	$orderby 	= ' ORDER BY '. $filter_order . ' ' . $filter_order_Dir .', m.menutype, m.ordering, m.ordering,  m.name';

	$query = 'SELECT count(*)'
	. ' FROM #__menu AS m'
	. ' LEFT JOIN #__users AS u ON u.id = m.checked_out'
	. $where
	;
	$db->setQuery( $query );
	$total = $db->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );

	// Query menu items
	$query = 'SELECT m.name, m.id, m.menutype, m.type, com.name AS com_name'
	. ' FROM #__menu AS m'
	. ' LEFT JOIN #__users AS u ON u.id = m.checked_out'
	. ' LEFT JOIN #__components AS com ON com.id = m.componentid AND m.type = "components"'
	. $where
	. $orderby
	;
	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$menus = $db->loadObjectList();

	// table ordering
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;

	// search filter
	$lists['search']= $search;

	HTML_trash::showListMenu( $option, $menus, $pageNav, $lists );
}


/**
* Compiles a list of the items you have selected to permanently delte
*/
function viewdeleteTrash( $cid, $mid, $option )
{
	global $mainframe;

	$db =& JFactory::getDBO();
	$return = JRequest::getCmd( 'return', 'viewContent', 'post' );

	JArrayHelper::toInteger($cid, array(0));
	JArrayHelper::toInteger($mid, array(0));

	// seperate contentids
	$cids = implode( ',', $cid );
	$mids = implode( ',', $mid );

	if ( $cids ) {
		// Articles query
		$query = 	'SELECT a.title AS name'
		. ' FROM #__content AS a'
		. ' WHERE ( a.id IN ( '.$cids.' ) )'
		. ' ORDER BY a.title'
		;
		$db->setQuery( $query );
		$items = $db->loadObjectList();
		$id = $cid;
		$type = "content";
	} else if ( $mids ) {
		// Articles query
		$query = 	'SELECT a.name'
		. ' FROM #__menu AS a'
		. ' WHERE ( a.id IN ( '.$mids.' ) )'
		. ' ORDER BY a.name'
		;
		$db->setQuery( $query );
		$items = $db->loadObjectList();
		$id = $mid;
		$type = "menu";
	}

	HTML_trash::showDelete( $option, $id, $items, $type, $return );
}


/**
* Permanently deletes the selected list of trash items
*/
function deleteTrash( $cid, $option )
{
	global $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$db		=& JFactory::getDBO();
	$return	= JRequest::getCmd( 'return', 'viewContent', 'post' );
	$type	= JRequest::getCmd( 'type', '', 'post' );

	$total = count( $cid );

	if ( $type == 'content' )
	{
		$obj =& JTable::getInstance('content');

		require_once (JPATH_SITE.DS.'components'.DS.'com_articles'.DS.'tables'.DS.'frontpage.php');
		$fp = new TableFrontPage( $db );
		foreach ( $cid as $id ) {
			$id = intval( $id );
			$obj->delete( $id );
			$fp->delete( $id );
		}
	} else if ( $type == "menu" ) {
		$obj =& JTable::getInstance('menu');
		foreach ( $cid as $id ) {
			$id = intval( $id );
			$obj->delete( $id );
		}
	}

	$msg = JText::sprintf( 'Item(s) successfully Deleted', $total );
	$mainframe->redirect( 'index.php?option='.$option.'&task='.$return, $msg );
}


/**
* Compiles a list of the items you have selected to permanently delte
*/
function viewrestoreTrash( $cid, $mid, $option ) {
	global $mainframe;

	$db		=& JFactory::getDBO();
	$return = JRequest::getCmd( 'return', 'viewContent', 'post' );

	JArrayHelper::toInteger($cid, array(0));
	JArrayHelper::toInteger($mid, array(0));

	// seperate contentids
	$cids = implode( ',', $cid );
	$mids = implode( ',', $mid );

	if ( $cids ) {
		// Articles query
		$query = 'SELECT a.title AS name'
		. ' FROM #__content AS a'
		. ' WHERE ( a.id IN ( '.$cids.' ) )'
		. ' ORDER BY a.title'
		;
		$db->setQuery( $query );
		$items = $db->loadObjectList();
		$id = $cid;
		$type = "content";
	} else if ( $mids ) {
		// Articles query
		$query = 'SELECT a.name'
		. ' FROM #__menu AS a'
		. ' WHERE ( a.id IN ( '.$mids.' ) )'
		. ' ORDER BY a.name'
		;
		$db->setQuery( $query );
		$items = $db->loadObjectList();
		$id = $mid;
		$type = "menu";
	}

	HTML_trash::showRestore( $option, $id, $items, $type, $return );
}


/**
* Restores items selected to normal - restores to an unpublished state
*/
function restoreTrash( $cid, $option )
{
	global $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or jexit( 'Invalid Token' );

	$db		= & JFactory::getDBO();
	$type	= JRequest::getCmd( 'type', '', 'post' );

	$total = count( $cid );

	// restores to an unpublished state
	$state 		= 0;
	$ordering 	= 9999;

	if ( $type == 'content' ) {
		$return = 'viewContent';

		//seperate contentids
		JArrayHelper::toInteger($cid, array(0));
		$cids = implode( ',', $cid );

		// query to restore article
		$query = 'UPDATE #__content'
		. ' SET state = '.(int) $state.', ordering = '.(int) $ordering
		. ' WHERE id IN ( '.$cids.' )'
		;
		$db->setQuery( $query );
		if ( !$db->query() ) {
			JError::raiseError(500, $db->getErrorMsg() );
		}
	} else if ( $type == 'menu' ) {
		$return = 'viewMenu';

		jimport('joomla.application.component.model');
		JModel::addIncludePath(JPATH_BASE.DS.'components'.DS.'com_menus'.DS.'models');
		$model =& JModel::getInstance('List', 'MenusModel');
		$total = $model->fromTrash($cid);

		if (!$total) {
			JError::raiseError(500, $db->getErrorMsg() );
		}
	}

	$msg = JText::sprintf( 'Item(s) successfully Restored', $total );
	$mainframe->redirect( 'index.php?option='.$option.'&task='.$return, $msg );
}

function ReadMenuXML( $type, $component=-1 )
{
	// xml file for module
	$xmlfile = JPATH_ADMINISTRATOR .'/components/com_menus/'. $type .'/'. $type .'.xml';

	$data = JApplicationHelper::parseXMLInstallFile($xmlfile);

	if ( $data['type'] == 'component' || $data['type'] == 'menu' )
	{
		if ( ( $component <> -1 ) && ( $data['name'] == 'Component') ) {
			$data['name'] .= ' - '. $component;
		}

		$row[0]	= $data['name'];
	}

	return $row;
}