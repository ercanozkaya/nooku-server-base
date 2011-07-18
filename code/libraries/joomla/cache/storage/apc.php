<?php
/**
 * @version		$Id: apc.php 2057 2011-06-27 20:09:16Z johanjanssens $
 * @package		Joomla.Framework
 * @subpackage	Cache
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
 * APC cache storage handler
 *
 * @package		Joomla.Framework
 * @subpackage	Cache
 * @since		1.5
 */
class JCacheStorageApc extends JCacheStorage
{

	/**
	 * Get cached data from APC by id and group
	 *
	 * @access	public
	 * @param	string	$id			The cache data id
	 * @param	string	$group		The cache data group
	 * @param	boolean	$checkTime	True to verify cache time expiration threshold
	 * @return	mixed	Boolean false on failure or a cached data string
	 * @since	1.5
	 */
	function get($id, $group, $checkTime)
	{
		$cache_id = $this->_getCacheId($id, $group);
		$this->_setExpire($cache_id);
		return apc_fetch($cache_id);
	}
	
	/**
	 * Get all cached data
	 *
	 * @return	array data
	 * @since	Nooku Server 0.7
	 */
	public function keys()
	{
		$allinfo 	= apc_cache_info('user');
		$keys 		= $allinfo['cache_list'];
		$secret 	= $this->_hash;

		$result = array();

		foreach ($keys as $key) 
		{
			$name  = $key['info'];
			$parts = explode('-', $name);

			if ($parts !== false && $parts[0] == $secret &&  $parts[1] == 'cache') 
			{
			    $data = array();
				$data['name']  = $key['info'];
				$data['hash']  = $parts[4];
				$data['group'] = $parts[3];
				$data['site']  = $parts[2];
				$data['size'] = $key['mem_size'];
				$data['hits'] = $key['num_hits'];
			    $data['created_on']  = $key['creation_time'];
			    $data['modified_on'] = $key['mtime'];
			    $data['accessed_on'] = $key['access_time'];
			    
				$result[$data['hash']] = (object) $data;
			}
		}

		return $result;
	}

	/**
	 * Store the data to APC by id and group
	 *
	 * @access	public
	 * @param	string	$id		The cache data id
	 * @param	string	$group	The cache data group
	 * @param	string	$data	The data to store in cache
	 * @return	boolean	True on success, false otherwise
	 * @since	1.5
	 */
	function store($id, $group, $data)
	{
		$cache_id = $this->_getCacheId($id, $group);
		apc_store($cache_id.'_expire', time());
		return apc_store($cache_id, $data, $this->_lifetime);
	}

	/**
	 * Remove a cached data entry by id and group
	 *
	 * @access	public
	 * @param	string	$id		The cache data id
	 * @param	string	$group	The cache data group
	 * @return	boolean	True on success, false otherwise
	 * @since	1.5
	 */
	function remove($id, $group)
	{
		$cache_id = $this->_getCacheId($id, $group);
		apc_delete($cache_id.'_expire');
		return apc_delete($cache_id);
	}
	
	/**
	 * Delete a cached data entry by key
	 *
	 * @access	public
	 * @param	string	$key
	 * @return	boolean	True on success, false otherwise
	 * @since	Nooku Server 0.7
	 */
	function delete($key)
	{
		apc_delete($key.'_expire');
		return apc_delete($key);
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
	function clean($group, $mode)
	{
		$allinfo 	= apc_cache_info('user');
		$keys 		= $allinfo['cache_list'];
		$secret 	= $this->_hash;

		foreach ($keys as $key) 
		{
			if (strpos($key['info'], $secret.'-cache-'.$this->_site.'-'.$group.'-') === 0 xor $mode != 'group') {
				apc_delete($key['info']);
			}
		}
		
		return true;
	}
	
	/**
	 * Force garbage collect expired cache data as items are removed only on fetch!
	 *
	 * @return boolean  True on success, false otherwise.
	 * @since	Nooku Server 0.7
	 */
	public function gc()
	{
		$lifetime 	= $this->_lifetime;
		$allinfo 	= apc_cache_info('user');
		$keys 		= $allinfo['cache_list'];
		$secret 	= $this->_hash;

		foreach ($keys as $key) 
		{
			if (strpos($key['info'], $secret.'-cache-')) {
				apc_fetch($key['info']);
			}
		}
	}

	/**
	 * Test to see if the cache storage is available.
	 *
	 * @static
	 * @access public
	 * @return boolean  True on success, false otherwise.
	 */
	function test()
	{
		return extension_loaded('apc');
	}

	/**
	 * Set expire time on each call since memcache sets it on cache creation.
	 *
	 * @access private
	 *
	 * @param string  $key   Cache key to expire.
	 * @param integer $lifetime  Lifetime of the data in seconds.
	 */
	function _setExpire($key)
	{
		$lifetime	= $this->_lifetime;
		$expire		= apc_fetch($key.'_expire');

		// set prune period
		if ($expire + $lifetime < time()) {
			apc_delete($key);
			apc_delete($key.'_expire');
		} else {
			apc_store($key.'_expire',  time());
		}
	}
}