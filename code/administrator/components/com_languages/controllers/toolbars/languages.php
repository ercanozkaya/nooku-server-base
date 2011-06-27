<?php
/**
 * @version     $Id: languages.php 2053 2011-06-27 13:01:20Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Languages
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Languages Toolbar Class
 *
 * @author      Ercan Ozkaya <http://nooku.assembla.com/profile/ercanozkaya>
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Languages
 */
class ComLanguagesControllerToolbarLanguages extends ComDefaultControllerToolbarDefault
{
    public function getCommands()
    {
        $this->addDefault();
        
        return parent::getCommands();
    }
     
    protected function _commandDefault(KControllerToolbarCommand $command)
    {
        $command->label = JText::_('Make Default');
        
        $command->append(array(
        	'attribs' => array(
                'data-action' => 'edit',
                'data-data'   => '{default:1}'
            )
        ));
    }
}