<?php
/**
 * @version      $Id: banner.php 1797 2011-06-19 21:15:19Z tomjanssens $
 * @category     Nooku
 * @package      Nooku_Server
 * @subpackage   Banners
 * @copyright    Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link         http://www.nooku.org
 */

/**
 * Banners Controller Class - Banner
 *
 * @author      Cristiano Cucco <cristiano.cucco at gmail dot com>
 * @category    Nooku
 * @package     Nooku_Server
 * @subpackage  Banners    
 */
class ComBannersControllerBanner extends ComDefaultControllerDefault
{     
    public function _actionRead(KCommandContext $context)
	{
        $banner = parent::_actionRead($context);
        
		// Redirect the user if the banner has a url
		if ($banner->clickurl) 
		{
			// Increase hit counter
			if($banner->isHittable()) { 
			    $banner->hit(); 
			}
		    
		    KFactory::get('lib.joomla.application')->redirect($banner->clickurl);
			return true;
		}

		return $banner;
	}
}