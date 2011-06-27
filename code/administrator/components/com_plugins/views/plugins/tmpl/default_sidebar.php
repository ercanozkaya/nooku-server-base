<?php 
/**
 * @version     $Id: default_sidebar.php 1627 2011-06-07 15:35:17Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Modules
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<div id="sidebar">
    <h3><?= @text( 'Types' ); ?></h3>
    <ul>
        <li <? if(!$state->type) echo 'class="active"' ?>>
            <a href="<?= @route('&type=') ?>">
                <?= @text('All types') ?>
            </a>
        </li>
        <? foreach($types as $type) : ?>
        <li <? if($state->type == $type->type) echo 'class="active"' ?>>
            <a href="<?= @route('type='.$type->type) ?>">
                <?= $type->type ?>
            </a>
        </li>
        <? endforeach ?>
    </ul>
</div>