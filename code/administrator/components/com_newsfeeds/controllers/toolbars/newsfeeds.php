<?php
/**
 * @version     $Id: newsfeeds.php 1893 2011-06-23 17:14:13Z johanjanssens $
 * @category    Nooku
 * @package     Nooku_Server
 * @subpackage  Newsfeeds
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Newsfeeds Toolbar Class
 *
 * @author      Babs Gšsgens <http://nooku.assembla.com/profile/babsgosgens>
 * @category    Nooku
 * @package     Nooku_Server
 * @subpackage  Newsfeeds
 */
class ComNewsfeedsControllerToolbarNewsfeeds extends ComDefaultControllerToolbarDefault
{
    public function getCommands()
    {
        $this->addSeperator()
			 ->addEnable()
			 ->addDisable();
	
	    return parent::getCommands();
    }
}