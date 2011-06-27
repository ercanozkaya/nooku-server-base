<?
/**
 * @version		$Id: form.php 1986 2011-06-26 16:25:30Z johanjanssens $
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Weblinks
 * @copyright	Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://www.nooku.org
 */

defined('KOOWA') or die('Restricted access'); ?>

<? @helper('behavior.tooltip'); ?>
<?= @helper('behavior.validator'); ?>

<script src="media://lib_koowa/js/koowa.js" />
<style src="media://lib_koowa/css/koowa.css" />

<form action="<?= @route('id='.$weblink->id) ?>" method="post" class="-koowa-form">
<input type="hidden" name="id" value="<?= $weblink->id ?>" />

<div class="grid_8">
	<div class="panel title group">
		<input class="inputbox required" type="text" name="title" id="title" size="40" maxlength="255" value="<?= $weblink->title; ?>" placeholder="<?= @text( 'Title' ); ?>" />
        <label for="alias">
            <?= @text( 'Alias' ); ?>:
            <input class="inputbox" type="text" name="alias" id="alias" size="40" maxlength="255" value="<?= $weblink->slug; ?>" />
        </label>
    </div>
	<div class="panel">
	    <h3><?= @text( 'Details' ); ?></h3>
		<table class="admintable">
		<tr>
			<td class="key">
				<label for="url">
					<?= @text( 'URL' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area required validate-url" type="text" name="url" id="url" value="<?= $weblink->url; ?>" size="70" maxlength="250" />
			</td>
		</tr>
		<tr>
			<td valign="top" class="key">
				<label for="description">
					<?= @text( 'Description' ); ?>:
				</label>
			</td>
			<td>
				<textarea class="text_area" cols="40" rows="9" name="description" id="description"><?= $weblink->description; ?></textarea>
			</td>
		</tr>
		</table>
	</div>
</div>

<div class="grid_4">
    <div class="panel">
        <h3><?= @text( 'Publish' ); ?></h3>
        <table class="admintable">
		<tr>
			<td class="key">
			    <label for="enabled">
			        <?= @text( 'Published' ) ?>:
			    </label>
			</td>
			<td>
				<?= @helper('select.booleanlist', array('name' => 'enabled', 'selected' => $weblink->enabled)) ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="catid">
					<?= @text( 'Category' ); ?>:
				</label>
			</td>
			<td>
				<?= @helper('listbox.category', array('name' => 'catid', 'selected' => $weblink->catid, 'attribs' => array('id' => 'catid', 'class' => 'required'))) ?>
			</td>
		</tr>
		</table>
	</div>
</div>

</form>