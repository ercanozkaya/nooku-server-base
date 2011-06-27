<?php
/**
 * @version     $Id: file.php 1829 2011-06-21 01:59:15Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Files
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * File Controller Class
 *
 * @author      Ercan Ozkaya <http://nooku.assembla.com/profile/ercanozkaya>
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Files
 */

class ComFilesControllerToolbarFiles extends ComDefaultControllerToolbarDefault
{
    public function getCommands()
    {
		 $this->reset()
		      ->addDelete();
		 
		 return parent::getCommands();
    }

	protected function _commandDelete(KControllerToolbarCommand $command)
    {
        $command->append(array(
            'attribs'  => array(
                'href' => '#'
            )
        ));
    }
}