<?php
/**
* @version		$Id: router.php 8180 2007-07-23 05:52:29Z eddieajau $
* @package		Joomla.Framework
* @subpackage	Application
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Class to create and parse routes for the site application
 *
 * @package 	Joomla
 * @since		1.5
 */
class JRouterSite extends JRouter
{
	/**
	 * Class constructor
	 *
	 * @access public
	 */
	function __construct($options = array()) {
		parent::__construct($options);
	}

	function parse(&$uri)
	{
		$vars = array();

		// Get the application
		$app =& JFactory::getApplication();

		// Forward to https
		if($app->getCfg('force_ssl') == 2 && strtolower($uri->getScheme()) != 'https') 
		{
			$uri->setScheme('https');
			$app->redirect($uri->toString());
		}


		// Get the path
		$path = $uri->getPath();

		//Remove the suffix
		if($this->_mode == JROUTER_MODE_SEF)
		{

			if($app->getCfg('sef_suffix') && !(substr($path, -9) == 'index.php' || substr($path, -1) == '/'))
			{
				if($suffix = pathinfo($path, PATHINFO_EXTENSION))
				{
					$path = str_replace('.'.$suffix, '', $path);
					$vars['format'] = $suffix;
				}
			}
		}

		//Remove basepath
		$path = substr_replace($path, '', 0, strlen(JURI::base(true)));

		//Remove prefix
		$path = str_replace('index.php', '', $path);

		//Set the route
		$uri->setPath(trim($path , '/'));

		$vars += parent::parse($uri);

		return $vars;
	}

	function &build($url)
	{
		$uri =& parent::build($url);

		// Get the path data
		$route = $uri->getPath();

		//Add the suffix to the uri
		if($this->_mode == JROUTER_MODE_SEF && $route)
		{
			$app =& JFactory::getApplication();

			if($app->getCfg('sef_suffix') && !(substr($route, -9) == 'index.php' || substr($route, -1) == '/'))
			{
				if($format = $uri->getVar('format', 'html'))
				{
					$route .= '.'.$format;
					$uri->delVar('format');
				}
			}

			if($app->getCfg('sef_rewrite'))
			{
				//Transform the route
				$route = str_replace('index.php/', '', $route);
			}
		}

		//Add basepath to the uri
		$uri->setPath(JURI::base(true).'/'.$route);

		return $uri;
	}

	function _parseRawRoute(&$uri)
	{
		$vars   = array();

		$menu =& JSite::getMenu(true);

		//Handle an empty URL (special case)
		if(!$uri->getVar('Itemid') && !$uri->getVar('option'))
		{
			$item = $menu->getDefault();
			if(!is_object($item)) return $vars; // No default item set

			//Set the information in the request
			$vars = $item->query;

			//Get the itemid
			$vars['Itemid'] = $item->id;

			// Set the active menu item
			$menu->setActive($vars['Itemid']);

			return $vars;
		}

		//Get the variables from the uri
		$this->setVars($uri->getQuery(true));

		//Get the itemid, if it hasn't been set force it to null
		$this->setVar('Itemid', JRequest::getInt('Itemid', null));

		//Only an Itemid ? Get the full information from the itemid
		if(count($this->getVars()) == 1)
		{
			$item = $menu->getItem($this->getVar('Itemid'));
			if($item !== NULL && is_array($item->query)) {
				$vars = $vars + $item->query;
			}
		}

		// Set the active menu item
		$menu->setActive($this->getVar('Itemid'));

		return $vars;
	}

	function _parseSefRoute(&$uri)
	{
		$vars   = array();

		$menu  =& JSite::getMenu(true);
		$route = $uri->getPath();

		//Get the variables from the uri
		$vars = $uri->getQuery(true);

		//Remove the site from the route
		$site  = JFactory::getApplication()->getSite();
		$route = ltrim(str_replace($site, '', $route), '/');

		/*
		 * Parse the application route
		 */
		if(substr($route, 0, 9) == 'component')
		{
			$segments	= explode('/', $route);
			$route      = str_replace('component/'.$segments[1], '', $route);

			$vars['option'] = 'com_'.$segments[1];
			$vars['Itemid'] = null;
		}
		else
		{
			//Need to reverse the array (highest sublevels first)
			$items = array_reverse($menu->getMenu());

			foreach ($items as $item)
			{
				$lenght = strlen($item->route); //get the lenght of the route

				if($lenght > 0 && strpos($route.'/', $item->route.'/') === 0 && $item->type != 'menulink')
				{
					$route   = substr($route, $lenght);

					$vars['Itemid'] = $item->id;
					$vars['option'] = $item->component;
					
					break;
				}
			}
		}

		// Set the active menu item
		if ( isset($vars['Itemid']) ) {
			$menu->setActive(  $vars['Itemid'] );
		}
		
		// FIXME: remove this after com_content is fully gone
		if ($vars['option'] == 'com_articles') {
			$vars['option'] = 'com_content';
		}
		
		//Set the variables
		$this->setVars($vars);

		/*
		 * Parse the component route
		 */
		if(!empty($route) && isset($this->_vars['option']) )
		{
			$segments = explode('/', $route);
			array_shift($segments);

			// Handle component	route
			$component = preg_replace('/[^A-Z0-9_\.-]/i', '', $this->_vars['option']);

			// Use the component routing handler if it exists
			$path = JPATH_SITE.DS.'components'.DS.$component.DS.'router.php';

			if (file_exists($path) && count($segments))
			{
				if ($component != "com_search") { // Cheap fix on searches
					//decode the route segments
					$segments = $this->_decodeSegments($segments);
				}
				else { // fix up search for URL
					$total = count($segments);
					for($i=0; $i<$total; $i++) {
						// urldecode twice because it is encoded twice
						$segments[$i] = urldecode(urldecode(stripcslashes($segments[$i])));
					}
				}

				require_once $path;
				$function =  substr($component, 4).'ParseRoute';
				$vars =  $function($segments);

				$this->setVars($vars);
			}
		}
		else
		{
			//Set active menu item
			if($item =& $menu->getActive()) {
				$vars = $item->query;
			}
		}

		return $vars;
	}

	function _buildRawRoute(&$uri)
	{
	    $site = JFactory::getApplication()->getSite();
	    if($site != 'default') {
	        $uri->setVar('site', $site);
	}
	}

	function _buildSefRoute(&$uri)
	{
		// Get the route
		$route = $uri->getPath();

		// Get the query data
		$query = $uri->getQuery(true);

		if(!isset($query['option'])) {
			return;
		}

		$menu =& JSite::getMenu();

		/*
		 * Build the component route
		 */
		$component	= preg_replace('/[^A-Z0-9_\.-]/i', '', $query['option']);
		$tmp 		= '';

		// Use the component routing handler if it exists
		$path = JPATH_SITE.DS.'components'.DS.$component.DS.'router.php';

		// Use the custom routing handler if it exists
		if (file_exists($path) && !empty($query))
		{
			require_once $path;
			$function	= substr($component, 4).'BuildRoute';
			$parts		= $function($query);

			// encode the route segments
			if ($component != "com_search") { // Cheep fix on searches
				$parts = $this->_encodeSegments($parts);
			}
			else 
			{ 
			    // fix up search for URL
				$total = count($parts);
				for($i=0; $i<$total; $i++) 
				{
					// urlencode twice because it is decoded once after redirect
					$parts[$i] = urlencode(urlencode(stripcslashes($parts[$i])));
				}
			}

			$result = implode('/', $parts);
			$tmp	= ($result != "") ? '/'.$result : '';
		}

		/*
		 * Build the application route
		 */
		$built = false;
		if (isset($query['Itemid']) && !empty($query['Itemid']))
		{
			$item = $menu->getItem($query['Itemid']);

			if (is_object($item)) {
				// FIXME: remove this after com_content is fully gone
				if (($query['option'] === 'com_content' && $item->component === 'com_articles') || $query['option'] == $item->component) {
					$tmp = !empty($tmp) ? $item->route.'/'.$tmp : $item->route;
					$built = true;
				}
			}
		}

		if(!$built) {
			$tmp = 'component/'.substr($query['option'], 4).'/'.$tmp;
		}

		//Add the site
	    $site = JFactory::getApplication()->getSite();
	    if($site != 'default') {
	        $tmp = $site.'/'.$tmp;
	    }
		
		$route .= '/'.$tmp;

		// Unset unneeded query information
		unset($query['Itemid']);
		unset($query['option']);

		//Set query again in the URI
		$uri->setQuery($query);
		$uri->setPath($route);
	}

	function _processParseRules(&$uri)
	{
		// Process the attached parse rules
		$vars = parent::_processParseRules($uri);

		// Process the pagination support
		if($this->_mode == JROUTER_MODE_SEF)
		{
			$app =& JFactory::getApplication();

			if($start = $uri->getVar('start'))
			{
				$uri->delVar('start');
				$vars['limitstart'] = $start;
			}
		}

		return $vars;
	}

	function _processBuildRules(&$uri)
	{
		// Make sure any menu vars are used if no others are specified
		if(($this->_mode != JROUTER_MODE_SEF) && $uri->getVar('Itemid') && count($uri->getQuery(true)) == 2)
		{
			$menu =& JSite::getMenu();

			// Get the active menu item
			$itemid = $uri->getVar('Itemid');
			$item   = $menu->getItem($itemid);

			$uri->setQuery($item->query);
			$uri->setVar('Itemid', $itemid);
		}

		// Process the attached build rules
		parent::_processBuildRules($uri);

		// Get the path data
		$route = $uri->getPath();

		if($this->_mode == JROUTER_MODE_SEF && $route)
		{
			$app =& JFactory::getApplication();

			if ($limitstart = $uri->getVar('limitstart'))
			{
				$uri->setVar('start', (int) $limitstart);
				$uri->delVar('limitstart');
			}
		}

		$uri->setPath($route);
	}

	function &_createURI($url)
	{
		//Create the URI
		$uri =& parent::_createURI($url);

		// Set URI defaults
		$menu =& JSite::getMenu();

		// Get the itemid form the URI
		$itemid = $uri->getVar('Itemid');

		if(is_null($itemid))
		{
			if($option = $uri->getVar('option'))
			{
				$item  = $menu->getItem($this->getVar('Itemid'));
				if(isset($item) && $item->component == $option) {
					$uri->setVar('Itemid', $item->id);
				}
			}
			else
			{
				if($option = $this->getVar('option')) {
					$uri->setVar('option', $option);
				}

				if($itemid = $this->getVar('Itemid')) {
					$uri->setVar('Itemid', $itemid);
				}
			}
		}
		else
		{
			if(!$uri->getVar('option'))
			{
				$item  = $menu->getItem($itemid);
				$uri->setVar('option', $item->component);
			}
		}

		return $uri;
	}
}
