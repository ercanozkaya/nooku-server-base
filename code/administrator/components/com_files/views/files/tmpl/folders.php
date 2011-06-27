<?php
/**
 * @version     $Id: folders.php 1978 2011-06-24 22:58:54Z stiandidriksen $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Files
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<ul <?= isset($id) && $id === false ? '' : 'id="files-tree-html"'; ?>>
<? foreach($folders as $folder): ?>
	<li>
		<a href="#/<?= $folder->path; ?>" title="<?= $folder->path; ?>">
			<span class="icon"></span>
			<!--id:<?= $folder->path; ?>-->
			<?= $folder->name; ?>
		</a>
	<? if (count($folder->children)): ?>
		<?= @template('folders', array('folders' => $folder->children, 'id' => false)); ?>
	<? endif; ?>
	</li>
<? endforeach; ?>
</ul>