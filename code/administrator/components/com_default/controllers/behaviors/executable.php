<?php
/**
 * @version     $Id: executable.php 3647 2011-06-27 13:45:19Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Default Controller Executable Behavior
.*
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultControllerBehaviorExecutable extends KControllerBehaviorExecutable
{  
	/**
     * Command handler
     * 
     * @param   string      The command name
     * @param   object      The command context
     * @return  boolean     Can return both true or false.  
     * @throws  KControllerException
     */
    final public function execute( $name, KCommandContext $context) 
    { 
        $parts = explode('.', $name); 
        
        if($parts[0] == 'before') 
        { 
            if(!$this->_checkToken($context)) 
            {    
                $context->setError(new KControllerException(
                	'Invalid token or session time-out', KHttpResponse::FORBIDDEN
                ));
                
                return false;
            }
        }
        
        return parent::execute($name, $context); 
    }
    
    /**
     * Generic authorize handler for controller add actions
     * 
     * @return  boolean     Can return both true or false.  
     */
    public function canAdd()
    {
        $result = false;
        
        if(parent::canAdd())
        {
            if(version_compare(JVERSION,'1.6.0','ge')) {
                $result = KFactory::get('lib.joomla.user')->authorise('core.create');
            } else {
                $result = KFactory::get('lib.joomla.user')->get('gid') > 22;
            }
        }
        
        return $result;
    }
    
  	/**
     * Generic authorize handler for controller edit actions
     * 
     * @return  boolean     Can return both true or false.  
     */
    public function canEdit()
    {
        $result = false;
        
        if(parent::canEdit())
        {
            if(version_compare(JVERSION,'1.6.0','ge')) {
                $result = KFactory::get('lib.joomla.user')->authorise('core.edit');
            } else {
                $result = KFactory::get('lib.joomla.user')->get('gid') > 22;
            }
        }
            
        return $result;
    }
    
    /**
     * Generic authorize handler for controller delete actions
     * 
     * @return  boolean     Can return both true or false.  
     */
    public function canDelete()
    {
        $result = false;
        
        if(parent::canDelete())
        {
            if(version_compare(JVERSION,'1.6.0','ge')) {
                $result = KFactory::get('lib.joomla.user')->authorise('core.delete');
            } else {
                $result = KFactory::get('lib.joomla.user')->get('gid') > 22;
            }
        }
          
        return $result;
    }
    
    /**
	 * Check the token to prevent CSRF exploits
	 *
	 * @param   object  The command context
	 * @return  boolean Returns FALSE if the check failed. Otherwise TRUE.
	 */
    protected function _checkToken(KCommandContext $context)
    {
        //Check the token
        if($context->caller->isDispatched())
        {  
            $method = KRequest::method();
            
            //Only check the token for PUT, DELETE and POST requests
            if(($method != KHttpRequest::GET) && ($method != KHttpRequest::OPTIONS)) 
            {     
                if( KRequest::token() !== JUtility::getToken()) {     
                    return false;
                }
            }
        }
       
        return true;
    }
}