<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<div id="rok-stats">
	<ul>
		<?php foreach($rows as $row) : ?>
			<?php if ($row[0]=='full') : ?>
			<?php if (!empty($row[1])) : ?>
			<li class="full"><?php echo implode(", ",$row[1]); ?></li>
			<?php endif; ?>
			<?php else : ?>
			<li class="<?php echo $row[0]; ?>"><span class="desc"><?php echo $row[1]; ?><span class="value"><?php echo $row[2]; ?></span></li>
			<?php endif; ?>
		<?php endforeach; ?>
	<ul>
</div>
