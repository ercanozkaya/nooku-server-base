<?php
/**
* @version		$Id: sef.php 14401 2010-01-26 14:10:00Z louis $
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

jimport( 'joomla.plugin.plugin');

/**
* Joomla! SEF Plugin
*
* @package 		Joomla
* @subpackage	System
*/
class plgSystemSef extends JPlugin
{
	/**
	 * Method to trigger events
	 * 
	 * Only trigger the event if SEF is enabled
	 *
	 * @access public
	 * @param array Arguments
	 * @return mixed Routine return value
	 */
	function update(&$args)
	{
		if(JFactory::getApplication()->getCfg('sef')) {
			parent::update($args);
		}
	}

	/**
     * Converting the site URL to fit to the HTTP request
     */
	function onAfterRender()
	{
		//Replace src links
      	$base   = JURI::base(true).'/';
		$buffer = JResponse::getBody();
		
		// pull out contents of editor to prevent URL changes inside edit area
		$editor =& JFactory::getEditor();
		$regex = '#'.$editor->_tagForSEF['start'].'(.*)'.$editor->_tagForSEF['end'].'#Us';
		preg_match_all($regex, $buffer, $editContents, PREG_PATTERN_ORDER);

		// create an array to hold the placeholder text (in case there are more than one editor areas)
		$placeholders = array();
		for ($i = 0; $i < count($editContents[0]); $i++) {
			$placeholders[] = $editor->_tagForSEF['start'].$i.$editor->_tagForSEF['end'];
		}
		
		// replace editor contents with placeholder text
		$buffer 	= str_replace($editContents[0], $placeholders, $buffer);

		// do the SEF substitutions
       	$regex  = '#(href|src|action|location.href|<option\s+value)(="|=\')(index.php[^"]*)#m';
      	$buffer = preg_replace_callback( $regex, array('plgSystemSEF', 'route'), $buffer );

       	$protocols = '[a-zA-Z0-9]+:'; //To check for all unknown protocals (a protocol must contain at least one alpahnumeric fillowed by :
      	$regex     = '#(src|href)="(?!/|'.$protocols.'|\#|\')([^"]*)"#m';
        $buffer    = preg_replace($regex, "$1=\"$base\$2\"", $buffer);
		$regex     = '#(onclick="window.open\(\')(?!/|'.$protocols.'|\#)([^/]+[^\']*?\')#m';
		$buffer    = preg_replace($regex, '$1'.$base.'$2', $buffer);
		
		// ONMOUSEOVER / ONMOUSEOUT
		$regex 		= '#(onmouseover|onmouseout)="this.src=([\']+)(?!/|'.$protocols.'|\#|\')([^"]+)"#m';
		$buffer 	= preg_replace($regex, '$1="this.src=$2'. $base .'$3$4"', $buffer);
		
		// Background image
		$regex 		= '#style\s*=\s*[\'\"](.*):\s*url\s*\([\'\"]?(?!/|'.$protocols.'|\#)([^\)\'\"]+)[\'\"]?\)#m';
		$buffer 	= preg_replace($regex, 'style="$1: url(\''. $base .'$2$3\')', $buffer);
		
		// OBJECT <param name="xx", value="yy"> -- fix it only inside the <param> tag
		$regex 		= '#(<param\s+)name\s*=\s*"(movie|src|url)"[^>]\s*value\s*=\s*"(?!/|'.$protocols.'|\#|\')([^"]*)"#m';
		$buffer 	= preg_replace($regex, '$1name="$2" value="' . $base . '$3"', $buffer);
		
		// OBJECT <param value="xx", name="yy"> -- fix it only inside the <param> tag
		$regex 		= '#(<param\s+[^>]*)value\s*=\s*"(?!/|'.$protocols.'|\#|\')([^"]*)"\s*name\s*=\s*"(movie|src|url)"#m';
		$buffer 	= preg_replace($regex, '<param value="'. $base .'$2" name="$3"', $buffer);

		// OBJECT data="xx" attribute -- fix it only in the object tag
		$regex = 	'#(<object\s+[^>]*)data\s*=\s*"(?!/|'.$protocols.'|\#|\')([^"]*)"#m';
		$buffer 	= preg_replace($regex, '$1data="' . $base . '$2"$3', $buffer);
		
		// restore the editor contents
		$buffer 	= str_replace($placeholders, $editContents[0], $buffer);
		
		JResponse::setBody($buffer);
		return true;
	}

	/**
     * Replaces the matched tags
     *
     * @param array An array of matches (see preg_match_all)
     * @return string
     */
   	 function route( &$matches )
     {    
         $url    = str_replace('&amp;','&',$matches[3]);
         $route  = JRoute::_($url);
         
         return $matches[1].$matches[2].$route;
      }
}
