<?php
/**
 * @version     $Id: newsfeeds.php 1809 2011-06-20 16:42:57Z johanjanssens $
 * @category    Nooku
 * @package     Nooku_Server
 * @subpackage  Newsfeeds
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Component Loader
 *
 * @author      Babs G�sgens <http://nooku.assembla.com/profile/babsgosgens>
 * @category    Nooku
 * @package     Nooku_Server
 * @subpackage  Newsfeeds
 */

if (!KFactory::get('lib.joomla.user')->authorize( 'com_newsfeeds', 'manage' )) {
	KFactory::get('lib.joomla.application')->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
}

echo KFactory::get('admin::com.newsfeeds.dispatcher')->dispatch();