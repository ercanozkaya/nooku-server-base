<?php
/**
 * @version     $Id: plugins.php 1628 2011-06-07 15:40:07Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Plugins
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Plugins Database Table Class
 *
 * @author      Stian Didriksen <http://nooku.assembla.com/profile/stiandidriksen>
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Plugins    
 */
class ComPluginsDatabaseTablePlugins extends KDatabaseTableDefault
{
    public function  _initialize(KConfig $config) 
    {
        $config->identity_column = 'id';

        $config->append(array(
            'name'       => 'plugins',
            'behaviors'  =>  array('lockable', 'orderable'),
            'column_map' =>  array(
                'title'     => 'name',
                'name'		=> 'element',
                'enabled' 	=> 'published',
                'locked_on' => 'checked_out_time',
                'locked_by' => 'checked_out',
                'type'		=> 'folder'
            ),
            'filters'    => array('params' => 'ini')
        ));
        
        parent::_initialize($config);
    }
}