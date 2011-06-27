<?php
/**
 * @version     $Id: executable.php 1945 2011-06-24 03:00:20Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Plugins
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Plugin Controller Executable Behavior 
 *
 * @author      Stian Didriksen <http://nooku.assembla.com/profile/stiandidriksen>
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Plugins    
 */
class ComPluginsControllerBehaviorExecutable extends ComDefaultControllerBehaviorExecutable
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