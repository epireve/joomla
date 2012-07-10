<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<div id="rok-audit">
	<input type="hidden" id="rok-audit-count" value="<?php echo $count; ?>" />
	<ul>
		<?php foreach($rows as $row) : ?>
			<li>
				<span class="rok-audit-user">
					<?php echo $row->name;?>
					<?php 
						if ($params->get('enableGravatar',1)) {
							echo rokAdminAuditHelper::getGravatar($row->email,20,'mm','g',true);
						} 
					?>
				</span>
				<span class="rok-audit-date">
					<?php echo $row->timestamp; ?>
				</span>
				<span class="rok-audit-details">
					<?php echo rokAdminAuditHelper::getDescription($row); ?>
				</span>
			</li>
		<?php endforeach; ?>
	</ul>
	<div class="rok-more">
		<span class="loader"></span>
		<div class="mc-button">
			<span class="button"><a href="#"><?php echo JTEXT::_('MC_RAA_LOAD_MORE') ?></a></span>
		</div>
		<div class="rok-audit-filter">
			<span><?php echo JTEXT::_('MC_RAA_DETAILS') ?></span>
			<select id="rok-audit-details" autocomplete="off">
				<option value="low" <?php echo rokAdminAuditHelper::isDetail('low', $params); ?>><?php echo JTEXT::_('MC_RAA_LOW') ?></option>
				<option value="medium" <?php echo rokAdminAuditHelper::isDetail('medium', $params); ?>><?php echo JTEXT::_('MC_RAA_MEDIUM') ?></option>
				<option value="high" <?php echo rokAdminAuditHelper::isDetail('high', $params); ?>><?php echo JTEXT::_('MC_RAA_HIGH') ?></option>
			</select>
		</div>
	</div>
</div>
