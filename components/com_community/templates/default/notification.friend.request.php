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
	<?php if (count ($rows) > 0 ) { ?>
		<div class="subject"><?php echo JText::_( 'COM_COMMUNITY_NOTI_NEW_FRIEND_REQUEST' ) . ':'; ?></div>
	<?php }//end if ?>
	<?php foreach ( $rows as $row ) : ?>
	
	<div class="mini-profile" style="padding: 5px 5px 2px;" id="noti-pending-<?php echo $row->connection_id; ?>">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		    <tr>
		        <td width="50">
		            <a href="<?php echo $row->user->profileLink; ?>">
						<img width="32" src="<?php echo $row->user->getThumbAvatar(); ?>" class="cAvatar" alt="<?php echo $row->user->getDisplayName();?>"/>
					</a>
				</td>
	
				<td valign="top">
					<div>
					    <span id="msg-pending-<?php echo $row->connection_id; ?>">
					    	<?php echo JText::sprintf('COM_COMMUNITY_NOTI_ADD_YOU_AS_FRIEND' , $row->user->getDisplayName() ,  CRoute::_('index.php?option=com_community&view=friends&task=pending', false)); ?>
					    	<br />
						<span id="noti-answer-friend-<?php echo $row->connection_id; ?>">
						    <a class="jsIcon1 icon-add-friend" style="text-indent: 0; padding-left: 20px;" href="javascript:void(0);" onclick="joms.jQuery('#noti-answer-friend-<?php echo $row->connection_id; ?>').remove(); jax.call('community' , 'notification,ajaxApproveRequest' , '<?php echo $row->connection_id; ?>');">
							    <?php echo JText::_('COM_COMMUNITY_PENDING_ACTION_APPROVE'); ?>
						    </a>
						    <a class="icon-remove" style="text-indent: 0;" href="javascript:void(0);" onclick="joms.jQuery('#noti-answer-friend-<?php echo $row->connection_id; ?>').remove(); jax.call('community','notification,ajaxRejectRequest','<?php echo $row->connection_id; ?>');">
							    <?php echo JText::_('COM_COMMUNITY_REMOVE'); ?>
						    </a>
						</span>
					    </span>
					    <span id="error-pending-<?php echo $row->connection_id; ?>">
					    </span>
					</div>
				</td>
	
			</tr>
		</table>
	</div>
	    
	<?php endforeach; ?>
</div>