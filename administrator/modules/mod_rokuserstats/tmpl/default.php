<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<div id="rok-stats">
	<ul>
		<?php foreach($rows as $row) : ?>
			<li class="<?php echo $row[0]; ?>"><span class="desc"><?php echo $row[1]; ?><span class="value"><?php echo $row[2]; ?></span></li>
		<?php endforeach; ?>
	<ul>
</div>
