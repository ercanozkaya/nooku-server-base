<?php
/**
 * @version		$Id: cache.php 837 2011-04-06 00:58:44Z johanjanssens $
 * @package		Joomla.Framework
 * @subpackage	Cache
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

//Register the session storage class with the loader
JLoader::register('JCacheStorage', dirname(__FILE__).DS.'storage.php');

/**
 * Joomla! Cache base object
 *
 * @abstract
 * @package		Joomla.Framework
 * @subpackage	Cache
 * @since		1.5
 */
class JCache extends JObject
{
	/**
	 * Storage Handler
	 * @access	private
	 * @var		object
	 */
	var $_handler;

	/**
	 * Cache Options
	 * @access	private
	 * @var		array
	 */
	var $_options;

	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	array	$options	options
	 */
	function __construct($options)
	{
		$this->_options =& $options;

		// Get the default group and caching
    	if(isset($options['site'])) {
			$this->_options['site'] = $options['site'];
		} else {
			$options['site'] = 'default';
		}
		
		if(isset($options['language'])) {
			$this->_options['language'] = $options['language'];
		} else {
			$options['language'] = 'en-GB';
		}

		if(isset($options['cachebase'])) {
			$this->_options['cachebase'] = $options['cachebase'];
		} else {
			$this->_options['cachebase'] = JPATH_CACHE;
		}

		if(isset($options['defaultgroup'])) {
			$this->_options['defaultgroup'] = $options['defaultgroup'];
		} else {
			$this->_options['defaultgroup'] = 'default';
		}

		if(isset($options['caching'])) {
			$this->_options['caching'] =  $options['caching'];
		} else {
			$this->_options['caching'] = true;
		}

		if( isset($options['storage'])) {
			$this->_options['storage'] = $options['storage'];
		} else {
			$this->_options['storage'] = 'file';
		}

		//Fix to detect if template positions are enabled...
		if(JRequest::getCMD('tpl',0)) {
			$this->_options['caching'] = false;
		}
	}

	/**
	 * Returns a reference to a cache adapter object, always creating it
	 *
	 * @static
	 * @param	string	$type	The cache object type to instantiate
	 * @return	object	A JCache object
	 * @since	1.5
	 */
	function &getInstance($type = 'output', $options = array())
	{
		$type = strtolower(preg_replace('/[^A-Z0-9_\.-]/i', '', $type));

		$class = 'JCache'.ucfirst($type);

		if(!class_exists($class))
		{
			$path = dirname(__FILE__).DS.'handler'.DS.$type.'.php';

			if (file_exists($path)) {
				require_once($path);
			} else {
				JError::raiseError(500, 'Unable to load Cache Handler: '.$type);
			}
		}

		$instance = new $class($options);

		return $instance;
	}

	/**
	 * Get the storage handlers
	 *
	 * @access public
	 * @return array An array of available storage handlers
	 */
	function getStores()
	{
		jimport('joomla.filesystem.folder');
		$handlers = JFolder::files(dirname(__FILE__).DS.'storage', '.php$');

		$names = array();
		foreach($handlers as $handler)
		{
			$name = substr($handler, 0, strrpos($handler, '.'));
			$class = 'JCacheStorage'.$name;

			if(!class_exists($class)) {
				require_once(dirname(__FILE__).DS.'storage'.DS.$name.'.php');
			}

			if(call_user_func_array( array( trim($class), 'test' ), array())) {
				$names[] = $name;
			}
		}

		return $names;
	}

	/**
	 * Set caching enabled state
	 *
	 * @access	public
	 * @param	boolean	$enabled	True to enable caching
	 * @return	void
	 * @since	1.5
	 */
	function setCaching($enabled)
	{
		$this->_options['caching'] = $enabled;
	}

	/**
	 * Set cache lifetime
	 *
	 * @access	public
	 * @param	int	$lt	Cache lifetime
	 * @return	void
	 * @since	1.5
	 */
	function setLifeTime($lt)
	{
		$this->_options['lifetime'] = $lt;
	}

	/**
	 * Set cache validation
	 *
	 * @access	public
	 * @return	void
	 * @since	1.5
	 */
	function setCacheValidation()
	{
		// Deprecated
	}

	/**
	 * Get cached data by id and group
	 *
	 * @abstract
	 * @access	public
	 * @param	string	$id		The cache data id
	 * @param	string	$group	The cache data group
	 * @return	mixed	Boolean false on failure or a cached data string
	 * @since	1.5
	 */
	function get($id, $group=null)
	{
		// Get the default group
		$group = ($group) ? $group : $this->_options['defaultgroup'];

		// Get the storage handler
		$handler =& $this->_getStorage();
		if (!JError::isError($handler) && $this->_options['caching']) {
			return $handler->get($id, $group, (isset($this->_options['checkTime']))? $this->_options['checkTime'] : true);
		}
		
		return false;
	}
	
	/**
	 * Get a list of all cached data
	 *
	 * @return	mixed	Boolean false on failure or an object with a list of cache groups and data
	 * @since	1.6
	 */
	public function keys()
	{
	    // Get the storage
		$handler = $this->_getStorage();
		if (!JError::isError($handler)) {
			return $handler->keys();
		}
		
		return false;
	}

	/**
	 * Store the cached data by id and group
	 *
	 * @access	public
	 * @param	string	$id		The cache data id
	 * @param	string	$group	The cache data group
	 * @param	mixed	$data	The data to store
	 * @return	boolean	True if cache stored
	 * @since	1.5
	 */
	function store($data, $id, $group=null)
	{
		// Get the default group
		$group = ($group) ? $group : $this->_options['defaultgroup'];

		// Get the storage handler and store the cached data
		$handler =& $this->_getStorage();
		if (!JError::isError($handler) && $this->_options['caching']) {
			return $handler->store($id, $group, $data);
		}
		return false;
	}

	/**
	 * Remove a cached data entry by id and group
	 *
	 * @abstract
	 * @access	public
	 * @param	string	$id		The cache data id
	 * @param	string	$group	The cache data group
	 * @return	boolean	True on success, false otherwise
	 * @since	1.5
	 */
	function remove($id, $group=null)
	{
		// Get the default group
		$group = ($group) ? $group : $this->_options['defaultgroup'];

		// Get the storage handler
		$handler =& $this->_getStorage();
		if (!JError::isError($handler)) {
			return $handler->remove($id, $group);
		}
		return false;
	}
	
	/**
	 * Remove a cached data entry by id and group
	 *
	 * @abstract
	 * @access	public
	 * @param	string	$key	The cache data id
	 * @return	boolean	True on success, false otherwise
	 * @since 	Nooku Server 0.7
	 */
	function delete($key)
	{
		// Get the storage handler
		$handler =& $this->_getStorage();
		if (!JError::isError($handler)) {
			return $handler->delete($key);
		}
		
		return false;
	}
	
	/**
	 * Clean cache for a group given a mode.
	 *
	 * group mode		: cleans all cache in the group
	 * notgroup mode	: cleans all cache not in the group
	 *
	 * @access	public
	 * @param	string	$group	The cache data group
	 * @param	string	$mode	The mode for cleaning cache [group|notgroup]
	 * @return	boolean	True on success, false otherwise
	 * @since	1.5
	 */
	function clean($group=null, $mode='group')
	{
		// Get the default group
		$group = ($group) ? $group : $this->_options['defaultgroup'];

		// Get the storage handler
		$handler =& $this->_getStorage();
		if (!JError::isError($handler)) {
			return $handler->clean($group, $mode);
		}
		return false;
	}

	/**
	 * Garbage collect expired cache data
	 *
	 * @access public
	 * @return boolean  True on success, false otherwise.
	 * @since	1.5
	 */
	function gc()
	{
		// Get the storage handler
		$handler =& $this->_getStorage();
		if (!JError::isError($handler)) {
			return $handler->gc();
		}
		return false;
	}

	/**
	 * Get the cache storage handler
	 *
	 * @access protected
	 * @return object A JCacheStorage object
	 * @since	1.5
	 */
	function &_getStorage()
	{
		if (is_a($this->_handler, 'JCacheStorage')) {
			return $this->_handler;
		}

		$this->_handler =& JCacheStorage::getInstance($this->_options['storage'], $this->_options);
		return $this->_handler;
	}
}
