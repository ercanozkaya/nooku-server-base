<?php
/**
 * @version		$Id: archive.php 14401 2010-01-26 14:10:00Z louis $
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

jimport('joomla.application.component.model');

/**
 * Content Component Archive Model
 *
 * @package 	Joomla
 * @subpackage	Content
 * @since		1.5
 */
class ContentModelArchive extends JModel
{
	/**
	 * Article list array
	 *
	 * @var array
	 */
	var $_data = array();

	/**
	 * Article total
	 *
	 * @var integer
	 */
	var $_total = array();

	/**
	 * Method to get the archived article list
	 *
	 * @access public
	 * @return array
	 */
	function getData()
	{
		global $mainframe;
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			// Get the page/component configuration
			$params = &$mainframe->getParams();

			// Get the pagination request variables
			$limit		= JRequest::getVar('limit', $params->get('display_num', 20), '', 'int');
			$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

			$query = $this->_buildQuery();

			$this->_data = $this->_getList($query, $limitstart, $limit);
		}

		return $this->_data;
	}

	/**
	 * Method to get the total number of content items for the frontpage
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	// JModel override to add alternating value for $odd
	function &_getList( $query, $limitstart=0, $limit=0 )
	{
		$result =& parent::_getList($query, $limitstart, $limit);

		$odd = 1;
		foreach ($result as $k => $row) {
			$result[$k]->odd = $odd;
			$odd = 1 - $odd;
		}

		return $result;
	}

	function _buildQuery()
	{
		global $mainframe;
		// Get the page/component configuration
		$params = &$mainframe->getParams();

		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();

		$query = 'SELECT a.id, a.title, a.alias, a.title_alias, a.introtext, a.sectionid, a.state, a.catid, a.created, a.created_by, a.created_by_alias, a.modified, a.modified_by,'.
			' a.checked_out, a.checked_out_time, a.publish_up, a.publish_down, a.attribs, a.hits, a.images, a.urls, a.ordering, a.metakey, a.metadesc, a.access, cc.title AS category, s.title AS section,' .
			' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug,'.
			' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug,'.
			' CHAR_LENGTH( a.`fulltext` ) AS readmore, u.name AS author, u.usertype, g.name AS groups'.
			' FROM #__content AS a' .
			' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
			' LEFT JOIN #__sections AS s ON s.id = a.sectionid' .
			' LEFT JOIN #__users AS u ON u.id = a.created_by' .
			' LEFT JOIN #__groups AS g ON a.access = g.id'.
			$where.
			$orderby;

		return $query;
	}

	function _buildContentOrderBy()
	{
		$filter_order		= JRequest::getCmd('filter_order');
		$filter_order_Dir	= JRequest::getWord('filter_order_Dir');

		$orderby = ' ORDER BY ';
		if ($filter_order && $filter_order_Dir) {
			$orderby .= $filter_order.' '.$filter_order_Dir.', ';
		}
		
		if (!in_array($filter_order, array('a.id', 'a.title', 'a.alias', 'a.title_alias', 'a.introtext', 'a.sectionid', 'a.state', 'a.catid',
			'a.created', 'a.created_by', 'a.created_by_alias', 'a.modified', 'a.modified_by', 'a.hits', 'a.ordering', 'cc.title', 's.title'))) {
			$filter_order = '';
		}

		if (!in_array(strtoupper($filter_order_Dir), array('ASC', 'DESC'))) {
			$filter_order_Dir = '';
		}

		// Get the page/component configuration
		$params = $this->getState('parameters.menu');
		if (!is_object($params)) {
			$params = &JComponentHelper::getParams('com_articles');
		}

		// Special ordering for archive articles
		$orderby_sec	= $params->def('orderby', 'rdate');
		$primary		= ContentHelperQuery::orderbySecondary($orderby_sec);
		$orderby		.= $primary;

		return $orderby;
	}

	function _buildContentWhere()
	{
		global $mainframe;

		// Initialize some variables
		$user	=& JFactory::getUser();
		$db		=& JFactory::getDBO();
		$aid	= (int) $user->get('aid', 0);

		// First thing we need to do is build the access section of the clause
		$where = ' WHERE a.access <= '.$aid;
		$where .= ' AND s.access <= '.$aid;
		$where .= ' AND cc.access <= '.$aid;
		$where .= ' AND s.published = 1';
		$where .= ' AND cc.published = 1';

		$where .= ' AND a.state = \'-1\'';
		$year	= JRequest::getInt( 'year' );
		if ($year) {
			$where .= ' AND YEAR( a.created ) = \''.$year.'\'';
		}
		$month	= JRequest::getInt( 'month' );
		if ($month) {
			$where .= ' AND MONTH( a.created ) = \''.$month.'\'';
		}

		/*
		 * If we have a filter... lets tack the AND clause
		 * for the filter onto the WHERE clause of the archive query.
		 */
		$filter = JRequest::getString('filter', '', 'post');
		if ($filter) {
			// clean filter variable
			$filter = JString::strtolower($filter);
			$filter	= $db->Quote( '%'.$db->getEscaped( $filter, true ).'%', false );

			// Get the page/component configuration
			$params = &$mainframe->getParams();
			switch ($params->get('filter_type', 'title'))
			{
				case 'title' :
				     default :
					$where .= ' AND LOWER( a.title ) LIKE '.$filter;
					break;

				case 'author' :
					$where .= ' AND ( ( LOWER( u.name ) LIKE '.$filter.' ) OR ( LOWER( a.created_by_alias ) LIKE '.$filter.' ) )';
					break;

				case 'hits' :
					$where .= ' AND a.hits LIKE '.$filter;
					break;
			}
		}
		return $where;
	}
}
