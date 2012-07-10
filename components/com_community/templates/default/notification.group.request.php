<?php

/**
 * @package		JomSocial
 * @subpackage 	Template
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 *
 */

defined('_JEXEC') or die();

?>

<div>
	<?php if (count ($oRows) > 0 ) { ?>
		<div class="subject"><?php echo JText::_('COM_COMMUNITY_GROUPS_REQUEST_NOTIFICATION') . ':'; ?></div>

	<?php }//end if ?>
            
	<?php foreach ( $oRows as $row ) : ?>
            
	<div class="mini-profile" style="padding: 5px 5px 2px;" id="noti-request-group-<?php echo $row->id; ?>">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		    <tr>
		        <td width="50">
		            <a href="<?php echo $row->url; ?>">
						<img width="32" src="<?php echo $row->groupAvatar; ?>" class="cAvatar" alt="<?php echo $this->escape($row->name); ?>"/>
					</a>
				</td>
				<td valign="top">
					<div>
					    <span id="msg-request-<?php echo $row->id; ?>">

					    	<?php echo JText::sprintf('COM_COMMUNITY_GROUPS_REQUESTED_NOTIFICATION' , $row->name ,  $row->groupName); ?>

					    	<br />
						<span id="noti-answer-group-<?php echo $row->id; ?>">
						    <a class="jsIcon1 icon-add-friend" style="text-indent: 0; padding-left: 20px;" href="javascript:void(0);" onclick="joms.jQuery('#noti-answer-group-<?php echo $row->id; ?>').remove(); jax.call('community' , 'notification,ajaxGroupJoinRequest' , '<?php echo $row->id ?>' , '<?php echo $row->groupId; ?>');">

							    <?php echo JText::_('COM_COMMUNITY_PENDING_ACTION_APPROVE'); ?>

						    </a>
						    <a class="icon-remove" style="text-indent: 0;" href="javascript:void(0);" onclick="joms.jQuery('#noti-answer-group-<?php echo $row->id; ?>').remove(); jax.call('community','notification,ajaxGroupRejectRequest', '<?php echo $row->id ?>' , '<?php echo $row->groupId; ?>');">

							    <?php echo JText::_('COM_COMMUNITY_FRIENDS_PENDING_ACTION_REJECT'); ?>

						    </a>
							
							<a class="jsIcon1 icon-go" style="text-indent: 0;" href="<?php echo CUrlHelper::groupLink($row->groupId); ?>" >

							    <?php echo JText::_('COM_COMMUNITY_EVENTS_GO'); ?>

						    </a>
						</span>
					    </span>

						<span id="error-request-<?php echo $row->id; ?>">
					    </span>

					</div>
				</td>

			</tr>
		</table>
	</div>

	<?php endforeach; ?>
</div>