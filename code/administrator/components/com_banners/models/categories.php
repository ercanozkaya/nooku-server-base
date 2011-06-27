<?php
/**
 * @version     $Id: categories.php 1839 2011-06-21 20:19:40Z tomjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Banners
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Categories Model Class
 *
 * @author      Cristiano Cucco <http://nooku.assembla.com/profile/cristiano.cucco>
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Banners    
 */
class ComBannersModelCategories extends ComDefaultModelDefault 
{
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'table' => 'admin::com.categories.database.table.categories'
        ));

        parent::_initialize($config);
    }
    
    protected function _buildQueryWhere(KDatabaseQuery $query)
    {
        parent::_buildQueryWhere($query);
        
        $query->where('tbl.section', '=', 'com_banner');
    }
    
    protected function _buildQueryOrder(KDatabaseQuery $query)
    {
        $query->order('tbl.title', 'ASC');
    }
    
    public function getColumn($column)
    {   
        if (!isset($this->_column[$column])) 
        {   
            if($table = $this->getTable()) 
            {
                $query = $table->getDatabase()->getQuery()
                    ->distinct()
                    ->group('tbl.'.$table->mapColumns($column))
                    ->where('tbl.section', '=', 'com_banner');

                $this->_buildQueryOrder($query);
                        
                $this->_column[$column] = $table->select($query);
            }
        }
            
        return $this->_column[$column];
    }
}