<?php
/**
* @version		$Id: helper.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

/// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');

class modSectionsHelper
{
	function getList(&$params)
	{
		global $mainframe;

		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();

		$count	= intval($params->get('count', 20));
		$contentConfig 	= &JComponentHelper::getParams( 'com_content' );
		$access	= !$contentConfig->get('show_noauth');

		$gid 		= $user->get('aid', 0);
		$now		= date('Y-m-d H:i:s', time() + $mainframe->getCfg('offset') * 60 * 60);
		$nullDate	= $db->getNullDate();


		$query = 'SELECT a.id AS id, a.title AS title, COUNT(b.id) as cnt' .
			' FROM #__sections as a' .
			' LEFT JOIN #__content as b ON a.id = b.sectionid' .
			($access ? ' AND b.access <= '.(int) $gid : '') .
			' AND ( b.publish_up = '.$db->Quote($nullDate).' OR b.publish_up <= '.$db->Quote($now).' )' .
			' AND ( b.publish_down = '.$db->Quote($nullDate).' OR b.publish_down >= '.$db->Quote($now).' )' .
			' WHERE a.scope = "content"' .
			' AND a.published = 1' .
			($access ? ' AND a.access <= '.(int) $gid : '') .
			' GROUP BY a.id '.
			' HAVING COUNT( b.id ) > 0' .
			' ORDER BY a.ordering';
		$db->setQuery($query, 0, $count);
		$rows = $db->loadObjectList();

		return $rows;
	}
}
