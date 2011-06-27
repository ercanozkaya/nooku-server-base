<?php 
/**
 * @version     $Id: list.php 1620 2011-06-07 10:36:41Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Modules
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<?= @helper('behavior.tooltip') ?>

<table class="adminlist" summary="Add Module">
	<thead>
		<tr>
			<th colspan="4">
				<?= @text('Select module') ?>
			</th>
		</tr>
	</thead>
	<tbody>
	<? $i = 0; foreach($modules as $module) : ?>
		<? if(!$i%2) : ?>
			<tr valign="top">
		<? endif; ?>
		<? $last = $i+1 == count($modules) ?>

		<td width="50%">
			<span <?= KHelperArray::toString(array(
				'class' 	=> 'editlinktip hasTip',
				'title'		=> @escape(@text($module->description)),
				'name'		=> 'type',
				'value'		=> $module->type
			)) ?>>
				<a href="<?= @route('view=module&layout=form&type='.$module->type) ?>">
					<?= @text(@escape($module->title)) ?>
				</a>
			</span>
		</td>

		<? if($last) : ?> 
			<td width="50%">&nbsp;</td>
		<? endif; ?>
		
		<? if($i%2 || $last) : ?> 
			</tr>
		<? endif; ?>
	<? $i++; endforeach ?>
	</tbody>
</table>