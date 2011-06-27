<?php
/**
 * @version     $Id: categories.php 1677 2011-06-09 17:14:00Z JohnBell $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Categories
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Categories Database Table Class
 *
 * @author      John Bell <http://nooku.assembla.com/profile/johnbell>
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Categories
 */
class ComCategoriesDatabaseTableCategories extends KDatabaseTableDefault
{
    public function  _initialize(KConfig $config)
    {
        $config->identity_column = 'id';
        $orderable = $this->getBehavior('admin::com.categories.database.behavior.orderable', array('parent_column' => 'section_id'));

        $config->append(array(
            'name'       => 'categories',
            'behaviors'  => array('lockable',$orderable, 'sluggable', 'cascadable'),
            'column_map' => array(
                'enabled'      => 'published',
                'locked_on'    => 'checked_out_time',
                'locked_by'    => 'checked_out',
                'slug'         => 'alias',
                'section_id'   => 'section'
                ),
            ));

        parent::_initialize($config);
    }
}
