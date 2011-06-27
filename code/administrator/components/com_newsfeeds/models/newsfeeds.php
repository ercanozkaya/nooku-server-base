<?php
/**
 * @version     $Id: newsfeeds.php 1725 2011-06-12 12:21:13Z gergoerdosi $
 * @category    Nooku
 * @package     Nooku_Server
 * @subpackage  Newsfeeds
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Newsfeeds Model Class
 *
 * @author      Babs G�sgens <http://nooku.assembla.com/profile/babsgosgens>
 * @category    Nooku
 * @package     Nooku_Server
 * @subpackage  Newsfeeds
 */
class ComNewsfeedsModelNewsfeeds extends ComDefaultModelDefault
{
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_state
            ->insert('published', 'int')
            ->insert('category', 'int');
    }

    protected function _buildQueryColumns(KDatabaseQuery $query)
    {
        parent::_buildQueryColumns($query);

        $query->select('category.title AS category_title');
    }

    protected function _buildQueryJoins(KDatabaseQuery $query)
    {
        // Exclude joins if counting records.
        if(!$query->count) {
            $query->join('LEFT', 'categories AS category', 'category.id = tbl.catid');
        }
    }

    protected function _buildQueryWhere(KDatabaseQuery $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->_state;

        if (is_numeric($state->published)) {
            $query->where('tbl.published', '=', $state->published);
        }

        if ($state->category) {
            $query->where('tbl.catid', '=', $state->category);
        }

        if (!empty($state->search)) {
            $query->where('LOWER(tbl.name)', 'LIKE', '%'.strtolower($state->search).'%');
        }
    }

    protected function _buildQueryOrder(KDatabaseQuery $query)
    {
        $sort       = $this->_state->sort;
        $direction  = strtoupper($this->_state->direction);

        if($sort) {
            $query->order($this->getTable()->mapColumns($sort), $direction);
        }

        if(array_key_exists('ordering', $this->getTable()->getColumns())) {
            $query->order('category.title, tbl.ordering', 'ASC');
        }
    }
}