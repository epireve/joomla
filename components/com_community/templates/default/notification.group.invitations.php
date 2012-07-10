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
	<?php if (count ($gRows) > 0 ) { ?>
		<div class="subject"><?php echo JText::_('COM_COMMUNITY_GROUPS_INVITATION_NOTIFICATION') . ':'; ?></div>

	<?php }//end if ?>

	<?php foreach ( $gRows as $row ) : ?>
            <?php //var_dump($row);?>
	<div class="mini-profile" style="padding: 5px 5px 2px;" id="noti-pending-group-<?php echo $row->groupid; ?>">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		    <tr>
		        <td width="50">
		            <a href="<?php echo $row->url; ?>">
						<img width="32" src="<?php echo $row->groupAvatar; ?>" class="cAvatar" alt="<?php echo $this->escape($row->name); ?>"/>
					</a>
				</td>
				<td valign="top">
					<div>
					    <span id="msg-pending-<?php echo $row->groupid; ?>">

					    	<?php echo JText::sprintf('COM_COMMUNITY_GROUPS_INVITED_NOTIFICATION' , $row->invitor->getDisplayName() ,  '<a style="text-decoration:none;" href="'.$row->url.'">'.$row->name.'</a>'); ?>

					    	<br />
						<span id="noti-answer-group-<?php echo $row->groupid; ?>">
						    <a class="jsIcon1 icon-add-friend" style="text-indent: 0; padding-left: 20px;" href="javascript:void(0);" onclick="joms.jQuery('#noti-answer-group-<?php echo $row->groupid; ?>').remove(); jax.call('community' , 'notification,ajaxGroupJoinInvitation' , '<?php echo $row->groupid ?>');">

							    <?php echo JText::_('COM_COMMUNITY_EVENTS_ACCEPT'); ?>

						    </a>
						    <a class="icon-remove" style="text-indent: 0;" href="javascript:void(0);" onclick="joms.jQuery('#noti-answer-group-<?php echo $row->groupid; ?>').remove(); jax.call('community','notification,ajaxGroupRejectInvitation', '<?php echo $row->groupid ?>');">

							    <?php echo JText::_('COM_COMMUNITY_EVENTS_REJECT'); ?>

						    </a>
							
						<a class="jsIcon1 icon-go" style="text-indent: 0;" href="<?php echo CUrlHelper::groupLink($row->groupid); ?>" >

							    <?php echo JText::_('COM_COMMUNITY_EVENTS_GO'); ?>

						    </a>
						</span>
					    </span>

						<span id="error-pending-<?php echo $row->groupid; ?>">
					    </span>

					</div>
				</td>

			</tr>
		</table>
	</div>

	<?php endforeach; ?>
</div>