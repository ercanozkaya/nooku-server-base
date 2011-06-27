<? /** $Id: list.php 670 2010-10-27 01:09:24Z johanjanssens $ */ ?>
<? defined('KOOWA') or die('Restricted access'); ?>

<table id="revisions-list" class="adminlist">
	<thead>
      	<tr>
          	<th width="80"><?= @text('Revision') ?></th>
           	<th><?= @text('Last Edited') ?></th>
      	</tr>
   	</thead>

  	<tbody>
  	<? foreach ($revisions as $revision): ?>
      	<tr>
          	<td><?= @text('Revision') ?> <?= $revision->revision ?></td>
           	<td><?= $revision->created_on ?> <?= @text('by') ?> <?= $revision->user_name ?></td>
        </tr>
    <? endforeach; ?>
  	</tbody>
</table>