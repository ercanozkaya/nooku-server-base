<?php 
/**
 * @version     $Id: default.php 1992 2011-06-26 16:27:18Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Sections
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */
defined('KOOWA') or die( 'Restricted access' ); ?>
 
<script src="media://lib_koowa/js/koowa.js" />
<style src="media://lib_koowa/css/koowa.css" />

<form action="<?= @route() ?>" method="get" class="-koowa-grid">	
	<input type="hidden" name="scope" value="<?= $state->scope;?>" />
	<?= @template('default_filter'); ?>
	<table class="adminlist">
		<thead>
			<tr>
				<th width="10"></th>
				<th>
					<?= @helper('grid.sort',  array('column' => 'title')   ); ?>
				</th>
				<th width="5%">
					<?= @helper('grid.sort',  array('column' => 'published')   ); ?>
				</th>
				<th width="8%" nowrap="nowrap">
					<?= @helper('grid.sort',  array( 'title' => 'Order', 'column' => 'ordering')   ); ?>
				</th>
				<th width="10%">
					<?= @helper('grid.sort',  array('title' => 'Access', 'column' => 'access')   ); ?>
				</th>
				<th width="5%" nowrap="nowrap">
					<?= @helper('grid.sort',  array( 'title' => 'Num Categories', 'column' => 'categorycount') ); ?>
				</th>
				<th width="5%" nowrap="nowrap">
					<?= @helper('grid.sort',  array( 'title' => 'Num Active', 'column' => 'activecount') ); ?>
				</th>
			</tr>
			<tr>
				<td align="center">
					<?= @helper( 'grid.checkall'); ?>
				</td>
				<td>
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
					<?= @helper('paginator.pagination', array('total' => $total)); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<? foreach ( $sections as $section ) :  ?>
			<tr>
				<td align="center">
					<?= @helper( 'grid.checkbox' , array('row' => $section)); ?>
				</td>
				<td>
					<a href="<?= @route( 'view=section&id='.$section->id ); ?>">
                        <?= @escape($section->title); ?>
                    </a>
				</td>
				<td align="center">
					<?= @helper('grid.enable', array('row' => $section)) ?>
				</td>
				<td class="order">
					<?= @helper( 'grid.order' , array('row' => $section, 'total' => $total)); ?>
				</td>
				<td align="center">
					<?= @helper('grid.access', array( 'row' => $section)) ;?>
				</td>
				<td align="center">
					<?= $section->categorycount; ?>
				</td>
				<td align="center">
					<?= $section->activecount; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>		
	</table>
</form>		