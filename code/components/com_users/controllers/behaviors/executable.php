<?php
/**
 * @version     $Id: executable.php 1955 2011-06-24 12:08:15Z ercanozkaya $
 * @category    Nooku
 * @package     Nooku_Server
 * @subpackage  Users
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Authorize Command
 *
 * @author      Gergo Erdosi <http://nooku.assembla.com/profile/gergoerdosi>
 * @category    Nooku
 * @package     Nooku_Server
 * @subpackage  Users
 */
class ComUsersControllerBehaviorExecutable extends ComDefaultControllerBehaviorExecutable
{
    public function canAdd()
    {
        $parameters = JComponentHelper::getParams('com_users');

        if($parameters->get('allowUserRegistration') == '0') {
            return false;
        }

        return true;
    }

    public function canRead()
    {
    	$parameters = JComponentHelper::getParams('com_users');

        if($parameters->get('allowUserRegistration') == '0')
        {
	        $view = $this->getView();
	    	if ($view->getName() === 'user' && $view->getLayout() === 'register') {
	    		return false;
	    	}
        }

        return true;
    }

    public function canEdit()
    {
        $request = $this->getRequest();

        if($request->id == 0 || $request->id != KFactory::get('lib.joomla.user')->id) {
            return false;
        }

        $result = !KFactory::get('lib.joomla.user')->guest;
        return $result;
    }
}