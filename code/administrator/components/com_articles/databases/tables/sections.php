<?php
/**
 * @version     $Id: sections.php 2038 2011-06-26 18:08:09Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Articles
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       	http://www.nooku.org
 */

/**
 * Sections Database Table Class
 *
 * @author      John Bell <http://nooku.assembla.com/profile/johnbell>
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Articles    
 */
class ComArticlesDatabaseTableSections extends KDatabaseTableDefault
{
	public function  _initialize(KConfig $config) 
  	{
		$config->identity_column = 'id';
			        
		$config->append(array(
    		'name' 			=> 'sections',
			'base' 			=> 'sections',
			'behaviors'		=>  array('lockable', 'orderable', 'sluggable', 'cascadable'),
    		'column_map'	=>  
				array(
					'enabled' 	=> 'published',
               		'locked_on' => 'checked_out_time',
               		'locked_by' => 'checked_out',
                	'slug' 		=> 'alias'
				),
			'filters'		=> 
		        array(
				    'description' => array('html', 'tidy'),
		        ),
		));
	
      	parent::_initialize($config);
  	}
}