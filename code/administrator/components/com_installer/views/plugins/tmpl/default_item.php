<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<tr class="<?php echo "row".$this->item->index % 2; ?>" <?php echo $this->item->style; ?>>
	<td><?php echo $this->pagination->getRowOffset( $this->item->index ); ?></td>
	<td align="center">
		<input type="checkbox" id="cb<?php echo $this->item->index;?>" name="eid[<?php echo $this->item->id; ?>]" value="<?php echo $this->item->client_id; ?>" onclick="isChecked(this.checked);" <?php echo $this->item->cbd; ?> />
	</td>
	<td>
		<span class="bold"><?php echo $this->item->name; ?></span>
	</td>
	<td><?php echo $this->item->folder; ?></td>
	<td align="center" <?php if(@$this->item->legacy) echo 'class="legacy-mode"'; ?>><?php echo @$this->item->version != '' ? $this->item->version : '&nbsp;'; ?></td>
	<td><?php echo @$this->item->creationdate != '' ? $this->item->creationdate : '&nbsp;'; ?></td>
	<td>
		<span class="editlinktip hasTip" title="<?php echo JText::_( 'Author Information' );?>::<?php echo $this->item->author_info; ?>">
			<?php echo @$this->item->author != '' ? $this->item->author : '&nbsp;'; ?>
		</span>
	</td>
	<td align="center">
		<span class="editlinktip hasTip" title="<?php echo (@$this->item->legacy ? JText::_( 'Not Compatible Extension') : JText::_('Compatible Extension'));?>">
			<img src="<?php echo JURI::root(true) ?>/media/system/images/<?php echo (@$this->item->legacy ? 'publish_x.png' : 'tick.png');?>"/>
		</span>
	</td>
</tr>
