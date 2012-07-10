<?php
/**
 * @package		JomSocial
 * @subpackage 	Template
 * @copyright (C) 2011 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 *
 */
defined('_JEXEC') or die();

/**
 * Require
 * @notification object
 *		- user CUser
 *		- title
 *		- created (optional)
 *		- action (optional)
 */
?>

<div class="jsNotification jsNotificationInbox">
	<?php if(!empty($notifications)) { ?>
	<ul class="cResetList jsNotificationList">
		<?php foreach ($notifications as $row) { ?>
			<li <?php if(!empty($row->rowid)){ echo 'id="'.$row->rowid.'"';} ?>>
				<!-- avatar -->
				<div class="jsNotificationIcon">
					<a href="<?php echo CUrlHelper::userLink($row->user->id); ?>"><img class="cAvatar" src="<?php echo $row->user->getThumbAvatar(); ?>"></a>
				</div>
				<!-- end avatar -->

				<!-- content -->
				<div class="jsNotificationContent<?php if(!is_null($row->action)){ echo ' jsNotificationHasActions'; } ?>">
					<div class="jsNotificationActor"><a href="<?php echo CUrlHelper::userLink($row->user->id); ?>"><?php echo $row->user->getDisplayName(); ?></a></div>
					<?php if (!is_null($row->link)) { ?><a href="<?php echo $row->link; ?>"> <?php } ?>
						<?php echo $row->title; ?>
					<?php if (!is_null($row->link)) { ?></a><?php } ?>
					
						
					<?php if (!is_null($row->created)) { ?>
						<br/><?php echo $row->created; ?>
					<?php } ?>
				</div>
				<!-- end content -->

				<!-- actions -->
				<?php if (!is_null($row->action)) { ?>
				<div class="jsNotificationActions">
					<?php echo $row->action; ?>
				</div>
				<?php } ?>
				<!-- end actions -->

				<div class="clr"></div>
			</li>
		<?php } ?>
	</ul>
	<?php } else { ?>
		<div class="jsNotificationContent jsNotificationEmpty">
			<div class="jsNotificationActor">
				<?php echo $empty_notice; ?>
			</div>
		</div>
	<?php } ?>
	
	<a href="<?php echo $link; ?>" class="jsNotificationLink"><?php echo $link_text; ?></a>
</div>

