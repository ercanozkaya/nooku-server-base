<?php
/**
 * @version     $Id: html.php 2032 2011-06-26 17:01:55Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Modules
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Modules HTML View Class
 *   
 * @author    	Stian Didriksen <http://nooku.assembla.com/profile/stiandidriksen>
 * @category    Nooku
 * @package     Nooku_Server
 * @subpackage  Modules
 */
class ComModulesViewModuleHtml extends ComDefaultViewHtml
{
	public function display()
	{
	    $module = $this->getModel()->getItem();
	   
	    if($module->application == 'site') {
	        KFactory::get('lib.joomla.language')->load($module->type, JPATH_SITE );
	    } else {
		    KFactory::get('lib.joomla.language')->load($module->type);
	    }
		
		return parent::display();
	}
}