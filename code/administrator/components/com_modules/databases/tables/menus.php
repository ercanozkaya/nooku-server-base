<?php
/**
 * @version     $Id: menus.php 1156 2011-05-11 13:42:45Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Modules
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Menus Database Table Class
 *
 * @author      Stian Didriksen <http://nooku.assembla.com/profile/stiandidriksen>
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Modules    
 */

class ComModulesDatabaseTableMenus extends KDatabaseTableDefault
{
    public function  _initialize(KConfig $config) 
    {
        $config->append(array(
            'name'  => 'modules_menu',
        ));
     
        parent::_initialize($config);
    }
}