<?php
/**
 * @version		$Id: admin.checkin.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla
 * @subpackage	Checkin
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Make sure the user is authorized to view this page

$user = & JFactory::getUser();
if (!$user->authorize( 'com_checkin', 'manage' )) {
	$mainframe->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
}
$db			=& JFactory::getDBO();
$nullDate	= $db->getNullDate();
?>
<div id="tablecell">
	<table class="adminlist">
	<thead>
		<tr>
			<th class="title">
				<?php echo JText::_( 'Database Table' ); ?>
			</th>
			<th class="title">
				<?php echo JText::_( 'Num of Items' ); ?>
			</th>
			<th class="title">
				<?php echo JText::_( 'Checked-In' ); ?>
			</th>
			<th class="title">
			</th>
		</tr>
	</thead>
	<?php
	$tables = $db->getTableList();
	$k = 0;
	foreach ($tables as $tn) {
		// make sure we get the right tables based on prefix
		if (!preg_match( "/^".$mainframe->getCfg('dbprefix')."/i", $tn )) {
			continue;
		}
		$fields = $db->getTableFields( array( $tn ) );

		$foundCO = false;
		$foundCOT = false;
		$foundE = false;

		$foundCO	= isset( $fields[$tn]['checked_out'] );
		$foundCOT	= isset( $fields[$tn]['checked_out_time'] );
		$foundE		= isset( $fields[$tn]['editor'] );

		if ($foundCO && $foundCOT) {
			if ($foundE) {
				$query = 'SELECT checked_out, editor FROM '.$tn.' WHERE checked_out > 0';
			} else {
				$query = 'SELECT checked_out FROM '.$tn.' WHERE checked_out > 0';
			}
			$db->setQuery( $query );
			$res = $db->query();
			$num = $db->getNumRows( $res );

			if ($foundE) {
				$query = 'UPDATE '.$tn.' SET checked_out = 0, checked_out_time = '.$db->Quote($nullDate).', editor = NULL WHERE checked_out > 0';
			} else {
				$query = 'UPDATE '.$tn.' SET checked_out = 0, checked_out_time = '.$db->Quote($nullDate).' WHERE checked_out > 0';
			}
			$db->setQuery( $query );
			$res = $db->query();

			if ($res == 1) {
				if ($num > 0) {
					echo "<tr class=\"row$k\">";
					echo "\n	<td width=\"350\">". JText::_( 'Checking table' ) ." - ". $tn ."</td>";
					echo "\n	<td width=\"150\">". JText::_( 'Checked-In' ) ." <b>". $num ."</b> ". JText::_( 'items' ) ."</td>";
					echo "\n	<td width=\"100\" align=\"center\"><img src=\"".JURI::root(true)."/media/system/images/tick.png\" border=\"0\" alt=\"". JText::_( 'tick' ) ."\" /></td>";
					echo "\n	<td>&nbsp;</td>";
					echo "\n</tr>";
				} else {
					echo "<tr class=\"row$k\">";
					echo "\n	<td width=\"350\">". JText::_( 'Checking table' ) ." - ". $tn ."</td>";
					echo "\n	<td width=\"150\">". JText::_( 'Checked-In' ) ." <b>". $num ."</b> ". JText::_( 'items' ) ."</td>";
					echo "\n	<td width=\"100\">&nbsp;</td>";
					echo "\n	<td>&nbsp;</td>";
					echo "\n</tr>";
				}
				$k = 1 - $k;
			}
		}
	}
	?>
	<tfoot>
		<tr>
			<td colspan="4">
				<strong>
				<?php echo JText::_( 'Checked out items have now been all checked in' ); ?>
				</strong>
			</td>
		</tr>
	</tfoot>
	</table>
</div>