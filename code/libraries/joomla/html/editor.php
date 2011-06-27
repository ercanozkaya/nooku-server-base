<?php
/**
 * @version		$Id: editor.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla.Framework
 * @subpackage	HTML
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

jimport('joomla.event.dispatcher');

/**
 * JEditor class to handle WYSIWYG editors
 *
 * @package		Joomla.Framework
 * @subpackage	HTML
 * @since		1.5
 */
class JEditor extends JObservable
{
	/**
	 * Editor Plugin object
	 *
	 * @var	object
	 */
	var $_editor = null;

	/**
	 * Editor Plugin name
	 *
	 * @var string
	 */
	var $_name = null;
	
	/**
	 * Editor start and end tag
	 * Used to tell SEF plugin not to process editor contents
	 * @var array
	 */
	var $_tagForSEF = array('start' => '<!-- Start Editor -->', 'end' => '<!-- End Editor -->'); 

	/**
	 * constructor
	 *
	 * @access	protected
	 * @param	string	The editor name
	 */
	function __construct($editor = 'none')
	{
		$this->_name = $editor;
	}

	/**
	 * Returns a reference to a global Editor object, only creating it
	 * if it doesn't already exist.
	 *
	 * This method must be invoked as:
	 * 		<pre>  $editor = &JEditor::getInstance([$editor);</pre>
	 *
	 * @access	public
	 * @param	string	$editor  The editor to use.
	 * @return	JEditor	The Editor object.
	 */
	function &getInstance($editor = 'none')
	{
		static $instances;

		if (!isset ($instances)) {
			$instances = array ();
		}

		$signature = serialize($editor);

		if (empty ($instances[$signature])) {
			$instances[$signature] = new JEditor($editor);
		}

		return $instances[$signature];
	}

	/**
	 * Initialize the editor
	 */
	function initialise()
	{
		//check if editor is already loaded
		if(is_null(($this->_editor))) {
			return;
		}

		$args['event'] = 'onInit';

		$return = '';
		$results[] = $this->_editor->update($args);
		foreach ($results as $result) {
			if (trim($result)) {
				//$return .= $result;
				$return = $result;
			}
		}

		$document =& JFactory::getDocument();
		$document->addCustomTag($return);
	}

	/**
	 * Present a text area
	 *
	 * @param	string	The control name
	 * @param	string	The contents of the text area
	 * @param	string	The width of the text area (px or %)
	 * @param	string	The height of the text area (px or %)
	 * @param	int		The number of columns for the textarea
	 * @param	int		The number of rows for the textarea
	 * @param	boolean	True and the editor buttons will be displayed
	 * @param	array	Associative array of editor parameters
	 */
	function display($name, $html, $width, $height, $col, $row, $buttons = true, $params = array())
	{
		$this->_loadEditor($params);

		//check if editor is already loaded
		if(is_null(($this->_editor))) {
			return;
		}

		// Backwards compatibility. Width and height should be passed without a semicolon from now on.
		// If editor plugins need a unit like "px" for CSS styling, they need to take care of that
		$width	= str_replace( ';', '', $width );
		$height	= str_replace( ';', '', $height );

		// Initialize variables
		$return = null;

		$args['name'] 		 = $name;
		$args['content']	 = $html;
		$args['width'] 		 = $width;
		$args['height'] 	 = $height;
		$args['col'] 		 = $col;
		$args['row'] 		 = $row;
		$args['buttons']	 = $buttons;
		$args['event'] 		 = 'onDisplay';

		$results[] = $this->_editor->update($args);

		foreach ($results as $result)
		{
			if (trim($result)) {
				$return .= $result;
			}
		}
		return $this->_tagForSEF['start'] . $return . $this->_tagForSEF['end'];
	}

	/**
	 * Save the editor content
	 *
	 * @param	string	The name of the editor control
	 */
	function save( $editor )
	{
		$this->_loadEditor();

		//check if editor is already loaded
		if(is_null(($this->_editor))) {
			return;
		}

		$args[] = $editor;
		$args['event'] = 'onSave';

		$return = '';
		$results[] = $this->_editor->update($args);
		foreach ($results as $result) {
			if (trim($result)) {
				$return .= $result;
			}
		}
		return $return;
	}

	/**
	 * Get the editor contents
	 *
	 * @param	string	The name of the editor control
	 */
	function getContent( $editor )
	{
		$this->_loadEditor();

		$args['name'] = $editor;
		$args['event'] = 'onGetContent';

		$return = '';
		$results[] = $this->_editor->update($args);
		foreach ($results as $result) {
			if (trim($result)) {
				$return .= $result;
			}
		}
		return $return;
	}

	/**
	 * Set the editor contents
	 *
	 * @param	string	The name of the editor control
	 * @param	string	The contents of the text area
	 */
	function setContent( $editor, $html )
	{
		$this->_loadEditor();

		$args['name'] = $editor;
		$args['html'] = $html;
		$args['event'] = 'onSetContent';

		$return = '';
		$results[] = $this->_editor->update($args);
		foreach ($results as $result) {
			if (trim($result)) {
				$return .= $result;
			}
		}
		return $return;
	}

	/**
	 * Get the editor buttons
	 *
	 * @param	mixed	$buttons Can be boolean or array, if boolean defines if the buttons are displayed, if array defines a list of buttons not to show.
	 * @access public
	 * @since 1.5
	 */
	 function getButtons($editor, $buttons = true)
	 {
		$result = array();

		if(is_bool($buttons) && !$buttons) {
			return $result;
		}

		// Get plugins
		$plugins = JPluginHelper::getPlugin('editors-xtd');

		foreach($plugins as $plugin)
		{
			if(is_array($buttons) &&  in_array($plugin->name, $buttons)) {
				continue;
			}

			$isLoaded = JPluginHelper::importPlugin('editors-xtd', $plugin->name, false);

			$className = 'plgButton'.$plugin->name;
			if(class_exists($className)) {
				$plugin = new $className($this, (array)$plugin);
			}

			// Try to authenticate -- only add to array if authentication is successful
			$resultTest = $plugin->onDisplay($editor);
			if ($resultTest) $result[] =  $resultTest;
		}

		return $result;
	 }

	/**
	 * Load the editor
	 *
	 * @access	private
	 * @param	array	Associative array of editor config paramaters
	 * @since	1.5
	 */
	function _loadEditor($config = array())
	{
		//check if editor is already loaded
		if(!is_null(($this->_editor))) {
			return;
		}

		jimport('joomla.filesystem.file');

		// Build the path to the needed editor plugin
		$name = JFilterInput::clean($this->_name, 'cmd');
		$path = JPATH_SITE.DS.'plugins'.DS.'editors'.DS.$name.'.php';

		if ( ! JFile::exists($path) )
		{
			$message = JText::_('Cannot load the editor');
			JError::raiseWarning( 500, $message );
			return false;
		}

		// Require plugin file
		require_once $path;

		// Get the plugin
		$plugin   =& JPluginHelper::getPlugin('editors', $this->_name);
		$params   = new JParameter($plugin->params);
		$params->loadArray($config);
		$plugin->params = $params;

		// Build editor plugin classname
		$name = 'plgEditor'.$this->_name;
		if($this->_editor = new $name ($this, (array)$plugin))
		{
			// load plugin parameters
			$this->initialise();
			JPluginHelper::importPlugin('editors-xtd');
		}
	}
}