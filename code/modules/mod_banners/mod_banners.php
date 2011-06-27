<?php
/**
 * @version     $Id: mod_banners.php 1382 2011-05-20 21:14:31Z johanjanssens $
 * @category    Nooku
 * @package     Nooku_Server
 * @subpackage  Banners
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Module Banners
 *
 * @author      Cristiano Cucco <http://nooku.assembla.com/profile/cristiano.cucco>
 * @category    Nooku
 * @package     Nooku_Server
 * @subpackage  Banners
 */

KLoader::load('site::com.banners.mappings');

echo KFactory::tmp('site::mod.banners.view', array(
    'params'  => $params,
    'module'  => $module,
    'attribs' => $attribs
))->display();