<?php defined('_JEXEC') or die('Restricted access'); ?>

<script language="javascript" type="text/javascript">
<!--
	function submitbutton(task)
	{
		var f = document.adminForm;
		if (task == 'deleteconfirm') {
			id = radioGetCheckedValue( f.id );
			//document.popup.show('index.php?option=com_menus&tmpl=component&task=deleteconfirm&id='+id, 700, 500, null);
		} else {
			submitform(task);
		}
	}

	function menu_listItemTask( id, task, option )
	{
		var f = document.adminForm;
		cb = eval( 'f.' + id );
		if (cb) {
			cb.checked = true;
			submitbutton(task);
		}
		return false;
	}
//-->
</script>

<form action="index.php" method="post" name="adminForm">

	<table class="adminlist">
	<thead>
		<tr>
			<th width="20">
				&nbsp;
			</th>
			<th class="title" nowrap="nowrap">
				<?php echo JText::_( 'Title' ); ?>
			</th>
			<th class="title" nowrap="nowrap">
				<?php echo JText::_( 'Type' ); ?>
			</th>
			<th width="10%">
				<?php echo JText::_( 'NUM Published' ); ?>
			</th>
			<th width="15%">
				<?php echo JText::_( 'NUM Unpublished' ); ?>
			</th>
			<th width="15%">
				<?php echo JText::_( 'NUM Trash' ); ?>
			</th>
			<th width="15%">
				<?php echo JText::_( 'NUM Modules' ); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="13">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php $i = 0; $k = 0; ?>
	<?php foreach ($this->menus as $menu) : ?>
		<?php
			// Get the current iteration and set a few values
			$link 	= 'index.php?option=com_menus&amp;task=editMenu&amp;id='. $menu->id;
			$linkA 	= 'index.php?option=com_menus&amp;task=view&amp;menutype='. htmlspecialchars($menu->menutype);
		?>
		<tr>
			<td width="30" align="center">
				<input type="radio" id="cb<?php echo $i;?>" name="id" value="<?php echo $menu->id; ?>" onclick="isChecked(this.checked);" />
			</td>
			<td>
			<span class="editlinktip hasTip" title="<?php echo JText::_( 'Edit Menu Name' );?>::<?php echo htmlspecialchars($menu->title); ?>">
				<a href="<?php echo $link; ?>">
					<?php echo htmlspecialchars($menu->title); ?></a></span>
			</td>
			<td>
				<?php echo htmlspecialchars($menu->menutype); ?>
			</td>
			<td align="center">
				<?php
				echo $menu->published;
				?>
			</td>
			<td align="center">
				<?php
				echo $menu->unpublished;
				?>
			</td>
			<td align="center">
				<?php
				echo $menu->trash;
				?>
			</td>
			<td align="center">
				<?php
				echo $menu->modules;
				?>
			</td>
		</tr>
		<?php $i++; ?>
	<?php endforeach; ?>
	</tbody>
	</table>

	<input type="hidden" name="option" value="com_menus" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
