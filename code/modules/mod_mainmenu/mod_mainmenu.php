<?php
/**
* @version		$Id: mod_mainmenu.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

$params->def('menutype', 			'mainmenu');
$params->def('class_sfx', 			'');
$params->def('expand_menu', 		0);
$params->def('activate_parent', 	0);
$params->def('spacer', 				'');
$params->def('end_spacer', 			'');
$params->def('full_active_id', 		0);

// Added in 1.5
$params->def('startLevel', 			0);
$params->def('endLevel', 			0);
$params->def('showAllChildren', 	0);

require(JModuleHelper::getLayoutPath('mod_mainmenu'));