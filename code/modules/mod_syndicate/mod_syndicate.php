<?php
/**
* @version		$Id: mod_syndicate.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

$params->def('text', 'Feed Entries');
$params->def('format', 'rss');

$link = modSyndicateHelper::getLink($params);

if(is_null($link)) {
	return;
}

require(JModuleHelper::getLayoutPath('mod_syndicate'));
