<?php
/**
 * @version   	$Id: menubar.php 3640 2011-06-26 23:00:39Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright  	Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license   	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Default Menubar
.*
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultControllerToolbarMenubar extends KControllerToolbarDefault
{
 	/**
     * Add a command
     * 
     * Disable the menubar only for singular views that are editable.
     *
     * @param   string	The command name
     * @param	mixed	Parameters to be passed to the command
     * @return  KControllerToolbarInterface
     */
    public function addCommand($name, $config = array())
    {
        parent::addCommand($name, $config);
        
        $controller = $this->getController();
        
        if($controller->isEditable() && KInflector::isSingular($controller->getView()->getName())) {
            $this->_commands[$name]->disabled = true;
        }
        
        return $this;
    }
}