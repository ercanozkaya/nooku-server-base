<?php
/**
 * @version     $Id: menubar.php 2039 2011-06-26 19:33:42Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Cache
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Cache Menubar Class
 *
 * @author      Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Cache
 */
class ComCacheControllerToolbarMenubar extends ComDefaultControllerToolbarMenubar
{
    public function getCommands()
    { 
        $this->addCommand('Groups', array(
        	'href' => JRoute::_('index.php?option=com_cache&view=groups'),
        	'active' => true 
        ));
        
        $this->addCommand('Keys', array(
        	'href' => JRoute::_('index.php?option=com_cache&view=keys'),
        ));
         
        return parent::getCommands();
    }
}