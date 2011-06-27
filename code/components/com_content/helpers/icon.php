<?php
/**
 * @version		$Id: icon.php 14401 2010-01-26 14:10:00Z louis $
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

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Content Component HTML Helper
 *
 * @static
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class JHTMLIcon
{
	function create($article, $params, $access, $attribs = array())
	{
		$uri =& JFactory::getURI();
		$ret = $uri->toString();	
	
		$url = 'index.php?task=new&ret='.base64_encode($ret).'&id=0&sectionid='.$article->sectionid;

		if ($params->get('show_icons')) {
			$text = JHTML::_('image.site', 'new.png', '/media/system/images/', NULL, NULL, JText::_('New') );
		} else {
			$text = JText::_('New').'&nbsp;';
		}

		$attribs	= array( 'title' => JText::_( 'New' ));
		return JHTML::_('link', JRoute::_($url), $text, $attribs);
	}
	
    function pdf($article, $params, $access, $attribs = array())
	{
		return '';
	}

	function email($article, $params, $access, $attribs = array())
	{
		return '';
	}

	function edit($article, $params, $access, $attribs = array())
	{
		$user =& JFactory::getUser();
		$uri =& JFactory::getURI();
		$ret = $uri->toString();

		if ($params->get('popup')) {
			return;
		}

		if ($article->state < 0) {
			return;
		}

		if (!$access->canEdit && !($access->canEditOwn && $article->created_by == $user->get('id'))) {
			return;
		}

		JHTML::_('behavior.tooltip');

		$url = 'index.php?view=article&id='.$article->slug.'&task=edit&ret='.base64_encode($ret);
		$icon = $article->state ? 'edit.png' : 'edit_unpublished.png';
		$text = JHTML::_('image.site', $icon, '/media/system/images/', NULL, NULL, JText::_('Edit'));

		if ($article->state == 0) {
			$overlib = JText::_('Unpublished');
		} else {
			$overlib = JText::_('Published');
		}
		$date = JHTML::_('date', $article->created);
		$author = $article->created_by_alias ? $article->created_by_alias : $article->author;

		$overlib .= '&lt;br /&gt;';
		$overlib .= JText::_($article->groups);
		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= htmlspecialchars($author, ENT_COMPAT, 'UTF-8');

		$button = JHTML::_('link', JRoute::_($url), $text);

		$output = '<span class="hasTip" title="'.JText::_( 'Edit Item' ).' :: '.$overlib.'">'.$button.'</span>';
		return $output;
	}


	function print_popup($article, $params, $access, $attribs = array())
	{
		$url  = 'index.php?view=article';
		$url .=  @$article->catslug ? '&catid='.$article->catslug : '';
		$url .= '&id='.$article->slug.'&tmpl=component&print=1&layout=default&page='.@ $request->limitstart;

		$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

		// checks template image directory for image, if non found default are loaded
		if ( $params->get( 'show_icons' ) ) {
			$text = JHTML::_('image.site',  'printButton.png', '/media/system/images/', NULL, NULL, JText::_( 'Print' ) );
		} else {
			$text = JText::_( 'ICON_SEP' ) .'&nbsp;'. JText::_( 'Print' ) .'&nbsp;'. JText::_( 'ICON_SEP' );
		}

		$attribs['title']	= JText::_( 'Print' );
		$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
		$attribs['rel']     = 'nofollow';

		return JHTML::_('link', JRoute::_($url), $text, $attribs);
	}

	function print_screen($article, $params, $access, $attribs = array())
	{
		// checks template image directory for image, if non found default are loaded
		if ( $params->get( 'show_icons' ) ) {
			$text = JHTML::_('image.site',  'printButton.png', '/media/system/images/', NULL, NULL, JText::_( 'Print' ) );
		} else {
			$text = JText::_( 'ICON_SEP' ) .'&nbsp;'. JText::_( 'Print' ) .'&nbsp;'. JText::_( 'ICON_SEP' );
		}
		return '<a href="#" onclick="window.print();return false;">'.$text.'</a>';
	}

}
