<?php
/**
 * @version		$Id: registry.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla.Framework
 * @subpackage	Registry
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

//Register the session storage class with the loader
JLoader::register('JRegistryFormat', dirname(__FILE__).DS.'format.php');

/**
 * JRegistry class
 *
 * @package 	Joomla.Framework
 * @subpackage	Registry
 * @since 		1.5
 */
class JRegistry extends JObject
{
	/**
	 * Default NameSpace
	 * @var string
	 */
	var $_defaultNameSpace = null;

	/**
	 * Registry Object
	 *  - actually an array of namespace objects
	 * @var array
	 */
	var $_registry = array ();

	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	string	$namespace	Default registry namespace
	 * @return	void
	 * @since	1.5
	 */
	function __construct($namespace = 'default')
	{
		$this->_defaultNameSpace = $namespace;
		$this->makeNameSpace($namespace);
	}

	/**
	 * Returns a reference to a global JRegistry object, only creating it
	 * if it doesn't already exist.
	 *
	 * This method must be invoked as:
	 * 		<pre>$registry =& JRegistry::getInstance($id[, $namespace]);</pre>
	 *
	 * @static
	 * @param	string	$id			An ID for the registry instance
	 * @param	string	$namespace	The default namespace for the registry object [optional]
	 * @return	object	The JRegistry object.
	 * @since	1.5
	 */
	function &getInstance($id, $namespace = 'default')
	{
		static $instances;

		if (!isset ($instances)) {
			$instances = array ();
		}

		if (empty ($instances[$id])) {
			$instances[$id] = new JRegistry($namespace);
		}

		return $instances[$id];
	}

	/**
	 * Create a namespace
	 *
	 * @access	public
	 * @param	string	$namespace	Name of the namespace to create
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function makeNameSpace($namespace)
	{
		$this->_registry[$namespace] = array('data' => new stdClass());
		return true;
	}

	/**
	 * Get the list of namespaces
	 *
	 * @access	public
	 * @return	array	List of namespaces
	 * @since	1.5
	 */
	function getNameSpaces()
	{
		return array_keys($this->_registry);
	}

	/**
	 * Get a registry value
	 *
	 * @access	public
	 * @param	string	$regpath	Registry path (e.g. joomla.content.showauthor)
	 * @param	mixed	$default	Optional default value
	 * @return	mixed	Value of entry or null
	 * @since	1.5
	 */
	function getValue($regpath, $default=null)
	{
		$result = $default;

		// Explode the registry path into an array
		if ($nodes = explode('.', $regpath))
		{
			// Get the namespace
			//$namespace = array_shift($nodes);
			$count = count($nodes);
			if ($count < 2) {
				$namespace	= $this->_defaultNameSpace;
				$nodes[1]	= $nodes[0];
			} else {
				$namespace = $nodes[0];
			}

			if (isset($this->_registry[$namespace])) {
				$ns = & $this->_registry[$namespace]['data'];
				$pathNodes = $count - 1;

				//for ($i = 0; $i < $pathNodes; $i ++) {
				for ($i = 1; $i < $pathNodes; $i ++) {
					if((isset($ns->$nodes[$i]))) $ns =& $ns->$nodes[$i];
				}

				if(isset($ns->$nodes[$i])) {
					$result = $ns->$nodes[$i];
				}
			}
		}
		return $result;
	}

	/**
	 * Set a registry value
	 *
	 * @access	public
	 * @param	string	$regpath 	Registry Path (e.g. joomla.content.showauthor)
	 * @param 	mixed	$value		Value of entry
	 * @return 	mixed	Value of old value or boolean false if operation failed
	 * @since	1.5
	 */
	function setValue($regpath, $value)
	{
		// Explode the registry path into an array
		$nodes = explode('.', $regpath);

		// Get the namespace
		$count = count($nodes);

		if ($count < 2) {
			$namespace = $this->_defaultNameSpace;
		} else {
			$namespace = array_shift($nodes);
			$count--;
		}

		if (!isset($this->_registry[$namespace])) {
			$this->makeNameSpace($namespace);
		}

		$ns = & $this->_registry[$namespace]['data'];

		$pathNodes = $count - 1;

		if ($pathNodes < 0) {
			$pathNodes = 0;
		}

		for ($i = 0; $i < $pathNodes; $i ++)
		{
			// If any node along the registry path does not exist, create it
			if (!isset($ns->$nodes[$i])) {
				$ns->$nodes[$i] = new stdClass();
			}
			$ns =& $ns->$nodes[$i];
		}

		// Get the old value if exists so we can return it
		$ns->$nodes[$i] =& $value;

		return $ns->$nodes[$i];
	}

	/**
	 * Load a associative array of values into the default namespace
	 *
	 * @access	public
	 * @param	array	$array		Associative array of value to load
	 * @param	string	$namepsace 	The name of the namespace
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function loadArray($array, $namespace = null)
	{
		// If namespace is not set, get the default namespace
		if ($namespace == null) {
			$namespace = $this->_defaultNameSpace;
		}

		if (!isset($this->_registry[$namespace])) {
			// If namespace does not exist, make it and load the data
			$this->makeNameSpace($namespace);
		}

		// Load the variables into the registry's default namespace.
		foreach ($array as $k => $v)
		{
			$this->_registry[$namespace]['data']->$k = $v;
		}

		return true;
	}

	/**
	 * Load the public variables of the object into the default namespace.
	 *
	 * @access	public
	 * @param	object	$object		The object holding the public vars to load
	 * @param	string	$namespace 	Namespace to load the INI string into [optional]
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function loadObject(&$object, $namespace = null)
	{
		// If namespace is not set, get the default namespace
		if ($namespace == null) {
			$namespace = $this->_defaultNameSpace;
		}

		if (!isset($this->_registry[$namespace])) {
			// If namespace does not exist, make it and load the data
			$this->makeNameSpace($namespace);
		}

		/*
		 * We want to leave groups that are already in the namespace and add the
		 * groups loaded into the namespace.  This overwrites any existing group
		 * with the same name
		 */
		if (is_object( $object ))
		{
			foreach (get_object_vars($object) as $k => $v) {
				if (substr($k, 0,1) != '_' || $k == '_name') {
					$this->_registry[$namespace]['data']->$k = $v;
				}
			}
		}

		return true;
	}

	/**
	 * Load the contents of a file into the registry
	 *
	 * @access	public
	 * @param	string	$file		Path to file to load
	 * @param	string	$format		Format of the file [optional: defaults to INI]
	 * @param	string	$namespace	Namespace to load the INI string into [optional]
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function loadFile($file, $format = 'INI', $namespace = null)
	{
		// Load a file into the given namespace [or default namespace if not given]
		$handler =& JRegistryFormat::getInstance($format);

		// If namespace is not set, get the default namespace
		if ($namespace == null) {
			$namespace = $this->_defaultNameSpace;
		}

		// Get the contents of the file
		jimport('joomla.filesystem.file');
		$data = JFile::read($file);

		if (!isset($this->_registry[$namespace]))
		{
			// If namespace does not exist, make it and load the data
			$this->makeNameSpace($namespace);
			$this->_registry[$namespace]['data'] = $handler->stringToObject($data);
		}
		else
		{
			// Get the data in object format
			$ns = $handler->stringToObject($data);

			/*
			 * We want to leave groups that are already in the namespace and add the
			 * groups loaded into the namespace.  This overwrites any existing group
			 * with the same name
			 */
			foreach (get_object_vars($ns) as $k => $v) {
				$this->_registry[$namespace]['data']->$k = $v;
			}
		}

		return true;
	}

	/**
	 * Load an XML string into the registry into the given namespace [or default if a namespace is not given]
	 *
	 * @access	public
	 * @param	string	$data		XML formatted string to load into the registry
	 * @param	string	$namespace	Namespace to load the XML string into [optional]
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function loadXML($data, $namespace = null)
	{
		// Load a string into the given namespace [or default namespace if not given]
		$handler =& JRegistryFormat::getInstance('XML');

		// If namespace is not set, get the default namespace
		if ($namespace == null) {
			$namespace = $this->_defaultNameSpace;
		}

		if (!isset($this->_registry[$namespace])) {
			// If namespace does not exist, make it and load the data
			$this->makeNameSpace($namespace);
			$this->_registry[$namespace]['data'] =& $handler->stringToObject($data);
		} else {
			// Get the data in object format
			$ns =& $handler->stringToObject($data);

			/*
			 * We want to leave groups that are already in the namespace and add the
			 * groups loaded into the namespace.  This overwrites any existing group
			 * with the same name
			 */
			foreach (get_object_vars($ns) as $k => $v) {
				$this->_registry[$namespace]['data']->$k = $v;
			}
		}

		return true;
	}

	/**
	 * Load an INI string into the registry into the given namespace [or default if a namespace is not given]
	 *
	 * @access	public
	 * @param	string	$data		INI formatted string to load into the registry
	 * @param	string	$namespace	Namespace to load the INI string into [optional]
	 * @return	boolean True on success
	 * @since	1.5
	 */
	function loadINI($data, $namespace = null)
	{
		// Load a string into the given namespace [or default namespace if not given]
		$handler =& JRegistryFormat::getInstance('INI');

		// If namespace is not set, get the default namespace
		if ($namespace == null) {
			$namespace = $this->_defaultNameSpace;
		}

		if (!isset($this->_registry[$namespace])) {
			// If namespace does not exist, make it and load the data
			$this->makeNameSpace($namespace);
			$this->_registry[$namespace]['data'] =& $handler->stringToObject($data);
		} else {
			// Get the data in object format
			$ns = $handler->stringToObject($data);

			/*
			 * We want to leave groups that are already in the namespace and add the
			 * groups loaded into the namespace.  This overwrites any existing group
			 * with the same name
			 */
			foreach (get_object_vars($ns) as $k => $v) {
				$this->_registry[$namespace]['data']->$k = $v;
			}
		}

		return true;
	}

	/**
	 * Merge a JRegistry object into this one
	 *
	 * @access	public
	 * @param	object	$source	Source JRegistry object ot merge
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function merge(&$source)
	{
		if (is_a($source, 'JRegistry'))
		{
			$sns = $source->getNameSpaces();
			foreach ($sns as $ns)
			{
				if (!isset($this->_registry[$ns]))
				{
					// If namespace does not exist, make it and load the data
					$this->makeNameSpace($ns);
				}

				// Load the variables into the registry's default namespace.
				foreach ($source->toArray($ns) as $k => $v)
				{
					if ($v != null) {
						$this->_registry[$ns]['data']->$k = $v;
					}
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * Get a namespace in a given string format
	 *
	 * @access	public
	 * @param	string	$format		Format to return the string in
	 * @param	string	$namespace	Namespace to return [optional: null returns the default namespace]
	 * @param	mixed	$params		Parameters used by the formatter, see formatters for more info
	 * @return	string	Namespace in string format
	 * @since	1.5
	 */
	function toString($format = 'INI', $namespace = null, $params = null)
	{
		// Return a namespace in a given format
		$handler =& JRegistryFormat::getInstance($format);

		// If namespace is not set, get the default namespace
		if ($namespace == null) {
			$namespace = $this->_defaultNameSpace;
		}

		// Get the namespace
		$ns = & $this->_registry[$namespace]['data'];

		return $handler->objectToString($ns, $params);
	}

	/**
	 * Transforms a namespace to an array
	 *
	 * @access	public
	 * @param	string	$namespace	Namespace to return [optional: null returns the default namespace]
	 * @return	array	An associative array holding the namespace data
	 * @since	1.5
	 */
	function toArray($namespace = null)
	{
		// If namespace is not set, get the default namespace
		if ($namespace == null) {
			$namespace = $this->_defaultNameSpace;
		}

		// Get the namespace
		$ns = & $this->_registry[$namespace]['data'];

		$array = array();
		foreach (get_object_vars( $ns ) as $k => $v) {
			$array[$k] = $v;
		}

		return $array;
	}

	/**
	 * Transforms a namespace to an object
	 *
	 * @access	public
	 * @param	string	$namespace	Namespace to return [optional: null returns the default namespace]
	 * @return	object	An an object holding the namespace data
	 * @since	1.5
	 */
	function toObject($namespace = null)
	{
		// If namespace is not set, get the default namespace
		if ($namespace == null) {
			$namespace = $this->_defaultNameSpace;
		}

		// Get the namespace
		$ns = & $this->_registry[$namespace]['data'];

		return $ns;
	}

	function __clone()
	{
		$this->_registry = unserialize(serialize($this->_registry));
	}
}