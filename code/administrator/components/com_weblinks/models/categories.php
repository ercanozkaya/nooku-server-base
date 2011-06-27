<?php
/**
 * @version		$Id: categories.php 1294 2011-05-16 22:57:57Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Weblinks
 * @copyright	Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://www.nooku.org
 */

/**
 * Category Model Class
 *
 * @author    	Jeremy Wilken <http://nooku.assembla.com/profile/gnomeontherun>
 * @category 	Nooku
 * @package     Nooku_Server
 * @subpackage  Weblinks
 */
class ComWeblinksModelCategories extends ComDefaultModelDefault
{
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'table' => 'admin::com.categories.database.table.categories'
        ));

        parent::_initialize($config);
    }
    
    protected function _buildQueryColumns(KDatabaseQuery $query)
    {
		parent::_buildQueryColumns($query);

		$query->select('COUNT(weblinks.id) AS numlinks');
    }

	protected function _buildQueryGroup(KDatabaseQuery $query)
    {
		parent::_buildQueryGroup($query);
		
		$query->group('tbl.id');
    }

    protected function _buildQueryJoins(KDatabaseQuery $query)
    {
		parent::_buildQueryJoins($query);
		
		$query->join('LEFT', 'weblinks AS weblinks', 'weblinks.catid = tbl.id');
    }

	protected function _buildQueryOrder(KDatabaseQuery $query)
    {
		parent::_buildQueryOrder($query);
		
		$query->order('tbl.ordering', 'DESC');
    }

	protected function _buildQueryWhere(KDatabaseQuery $query)
    {
		parent::_buildQueryWhere($query);
		
		$query->where('tbl.section', '=', 'com_weblinks')
			  ->where('tbl.published', '=', '1')
			  ->where('weblinks.published', '=', '1')
			  ->where('tbl.access', '<=', KFactory::get('lib.joomla.user')->get('aid', '0'));
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
                    ->where('tbl.section', '=', 'com_weblinks');

                $this->_buildQueryOrder($query);
                        
                $this->_column[$column] = $table->select($query);
            }
        }
            
        return $this->_column[$column];
    }
}