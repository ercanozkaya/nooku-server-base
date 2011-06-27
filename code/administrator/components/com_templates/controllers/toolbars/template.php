<?php
/**
 * @version     $Id: template.php 1895 2011-06-23 17:14:43Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Languages
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Templates Toolbar Class
 *
 * @author      Ercan Ozkaya <http://nooku.assembla.com/profile/ercanozkaya>
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Languages
 */
class ComTemplatesControllerToolbarTemplate extends ComDefaultControllerToolbarDefault
{
    public function getCommands()
    {
        $this->addSeperator()
             ->addPreview();

        return parent::getCommands();
    }
   
    protected function _commandPreview(KControllerToolbarCommand $command)
    {
        $state = $this->getController()->getModel()->getState();
        
        $template = $state->name;
        $base     = $state->application == 'site' ? JURI::root() : JURI::base();
        
        $command->append(array(
            'width'   => '640',
            'height'  => '480',
        ))->append(array(
            'attribs' => array(
                'href' 	 =>  $base.'index.php?tp=1&template='.$template,
                'target' => 'preview'
            )
        ));
    }
}