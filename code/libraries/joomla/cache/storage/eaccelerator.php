<?php
/**
 * @version		$Id: eaccelerator.php 837 2011-04-06 00:58:44Z johanjanssens $
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
 * eAccelerator cache storage handler
 *
 * @package		Joomla.Framework
 * @subpackage	Cache
 * @since		1.5
 */
class JCacheStorageEaccelerator extends JCacheStorage
{
	/**
	 * Get cached data by id and group
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
		
		$cache_content = eaccelerator_get($cache_id);
		
		if($cache_content === null) {
			return false;
		}
		
		return $cache_content;
	}
	
 	/**
	 * Get all cached data
	 *
	 * @return	array data
	 * @since	Nooku Server 0.7
	 */
	public function keys()
	{
		$keys = eaccelerator_list_keys();

		$secret = $this->_hash;
		$result = array();

		foreach ($keys as $key) 
		{
			/* Trim leading ":" to work around list_keys namespace bug in eAcc. This will still work when bug is fixed */
			$name  = ltrim($key['name'], ':');
			$parts = explode('-',$name);

			if ($parts !== false && $parts[0] == $secret &&  $parts[1]=='cache') 
			{    
				//Set the size
				$data = array();
				$data['name']  = $key['name'];
				$data['hash']  = $parts[4];
				$data['group'] = $parts[3];
				$data['site']  = $parts[2];
				$data['size']  = $key['size'];
				$data['hits']  = '';
			    $data['created_on']  = '';
			    $data['accessed_on'] = '';
			    
				$result[$data['hash']] = (object) $data;
			}
		}
		
		return $result;
	}

	/**
	 * Store the data to by id and group
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
		eaccelerator_put($cache_id.'_expire', time());
		return eaccelerator_put($cache_id, $data, $this->_lifetime);
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
		eaccelerator_rm($cache_id.'_expire');
		return eaccelerator_rm($cache_id);
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
		eaccelerator_rm($key.'_expire');
		return eaccelerator_rm($key);
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
		$keys = eaccelerator_list_keys();

        $secret = $this->_hash;

        if (is_array($keys)) 
        {
        	foreach ($keys as $key) 
        	{
        		/* Trim leading ":" to work around list_keys namespace bug in eAcc. This will still work when bug is fixed */
				$key['name'] = ltrim($key['name'], ':');

        		if (strpos($key['name'],  $secret.'-cache-'.$this->_site.'-'.$group.'-') === 0 xor $mode != 'group') {
					eaccelerator_rm($key['name']);
        		}
        	}
        }
		return true;
	}

	/**
	 * Garbage collect expired cache data
	 *
	 * @access public
	 * @return boolean  True on success, false otherwise.
	 */
	function gc()
	{
		return eaccelerator_gc();
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
		return (extension_loaded('eaccelerator') && function_exists('eaccelerator_get'));
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
		$expire		= eaccelerator_get($key.'_expire');

		// set prune period
		if ($expire + $lifetime < time()) {
			eaccelerator_rm($key);
			eaccelerator_rm($key.'_expire');
		} else {
			eaccelerator_put($key.'_expire',  time());
		}
	}
}