<?php
/**
 * @version		$Id: default.php 3524 2011-06-20 01:56:26Z johanjanssens $
 * @category	Koowa
 * @package     Koowa_Dispatcher
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Default controller dispatcher
 * 
 * The default dispatcher mplements a signleton. After instantiation the 
 * object can be access using the mapped dispatcher identifier.
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Dispatcher
 */

class KDispatcherDefault extends KDispatcherAbstract 
{ 
    /**
     * Force creation of a singleton
     *
     * @return KDispatcherDefault
     */
    public static function instantiate($config = array())
    {
        static $instance;
        
        if ($instance === NULL) 
        {
            //Create the singleton
            $classname = $config->identifier->classname;
            $instance = new $classname($config);
            
            //Add the factory map to allow easy access to the singleton
            KFactory::map('dispatcher', $config->identifier);
        }
        
        return $instance;
    }
}