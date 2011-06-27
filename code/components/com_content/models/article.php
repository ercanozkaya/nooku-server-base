<?php
/**
 * @version		$Id: article.php 14401 2010-01-26 14:10:00Z louis $
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
 * Content Component Article Model
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class ContentModelArticle extends JModel
{
	/**
	 * Article data
	 *
	 * @var object
	 */
	var $_article = null;

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();

		$id = JRequest::getVar('id', 0, '', 'int');
		$this->setId((int)$id);
	}

	/**
	 * Method to set the article id
	 *
	 * @access	public
	 * @param	int	Article ID number
	 */
	function setId($id)
	{
		// Set new article ID and wipe data
		$this->_id		= $id;
		$this->_article	= null;
	}

	/**
	 * Overridden set method to pass properties on to the article
	 *
	 * @access	public
	 * @param	string	$property	The name of the property
	 * @param	mixed	$value		The value of the property to set
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function set( $property, $value=null )
	{
		if ($this->_loadArticle()) {
			$this->_article->$property = $value;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Overridden get method to get properties from the article
	 *
	 * @access	public
	 * @param	string	$property	The name of the property
	 * @param	mixed	$value		The value of the property to set
	 * @return 	mixed 				The value of the property
	 * @since	1.5
	 */
	function get($property, $default=null)
	{
		if ($this->_loadArticle()) {
			if(isset($this->_article->$property)) {
				return $this->_article->$property;
			}
		}
		return $default;
	}

	/**
	 * Method to get content article data for the frontpage
	 *
	 * @since 1.5
	 */
	function &getArticle()
	{
		// Load the Category data
		if ($this->_loadArticle())
		{
			$user	= & JFactory::getUser();

			// Is the category published?
			if (!$this->_article->cat_pub && $this->_article->catid) {
				JError::raiseError( 404, JText::_("Article category not published") );
			}

			// Is the section published?
			if ($this->_article->sectionid)
			{
				if ($this->_article->sec_pub === null)
				{
					// probably a new item
					// check the sectionid probably passed in the request
					$db =& $this->getDBO();
					$query = 'SELECT published' .
							' FROM #__sections' .
							' WHERE id = ' . (int) $this->_article->sectionid;
					$db->setQuery( $query );
					$this->_article->sec_pub = $db->loadResult();
				}
				if (!$this->_article->sec_pub)
				{
					JError::raiseError( 404, JText::_("Article section not published") );
				}
			}

			// Do we have access to the category?
			if (($this->_article->cat_access > $user->get('aid', 0)) && $this->_article->catid) {
				JError::raiseError( 403, JText::_("ALERTNOTAUTH") );
			}

			// Do we have access to the section?
			if (($this->_article->sec_access > $user->get('aid', 0)) && $this->_article->sectionid) {
				JError::raiseError( 403, JText::_("ALERTNOTAUTH") );
			}

			$this->_loadArticleParams();
		}
		else
		{
			$user =& JFactory::getUser();
			$article =& JTable::getInstance('content');
			$article->state			= 1;
			$article->cat_pub		= null;
			$article->sec_pub		= null;
			$article->cat_access	= null;
			$article->sec_access	= null;
			$article->author		= null;
			$article->created_by	= $user->get('id');
			$article->parameters	= new JParameter( '' );
			$article->text			= '';
			$this->_article			= $article;
		}

		return $this->_article;
	}

	/**
	 * Tests if article is checked out
	 *
	 * @access	public
	 * @param	int	A user id
	 * @return	boolean	True if checked out
	 * @since	1.5
	 */
	function isCheckedOut( $uid=0 )
	{
		if ($this->_loadArticle())
		{
			if ($uid) {
				return ($this->_article->checked_out && $this->_article->checked_out != $uid);
			} else {
				return $this->_article->checked_out;
			}
		} elseif ($this->_id < 1) {
			return false;
		} else {
			JError::raiseWarning( 0, 'Unable to Load Data');
			return false;
		}
	}

	/**
	 * Method to checkin/unlock the article
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function checkin()
	{
		if ($this->_id)
		{
			$article = & JTable::getInstance('content');
			return $article->checkin($this->_id);
		}
		return false;
	}

	/**
	 * Method to checkout/lock the article
	 *
	 * @access	public
	 * @param	int	$uid	User ID of the user checking the article out
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function checkout($uid = null)
	{
		if ($this->_id)
		{
			// Make sure we have a user id to checkout the article with
			if (is_null($uid)) {
				$user	=& JFactory::getUser();
				$uid	= $user->get('id');
			}
			// Lets get to it and checkout the thing...
			$article = & JTable::getInstance('content');
			return $article->checkout($uid, $this->_id);
		}
		return false;
	}

	/**
	 * Method to store the article
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function store($data)
	{
		global $mainframe;

		$article  =& JTable::getInstance('content');
		$user     =& JFactory::getUser();
		$dispatcher =& JDispatcher::getInstance();
		JPluginHelper::importPlugin('content');

		// Bind the form fields to the web link table
		if (!$article->bind($data, "published")) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// sanitise id field
		$article->id = (int) $article->id;

		$isNew = ($article->id < 1);
		if ($isNew)
		{
			$article->created 		= gmdate('Y-m-d H:i:s');
			$article->created_by 	= $user->get('id');
		}
		else
		{
			$article->modified 		= gmdate('Y-m-d H:i:s');
			$article->modified_by 	= $user->get('id');
		}

		// Append time if not added to publish date
		if (strlen(trim($article->publish_up)) <= 10) {
			$article->publish_up .= ' 00:00:00';
		}

		$date =& JFactory::getDate($article->publish_up, $mainframe->getCfg('offset'));
		$article->publish_up = $date->toMySQL();

		// Handle never unpublish date
		if (trim($article->publish_down) == JText::_('Never') || trim( $article->publish_down ) == '')
		{
			$article->publish_down = $this->_db->getNullDate();;
		}
		else
		{
			if (strlen(trim( $article->publish_down )) <= 10) {
				$article->publish_down .= ' 00:00:00';
			}

			$date =& JFactory::getDate($article->publish_down, $mainframe->getCfg('offset'));
			$article->publish_down = $date->toMySQL();
		}

		$article->title = trim( $article->title );

		// get state and created_by from existing article
		$originalState = 0;
		if (!$isNew)
		{
			$query = 'SELECT state, created_by' .
			' FROM #__content' .
			' WHERE id = '.(int) $article->id;
			$this->_db->setQuery($query);
			$originalArticle = $this->_db->loadObject();
			$originalState = $originalArticle->state;
			// force the created_by to the existing value
			$article->created_by = $originalArticle->created_by;
		}

		// Publishing state hardening for Authors
		if (!$user->authorize('com_content', 'publish', 'content', 'all'))
		{
			if ($isNew)
			{
				// For new items - author is not allowed to publish - prevent them from doing so
				$article->state = 0;
			}
			else
			{
				// For existing items keep existing state - author is not allowed to change status

				$state = $originalState;

				if ($state) {
					$article->state = 1;
				}
				else {
					$article->state = 0;
				}

				// if current user is author, check that the current user is really the author
				if (!$user->authorize('com_content', 'edit', 'content', 'all'))
				{
					if ($originalArticle->created_by != $user->id)
					{
						JError::raiseError( 403, JText::_("ALERTNOTAUTH") );
					}
				}
			}
		}

		// Search for the {readmore} tag and split the text up accordingly.
		$text = str_replace('<br>', '<br />', $data['text']);

		$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
		$tagPos	= preg_match($pattern, $text);

		if ($tagPos == 0)	{
			$article->introtext	= $text;
		} else 	{
			list($article->introtext, $article->fulltext) = preg_split($pattern, $text, 2);
		}

		// Filter settings
		jimport( 'joomla.application.component.helper' );
		$config	= JComponentHelper::getParams( 'com_articles' );
		$user	= &JFactory::getUser();
		$gid	= $user->get( 'gid' );

		$filterGroups	= $config->get( 'filter_groups' );

		// convert to array if one group selected
		if ( (!is_array($filterGroups) && (int) $filterGroups > 0) ) { 
			$filterGroups = array($filterGroups);
		}

		if (is_array($filterGroups) && in_array( $gid, $filterGroups ))
		{
			$filterType		= $config->get( 'filter_type' );
			$filterTags		= preg_split( '#[,\s]+#', trim( $config->get( 'filter_tags' ) ) );
			$filterAttrs	= preg_split( '#[,\s]+#', trim( $config->get( 'filter_attritbutes' ) ) );
			switch ($filterType)
			{
				case 'NH':
					$filter	= new JFilterInput();
					break;
				case 'WL':
					$filter	= new JFilterInput( $filterTags, $filterAttrs, 0, 0 );
					break;
				case 'BL':
				default:
					$filter	= new JFilterInput( $filterTags, $filterAttrs, 1, 1 );
					break;
			}
			$article->introtext	= $filter->clean( $article->introtext );
			$article->fulltext	= $filter->clean( $article->fulltext );
		} elseif(empty($filterGroups)) {
			$filter = new JFilterInput(array(), array(), 1, 1);
			$article->introtext = $filter->clean( $article->introtext );
			$article->fulltext = $filter->clean( $article->fulltext );
		}

		// Make sure the article table is valid
		if (!$article->check()) {
			$this->setError($article->getError());
			return false;
		}

		$article->version++;

		//Trigger OnBeforeContentSave
		$result = $dispatcher->trigger('onBeforeContentSave', array(&$article, $isNew));
		if(in_array(false, $result, true)) {
			$this->setError($article->getError());
			return false;
		}

		// Store the article table to the database
		if (!$article->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if ($isNew)
		{
			$this->_id = $article->_db->insertId();
		}

		$article->reorder("catid = " . (int) $data['catid']);

		//Trigger OnAfterContentSave
		$dispatcher->trigger('onAfterContentSave', array(&$article, $isNew));

		$this->_article	=& $article;

		return true;
	}

	/**
	 * Method to load content article data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _loadArticle()
	{
		global $mainframe;

		if($this->_id == '0')
		{
			return false;
		}

		// Load the content if it doesn't already exist
		if (empty($this->_article))
		{
			// Get the page/component configuration
			$params = &$mainframe->getParams();

			// Get the WHERE clause
			$where	= $this->_buildContentWhere();

			$query = 'SELECT a.*, u.name AS author, u.usertype, cc.title AS category, s.title AS section,' .
					' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
					' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug,'.
					' g.name AS groups, s.published AS sec_pub, cc.published AS cat_pub, s.access AS sec_access, cc.access AS cat_access'.
					' FROM #__content AS a' .
					' LEFT JOIN #__categories AS cc ON cc.id = a.catid' .
					' LEFT JOIN #__sections AS s ON s.id = cc.section AND s.scope = "content"' .
					' LEFT JOIN #__users AS u ON u.id = a.created_by' .
					' LEFT JOIN #__groups AS g ON a.access = g.id'.
					$where;
			$this->_db->setQuery($query);
			$this->_article = $this->_db->loadObject();

			if ( ! $this->_article ) {
				return false;
			}

			if($this->_article->publish_down == $this->_db->getNullDate()) {
				$this->_article->publish_down = JText::_('Never');
			}

			return true;
		}
		return true;
	}

	/**
	 * Method to load content article parameters
	 *
	 * @access	private
	 * @return	void
	 * @since	1.5
	 */
	function _loadArticleParams()
	{
		global $mainframe;

		// Get the page/component configuration
		$params = clone($mainframe->getParams('com_articles'));

		// Merge article parameters into the page configuration
		$aparams = new JParameter($this->_article->attribs);
		$params->merge($aparams);

		// Set the popup configuration option based on the request
		$pop = JRequest::getVar('pop', 0, '', 'int');
		$params->set('popup', $pop);

		// Are we showing introtext with the article
		if (!$params->get('show_intro') && !empty($this->_article->fulltext)) {
			$this->_article->text = $this->_article->fulltext;
		} else {
			$this->_article->text = $this->_article->introtext . chr(13).chr(13) . $this->_article->fulltext;
		}

		// Set the article object's parameters
		$this->_article->parameters = & $params;
	}

	/**
	 * Method to build the WHERE clause of the query to select a content article
	 *
	 * @access	private
	 * @return	string	WHERE clause
	 * @since	1.5
	 */
	function _buildContentWhere()
	{
		global $mainframe;

		$user		=& JFactory::getUser();
		$aid		= (int) $user->get('aid', 0);

		$jnow		=& JFactory::getDate();
		$now		= $jnow->toMySQL();
		$nullDate	= $this->_db->getNullDate();

		/*
		 * First thing we need to do is assert that the content article is the one
		 * we are looking for and we have access to it.
		 */
		$where = ' WHERE a.id = '. (int) $this->_id;
//		$where .= ' AND a.access <= '. (int) $aid;

		if (!$user->authorize('com_content', 'edit', 'content', 'all'))
		{
			$where .= ' AND ( ';
			$where .= ' ( a.created_by = ' . (int) $user->id . ' ) ';
			$where .= '   OR ';
			$where .= ' ( a.state = 1' .
					' AND ( a.publish_up = '.$this->_db->Quote($nullDate).' OR a.publish_up <= '.$this->_db->Quote($now).' )' .
					' AND ( a.publish_down = '.$this->_db->Quote($nullDate).' OR a.publish_down >= '.$this->_db->Quote($now).' )';
			$where .= '   ) ';
			$where .= '   OR ';
			$where .= ' ( a.state = -1 ) ';
			$where .= ' ) ';
		}

		return $where;
	}
}
