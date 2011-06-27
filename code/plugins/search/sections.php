<?php
/**
 * @version		$Id: sections.php 14401 2010-01-26 14:10:00Z louis $
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

$mainframe->registerEvent( 'onSearch', 'plgSearchSections' );
$mainframe->registerEvent( 'onSearchAreas', 'plgSearchSectionAreas' );

JPlugin::loadLanguage( 'plg_search_sections' );

/**
 * @return array An array of search areas
 */
function &plgSearchSectionAreas() {
	static $areas = array(
		'sections' => 'Sections'
	);
	return $areas;
}

/**
* Sections Search method
*
* The sql must return the following fields that are used in a common display
* routine: href, title, section, created, text, browsernav
* @param string Target search string
* @param string mathcing option, exact|any|all
* @param string ordering option, newest|oldest|popular|alpha|category
 * @param mixed An array if restricted to areas, null if search all
*/
function plgSearchSections( $text, $phrase='', $ordering='', $areas=null )
{
	$db		=& JFactory::getDBO();
	$user	=& JFactory::getUser();

	$searchText = $text;

	require_once(JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');

	if (is_array( $areas )) {
		if (!array_intersect( $areas, array_keys( plgSearchSectionAreas() ) )) {
			return array();
		}
	}

	// load plugin params info
 	$plugin =& JPluginHelper::getPlugin('search', 'sections');
 	$pluginParams = new JParameter( $plugin->params );

	$limit = $pluginParams->def( 'search_limit', 50 );

	$text = trim( $text );
	if ($text == '') {
		return array();
	}

	switch ( $ordering ) {
		case 'alpha':
			$order = 'a.name ASC';
			break;

		case 'category':
		case 'popular':
		case 'newest':
		case 'oldest':
		default:
			$order = 'a.name DESC';
	}

	$text	= $db->Quote( '%'.$db->getEscaped( $text, true ).'%', false );
	$query	= 'SELECT a.title AS title, a.description AS text, a.name, '
	. ' "" AS created,'
	. ' "2" AS browsernav,'
	. ' a.id AS secid'
	. ' FROM #__sections AS a'
	. ' WHERE ( a.name LIKE '.$text
	. ' OR a.title LIKE '.$text
	. ' OR a.description LIKE '.$text.' )'
	. ' AND a.published = 1'
	. ' AND a.access <= '.(int) $user->get( 'aid' )
	. ' GROUP BY a.id'
	. ' ORDER BY '. $order
	;
	$db->setQuery( $query, 0, $limit );
	$rows = $db->loadObjectList();

	$count = count( $rows );
	for ( $i = 0; $i < $count; $i++ )
	{
		$rows[$i]->href 	= ContentHelperRoute::getSectionRoute($rows[$i]->secid);
		$rows[$i]->section 	= JText::_( 'Section' );
	}

	$return = array();
	foreach($rows AS $key => $section) {
		if(searchHelper::checkNoHTML($section, $searchText, array('name', 'title', 'text'))) {
			$return[] = $section;
		}
	}
	return $return;
}
