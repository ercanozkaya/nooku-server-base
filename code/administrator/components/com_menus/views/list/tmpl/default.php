<?php defined('_JEXEC') or die('Restricted access'); ?>

<div id="sidebar">
	<h3><?= JText::_( 'Menus' ); ?></h3>
	<?php echo $this->loadTemplate('menus'); ?>
</div>

<div class="-koowa-box-flex">
	<form action="index.php?option=com_menus&amp;menutype=<?php echo htmlspecialchars($this->menutype); ?>" method="post" name="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th width="20">
					
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort',   'Menu Item', 'm.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="5%">
					<?php echo JText::_( 'Default' ); ?>
				</th>
				<th width="5%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort',   'Published', 'm.published', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="8%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort',   'Order by', 'm.ordering', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
					<?php if ($this->ordering) echo JHTML::_('grid.order',  $this->items ); ?>
				</th>
				<th width="10%">
					<?php echo JHTML::_('grid.sort',   'Access', 'groupname', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="20%" class="title">
					<?php echo JHTML::_('grid.sort',   'Type', 'm.type', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
			</tr>
			<tr>
				<td align="center">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</td>
				<td>
					<?php echo JText::_( 'Filter' ); ?>:
					<input type="text" name="search" id="search" value="<?php echo htmlspecialchars($this->lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
					<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
				</td>
				<td></td>
				<td>
					<?php echo $this->lists['state'];	?>
				</td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="12">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$i = 0;
		$n = count( $this->items );
		$rows = &$this->items;
		foreach ($rows as $row) :
			$access 	= JHTML::_('grid.access',   $row, $i );
			$checked 	= JHTML::_('grid.checkedout',   $row, $i );
			$published 	= JHTML::_('grid.published', $row, $i );
			?>
			<tr>
				<td align="center">
					<?php echo $checked; ?>
				</td>
				<td nowrap="nowrap">
					<?php if (  JTable::isCheckedOut($this->user->get('id'), $row->checked_out ) ) : ?>
					<?php echo $row->treename; ?>
					<?php else : ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'Edit Menu' );?>::<?php echo $row->treename; ?>">
					<a href="<?php echo JRoute::_('index.php?option=com_menus&menutype=' . htmlspecialchars($row->menutype) . '&task=edit&cid[]=' . $row->id); ?>"><?php echo $row->treename; ?></a></span>
					<?php endif; ?>
				</td>
				<td align="center">
					<?php if ( $row->home == 1 ) : ?>
					<img src="templates/default/images/menu/icon-16-default.png" alt="<?php echo JText::_( 'Default' ); ?>" />
					<?php else : ?>
					&nbsp;
					<?php endif; ?>
				</td>
				<td align="center">
					<?php echo $published;?>
				</td>
				<td class="order" nowrap="nowrap">
					<span><?php echo $this->pagination->orderUpIcon( $i, $row->parent == 0 || $row->parent == @$rows[$i-1]->parent, 'orderup', 'Move Up', $this->ordering); ?></span>
					<span><?php echo $this->pagination->orderDownIcon( $i, $n, $row->parent == 0 || $row->parent == @$rows[$i+1]->parent, 'orderdown', 'Move Down', $this->ordering ); ?></span>
					<?php $disabled = $this->ordering ?  '' : 'disabled="disabled"'; ?>
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
				</td>
				<td align="center">
					<?php echo $access;?>
				</td>
				<td>
					<span class="editlinktip" style="text-transform:capitalize"><?php echo ($row->type == 'component') ? $row->view : $row->type; ?></span>
				</td>
			</tr>
			<?php
			$i++;
			?>
		<?php endforeach; ?>
		</tbody>
		</table>
	
		<input type="hidden" name="option" value="com_menus" />
		<input type="hidden" name="menutype" value="<?php echo htmlspecialchars($this->menutype); ?>" />
		<input type="hidden" name="task" value="view" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>