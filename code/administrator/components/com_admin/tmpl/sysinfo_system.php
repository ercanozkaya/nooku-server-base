<?php
/**
 * @version		$Id: sysinfo_system.php 11713 2009-03-27 09:18:26Z willebil $
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

?>
		<table>
		<thead>
			<tr>
				<th width="250">
					<?php echo JText::_( 'Setting' ); ?>
				</th>
				<th>
					<?php echo JText::_( 'Value' ); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
		<tr>
			<th colspan="2">&nbsp;
			</th>
		</tr>
		</tfoot>
		<tbody>
		<tr>
			<td valign="top">
				<strong><?php echo JText::_( 'PHP Built On' ); ?>:</strong>
			</td>
			<td>
				<?php echo php_uname(); ?>
			</td>
		</tr>
		<tr>
			<td>
				<strong><?php echo JText::_( 'Database Version' ); ?>:</strong>
			</td>
			<td>
				<?php echo $db->getVersion(); ?>
			</td>
		</tr>
		<tr>
			<td>
				<strong><?php echo JText::_( 'Database Collation' ); ?>:</strong>
			</td>
			<td>
				<?php echo $db->getCollation(); ?>
			</td>
		</tr>
		<tr>
			<td>
				<strong><?php echo JText::_( 'PHP Version' ); ?>:</strong>
			</td>
			<td>
				<?php echo phpversion(); ?>
			</td>
		</tr>
		<tr>
			<td>
				<strong><?php echo JText::_( 'Web Server' ); ?>:</strong>
			</td>
			<td>
				<?php echo HTML_admin_misc::get_server_software(); ?>
			</td>
		</tr>
		<tr>
			<td>
				<strong><?php echo JText::_( 'WebServer to PHP Interface' ); ?>:</strong>
			</td>
			<td>
				<?php echo php_sapi_name(); ?>
			</td>
		</tr>
		<tr>
			<td>
				<strong><?php echo JText::_( 'Joomla! Version' ); ?>:</strong>
			</td>
			<td>
				<?php
					$version = new JVersion();
					echo $version->getLongVersion();
				?>
			</td>
		</tr>
		<tr>
			<td>
				<strong><?php echo JText::_( 'User Agent' ); ?>:</strong>
			</td>
			<td>
				<?php echo htmlspecialchars(phpversion() <= "4.2.1" ? getenv( "HTTP_USER_AGENT" ) : $_SERVER['HTTP_USER_AGENT'], ENT_QUOTES);?>
			</td>
		</tr>
		</tbody>
		</table>