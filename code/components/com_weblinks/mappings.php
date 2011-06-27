<?php
/**
 * @version		$Id: mappings.php 1295 2011-05-16 22:58:08Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Weblinks
 * @copyright	Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://www.nooku.org
 */

/**
 * Factory mappings
 *
 * @author    	Jeremy Wilken <http://nooku.assembla.com/profile/gnomeontherun>
 * @category 	Nooku
 * @package     Nooku_Server
 * @subpackage  Weblinks
 */

KFactory::map('site::com.weblinks.model.categories', 'admin::com.weblinks.model.categories');
KFactory::map('site::com.weblinks.model.weblinks'  , 'admin::com.weblinks.model.weblinks');