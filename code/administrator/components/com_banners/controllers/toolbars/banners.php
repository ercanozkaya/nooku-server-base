<?php
/**
 * @version     $Id: banners.php 2044 2011-06-26 21:15:35Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Banners
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Banners Toolbar Class
 *
 * @author      Cristiano Cucco <http://nooku.assembla.com/profile/cristiano.cucco>
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Banners    
 */
class ComBannersControllerToolbarBanners extends ComDefaultControllerToolbarDefault
{
    public function getCommands()
    {
        $this->addSeperator()
              ->addEnable()
              ->addDisable()
              ->addSeperator()
              ->addModal(array(
                    'label'  => 'Preferences',
              		'height' => 88,
                    'href'   => 'index.php?option=com_config&controller=component&component=com_banners'
                  ));
         
        return parent::getCommands();
    }
}