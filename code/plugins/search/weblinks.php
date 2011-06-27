<?php
/**
 * @version		$Id: weblinks.php 14401 2010-01-26 14:10:00Z louis $
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

$mainframe->registerEvent( 'onSearch', 'plgSearchWeblinks' );
$mainframe->registerEvent( 'onSearchAreas', 'plgSearchWeblinksAreas' );

JPlugin::loadLanguage( 'plg_search_weblinks' );

/**
 * @return array An array of search areas
 */
function &plgSearchWeblinksAreas() {
	static $areas = array(
		'weblinks' => 'Weblinks'
	);
	return $areas;
}

/**
* Weblink Search method
*
* The sql must return the following fields that are used in a common display
* routine: href, title, section, created, text, browsernav
* @param string Target search string
* @param string mathcing option, exact|any|all
* @param string ordering option, newest|oldest|popular|alpha|category
 * @param mixed An array if the search it to be restricted to areas, null if search all
 */
function plgSearchWeblinks( $text, $phrase='', $ordering='', $areas=null )
{
	$db		=& JFactory::getDBO();
	$user	=& JFactory::getUser();

	$searchText = $text;

	require_once(JPATH_SITE.DS.'components'.DS.'com_weblinks'.DS.'helpers'.DS.'route.php');

	if (is_array( $areas )) {
		if (!array_intersect( $areas, array_keys( plgSearchWeblinksAreas() ) )) {
			return array();
		}
	}

	// load plugin params info
 	$plugin =& JPluginHelper::getPlugin('search', 'weblinks');
 	$pluginParams = new JParameter( $plugin->params );

	$limit = $pluginParams->def( 'search_limit', 50 );

	$text = trim( $text );
	if ($text == '') {
		return array();
	}
	$section 	= JText::_( 'Web Links' );

	$wheres 	= array();
	switch ($phrase)
	{
		case 'exact':
			$text		= $db->Quote( '%'.$db->getEscaped( $text, true ).'%', false );
			$wheres2 	= array();
			$wheres2[] 	= 'a.url LIKE '.$text;
			$wheres2[] 	= 'a.description LIKE '.$text;
			$wheres2[] 	= 'a.title LIKE '.$text;
			$where 		= '(' . implode( ') OR (', $wheres2 ) . ')';
			break;

		case 'all':
		case 'any':
		default:
			$words 	= explode( ' ', $text );
			$wheres = array();
			foreach ($words as $word)
			{
				$word		= $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
				$wheres2 	= array();
				$wheres2[] 	= 'a.url LIKE '.$word;
				$wheres2[] 	= 'a.description LIKE '.$word;
				$wheres2[] 	= 'a.title LIKE '.$word;
				$wheres[] 	= implode( ' OR ', $wheres2 );
			}
			$where 	= '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
			break;
	}

	switch ( $ordering )
	{
		case 'oldest':
			$order = 'a.date ASC';
			break;

		case 'popular':
			$order = 'a.hits DESC';
			break;

		case 'alpha':
			$order = 'a.title ASC';
			break;

		case 'category':
			$order = 'b.title ASC, a.title ASC';
			break;

		case 'newest':
		default:
			$order = 'a.date DESC';
	}

	$query = 'SELECT a.title AS title, a.description AS text, a.date AS created, a.url, '
	. ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug, '
	. ' CASE WHEN CHAR_LENGTH(b.alias) THEN CONCAT_WS(\':\', b.id, b.alias) ELSE b.id END as catslug, '
	. ' CONCAT_WS( " / ", '.$db->Quote($section).', b.title ) AS section,'
	. ' "1" AS browsernav'
	. ' FROM #__weblinks AS a'
	. ' INNER JOIN #__categories AS b ON b.id = a.catid'
	. ' WHERE ('. $where .')'
	. ' AND a.published = 1'
	. ' AND b.published = 1'
	. ' AND b.access <= '.(int) $user->get( 'aid' )
	. ' ORDER BY '. $order
	;
	$db->setQuery( $query, 0, $limit );
	$rows = $db->loadObjectList();

	foreach($rows as $key => $row) {
		$rows[$key]->href = WeblinksHelperRoute::getWeblinkRoute($row->slug, $row->catslug);
	}

	$return = array();
	foreach($rows AS $key => $weblink) {
		if(searchHelper::checkNoHTML($weblink, $searchText, array('url', 'text', 'title'))) {
			$return[] = $weblink;
		}
	}

	return $return;
}
