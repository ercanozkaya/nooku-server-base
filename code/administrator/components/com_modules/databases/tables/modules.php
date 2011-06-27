<?php
/**
 * @version     $Id: modules.php 1620 2011-06-07 10:36:41Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Modules
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Modules Database Table Class
 *
 * @author      Stian Didriksen <http://nooku.assembla.com/profile/stiandidriksen>
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Modules    
 */

class ComModulesDatabaseTableModules extends KDatabaseTableDefault
{
    public function  _initialize(KConfig $config) 
    {
        $config->identity_column = 'id';
		
        $config->append(array(
            'name'       => 'modules',
            'behaviors'  => array('lockable', 'orderable'),
            'column_map' => array(
                'enabled'      => 'published',
                'locked_on'    => 'checked_out_time',
                'locked_by'    => 'checked_out',
                'type'	       => 'module'
                ),
            'filters' => array(
                'content' => array('html', 'tidy'),
                'params'  => 'ini' 
                )
            ));
     
        parent::_initialize($config);
    }

	/**
	 * Get default values for all columns
	 *
	 * This method is specialized in order to set the default module position 
	 * and published state
	 * 
	 * @return  array
	 */
	public function getDefaults()
	{
		$defaults = parent::getDefaults();
		
		$defaults['position']		= 'left';
		$defaults['enabled']		= 1;
		$defaults['description']	= '';
		$defaults['module']			= 'mod_custom';
	     
		return $defaults;
	}
}