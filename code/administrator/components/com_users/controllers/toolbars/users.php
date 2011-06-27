<?php
/**
 * @version		$Id: users.php 1963 2011-06-24 20:25:04Z johanjanssens $
 * @category	Nooku
 * @package		Nooku_Server
 * @subpackage	Users
 * @copyright	Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://www.nooku.org
 */

/**
 * Users Toolbar Class
 *
 * @author      Gergo Erdosi <http://nooku.assembla.com/profile/gergoerdosi>
 * @category	Nooku
 * @package		Nooku_Server
 * @subpackage	Users
 */
class ComUsersControllerToolbarUsers extends ComDefaultControllerToolbarDefault
{
    public function getCommands()
    {
        $this->addSeperator()
			 ->addEnable()
			 ->addDisable()
			 ->addSeperator();
			 
		if($this->getController()->canLogout()) {	 
			 $this->addLogout();
		}
			
	    return parent::getCommands();
    }
}