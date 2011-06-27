<?php
/**
 * @version     $Id: menubar.php 2014 2011-06-26 16:51:37Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Modules
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Modules Menubar Class
 *
 * @author      Stian Didriksen <http://nooku.assembla.com/profile/stiandidriksen>
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Modules
 */
class ComModulesControllerToolbarMenubar extends ComDefaultControllerToolbarMenubar
{
    public function getCommands()
    { 
        $this->addCommand('Modules', array(
        	'href' => JRoute::_('index.php?option=com_modules&view=modules'), 
            'active' => true 
        ));
        
        $this->addCommand('Plugins', array(
        	'href' => JRoute::_('index.php?option=com_plugins&view=plugins'),
        ));
        
        $this->addCommand('Templates', array(
        	'href' => JRoute::_('index.php?option=com_templates&view=templates'),
        ));
        
        $this->addCommand('Languages', array(
        	'href' => JRoute::_('index.php?option=com_languages&view=languages'),
        ));
         
        return parent::getCommands();
    }
}