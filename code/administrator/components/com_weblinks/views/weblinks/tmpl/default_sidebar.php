<?
/**
 * @version		$Id: default_sidebar.php 1794 2011-06-19 16:39:46Z tomjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Weblinks
 * @copyright	Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://www.nooku.org
 */

defined('KOOWA') or die('Restricted access'); ?>

<div id="sidebar">
	<h3><?= @text('Categories') ?></h3>
	<?= @template('admin::com.categories.view.categories.list', array('categories' => KFactory::tmp('admin::com.categories.model.categories')->section('com_weblinks')->sort('title')->getList())); ?>
</div>