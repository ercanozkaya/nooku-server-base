<?php
/**
 * @version     $Id: listbox.php 1759 2011-06-15 22:02:56Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Modules
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Listbox Template Helper
 *
 * @author      Stian Didriksen <http://nooku.assembla.com/profile/stiandidriksen>
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Modules    
 */

class ComModulesTemplateHelperListbox extends KTemplateHelperListbox
{
	/**
	 * Customized to add a few attributes.
	 * 
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 * @see __call()
	 */
	protected function _listbox($config = array())
 	{
		$config = new KConfig($config);

		$config->append(array(
			//@TODO state isn't applied, work on patch later
			'state'		=> array(
				'application'	=> $config->application
			)
		));

		return parent::_listbox($config);
 	}
}