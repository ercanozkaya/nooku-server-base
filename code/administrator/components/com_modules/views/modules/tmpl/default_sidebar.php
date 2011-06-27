<?php 
/**
 * @version     $Id: default_sidebar.php 1623 2011-06-07 15:19:49Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Modules
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<div id="sidebar">
   	<h3><?= @text( 'Applications' ); ?></h3>
   	<ul>
        <li <? if($state->application == 'site') echo 'class="active"' ?>>
        	<a href="<?= @route('&application=site') ?>">
        	    <?= @text('Site') ?>
        	</a>
        </li>
        <li <? if($state->application == 'administrator') echo 'class="active"' ?>>
        	<a href="<?= @route('&application=administrator') ?>">
        	    <?= @text('Administrator') ?>
        	</a>
        </li>
    </ul>
</div>