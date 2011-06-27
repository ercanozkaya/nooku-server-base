<?php
/**
 * @version     $Id: executable.php 1944 2011-06-24 03:00:11Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Languages
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Language Database Row Class
 *
 * @author      Ercan Ozkaya <http://nooku.assembla.com/profile/ercanozkaya>
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Languages
 */
class ComLanguagesControllerBehaviorExecutable extends ComDefaultControllerBehaviorExecutable
{  
    public function canAdd()
    {
        return false;
    }
    
    public function canDelete()
    {
        return false;
    }
}