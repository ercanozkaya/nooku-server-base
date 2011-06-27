<?php
/**
 * @version      $Id: cascadable.php 1746 2011-06-14 21:47:06Z johanjanssens $
 * @category	 Nooku
 * @package      Nooku_Server
 * @subpackage   Categories
 * @copyright    Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link         http://www.nooku.org
 */

/**
 * Cascadable Database Behavior Class
 *
 * @author      John Bell <http://nooku.assembla.com/profile/johnbell>
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Categories    
 */
class ComCategoriesDatabaseBehaviorCascadable extends KDatabaseBehaviorAbstract
{
    /**
     * Deletes dependent rows.
     *
     * This performs an intelligent delete 
     *
     * @return KDatabaseRowAbstract
     */
    protected function _beforeTableDelete(KCommandContext $context)
    {
        $result = true;
        
        $section = $this->section;
        if ( is_numeric($section) || !$section) {
            $section = 'com_articles';
        } 
        
        $parts = explode('_',$section);
            
        //@TODO : Remove when refactoring is completed
        switch ($parts[1])
        {
            case 'contact':
                $name = 'contacts';
                $package ='contact';
                break;
            default :
                $name = KInflector::pluralize($parts[1]);
                $package = $name;
        }
                 
        $identifier = 'admin::com.'.$package.'.model.'.$name;
        
        $rowset = KFactory::tmp($identifier)
                    ->category($this->id)
                    ->limit(0)
                    ->getList();

        if($rowset->count()) {
            $result = $rowset->delete();
        } 
        
        return $result; 
    }
}