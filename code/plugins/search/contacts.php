<?php
/**
 * @version		$Id: contacts.php 14401 2010-01-26 14:10:00Z louis $
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

$mainframe->registerEvent( 'onSearch', 'plgSearchContacts' );
$mainframe->registerEvent( 'onSearchAreas', 'plgSearchContactAreas' );

JPlugin::loadLanguage( 'plg_search_contacts' );

/**
 * @return array An array of search areas
 */
function &plgSearchContactAreas()
{
	static $areas = array(
		'contacts' => 'Contacts'
	);
	return $areas;
}

/**
* Contacts Search method
*
* The sql must return the following fields that are used in a common display
* routine: href, title, section, created, text, browsernav
* @param string Target search string
* @param string mathcing option, exact|any|all
* @param string ordering option, newest|oldest|popular|alpha|category
*/
function plgSearchContacts( $text, $phrase='', $ordering='', $areas=null )
{
	$db		=& JFactory::getDBO();
	$user	=& JFactory::getUser();

	if (is_array( $areas )) {
		if (!array_intersect( $areas, array_keys( plgSearchContactAreas() ) )) {
			return array();
		}
	}

	// load plugin params info
 	$plugin =& JPluginHelper::getPlugin('search', 'contacts');
 	$pluginParams = new JParameter( $plugin->params );

	$limit = $pluginParams->def( 'search_limit', 50 );

	$text = trim( $text );
	if ($text == '') {
		return array();
	}

	$section = JText::_( 'Contact' );

	switch ( $ordering ) {
		case 'alpha':
			$order = 'a.name ASC';
			break;

		case 'category':
			$order = 'b.title ASC, a.name ASC';
			break;

		case 'popular':
		case 'newest':
		case 'oldest':
		default:
			$order = 'a.name DESC';
	}

	$text	= $db->Quote( '%'.$db->getEscaped( $text, true ).'%', false );
	$query	= 'SELECT a.name AS title, "" AS created,'
	. ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug, '
	. ' CASE WHEN CHAR_LENGTH(b.alias) THEN CONCAT_WS(\':\', b.id, b.alias) ELSE b.id END AS catslug, '
	. ' CONCAT_WS( ", ", a.name, a.con_position, a.misc ) AS text,'
	. ' CONCAT_WS( " / ", '.$db->Quote($section).', b.title ) AS section,'
	. ' "2" AS browsernav'
	. ' FROM #__contact_details AS a'
	. ' INNER JOIN #__categories AS b ON b.id = a.catid'
	. ' WHERE ( a.name LIKE '.$text
	. ' OR a.misc LIKE '.$text
	. ' OR a.con_position LIKE '.$text
	. ' OR a.address LIKE '.$text
	. ' OR a.suburb LIKE '.$text
	. ' OR a.state LIKE '.$text
	. ' OR a.country LIKE '.$text
	. ' OR a.postcode LIKE '.$text
	. ' OR a.telephone LIKE '.$text
	. ' OR a.fax LIKE '.$text.' )'
	. ' AND a.published = 1'
	. ' AND b.published = 1'
	. ' AND a.access <= '.(int) $user->get( 'aid' )
	. ' AND b.access <= '.(int) $user->get( 'aid' )
	. ' GROUP BY a.id'
	. ' ORDER BY '. $order
	;
	$db->setQuery( $query, 0, $limit );
	$rows = $db->loadObjectList();

	foreach($rows as $key => $row) {
		$rows[$key]->href = 'index.php?option=com_contact&view=contact&id='.$row->slug.'&catid='.$row->catslug;
	}

	return $rows;
}
