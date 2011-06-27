<?php
/**
 * @version     $Id: default.php 1987 2011-06-26 16:25:52Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Users
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<script src="media://lib_koowa/js/koowa.js" />
<style src="media://lib_koowa/css/koowa.css" />

<?= @template('default_sidebar', array('groups' => $groups)); ?>

<form action="<?= @route() ?>" method="get" class="-koowa-grid">
	<table class="adminlist">
		<thead>
			<tr>
				<th width="10"></th>
				<th>
					<?= @helper('grid.sort', array('title' => 'Name', 'column' => 'name')) ?>
				</th>
				<th width="15%">
					<?= @helper('grid.sort',  array('title' => 'Username', 'column' => 'username')) ?>
				</th>
				<th width="8%">
					<?= @helper('grid.sort',  array('title' => 'Logged In', 'column' => 'logged_in')) ?>
				</th>
				<th width="8%">
					<?= @helper('grid.sort',  array('title' => 'Enabled', 'column' => 'enabled')) ?>
				</th>
				<th width="15%">
					<?= @helper('grid.sort',  array('title' => 'Group', 'column' => 'group_name')) ?>
				</th>
				<th width="15%">
					<?= @helper('grid.sort',  array('title' => 'E-Mail', 'column' => 'email')) ?>
				</th>
				<th width="10%">
					<?= @helper('grid.sort',  array('title' => 'Last Visit', 'column' => 'last_visited_on')) ?>
				</th>
			</tr>
			<tr>
				<td align="center">
					<?= @helper( 'grid.checkall'); ?>
				</td>
				<td colspan="2">
					<?= @helper( 'grid.search'); ?>
				</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<?= @helper('paginator.pagination', array('total' => $total)) ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<? $i = 0 ?>
		<? foreach($users as $user) : ?>
			<tr>
				<td align="center">
					<?= @helper('grid.checkbox' , array('row' => $user)) ?>
				</td>
				<td>
					<a href="<?= @route('view=user&id='.$user->id) ?>">
						<?= @escape($user->name) ?>
					</a>
				</td>
				<td>
					<?= @escape($user->username) ?>
				</td>
				<td align="center">
					<img src="media://system/images/<?= $user->logged_in ? 'tick.png' : 'publish_x.png' ?>" border="0" />
				</td>
				<td align="center">
					<?= @helper('grid.enable', array('row' => $user, 'option' => 'com_users', 'view' => 'users')) ?>
				</td>
				<td>
					<?= @escape($user->group_name) ?>
				</td>
				<td>
					<?= @escape($user->email) ?>
				</td>
				<td>
					<? if($user->last_visited_on == '0000-00-00 00:00:00') : ?>
						<?= @text('Never') ?>
					<? else : ?>
						<?= @helper('date.humanize', array('date' => $user->last_visited_on)) ?>
					<? endif ?>
				</td>
			</tr>
			<? $i++ ?>
		<? endforeach ?>
		</tbody>
	</table>
</form>