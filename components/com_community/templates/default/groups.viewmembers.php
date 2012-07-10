<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @params	isMine		boolean is this group belong to me
 * @params	members		An array of member objects 
 */
defined('_JEXEC') or die();
?>
<?php
if( $type == '1' && !( $isMine || $isAdmin || $isSuperAdmin ) )
{
?>
	<div>
		<?php echo JText::_('COM_COMMUNITY_PERMISSION_DENIED_WARNING'); ?>
	</div>
<?php
}
else
{
?>
	<?php if( $members ): ?>
		<div id="notice"></div>
	<?php
		foreach( $members as $member )
		{
			//$member->isAdmin
			if( $member->isBanned && !( $isMine || $isAdmin || $isSuperAdmin ) )
			{
				continue;
			}

	?>
	<div class="mini-profile" id="member_<?php echo $member->id;?>">
		<div class="mini-profile-avatar">
			<a href="<?php echo CRoute::_('index.php?option=com_community&view=profile&userid=' . $member->id); ?>"><img class="cAvatar cAvatar-Large" src="<?php echo $member->getThumbAvatar(); ?>" alt="<?php echo $member->getDisplayName(); ?>" /></a>
		</div>
		<div class="mini-profile-details">
			<h3 class="name">
				<a href="<?php echo CRoute::_('index.php?option=com_community&view=profile&userid=' . $member->id); ?>"><strong><?php echo $member->getDisplayName(); ?></strong></a>
			</h3>
			
			<div class="mini-profile-details-status" style="padding-bottom:30px"><?php echo $member->getStatus() ;?></div>
			
			<div class="mini-profile-details-action jsAbs jsFriendAction">
				<span class="jsIcon1 icon-group">
		    		<a href="<?php echo CRoute::_('index.php?option=com_community&view=friends&userid=' . $member->id );?>"><?php echo JText::sprintf( (CStringHelper::isPlural($member->friendsCount)) ? 'COM_COMMUNITY_FRIENDS_COUNT_MANY' : 'COM_COMMUNITY_FRIENDS_COUNT' , $member->friendsCount);?></a>
		    	</span>

				<?php if( $my->id != $member->id && $config->get('enablepm') ): ?>
		        <span class="jsIcon1 icon-write">
		            <a onclick="joms.messaging.loadComposeWindow(<?php echo $member->id; ?>)" href="javascript:void(0);">
		            <?php echo JText::_('COM_COMMUNITY_INBOX_SEND_MESSAGE'); ?>
		            </a>
		        </span>
		        <?php endif; ?>
		        
			    <?php if( !$member->approved && ($isMine || $isAdmin || $isSuperAdmin ) ): ?>
			    <span class="jsIcon1 icon-approve" id="groups-approve-<?php echo $member->id;?>">
			    	<a href="javascript:void(0);" onclick="jax.call('community','groups,ajaxApproveMember', '<?php echo $member->id;?>' , '<?php echo $groupid;?>');">
						<?php echo JText::_('COM_COMMUNITY_PENDING_ACTION_APPROVE'); ?>
					</a>
			    </span>
			    <?php endif; ?>
			    
			    <?php
			    	if( ( $isAdmin || $isSuperAdmin ) && !$member->isAdmin )
					//if( ($isMine && !$member->isMe && !$member->isAdmin && $member->approved && !$member->isBanned) || ( $isMember && !$member->isAdmin && $member->approved && !$member->isBanned) || ($isSuperAdmin && !$member->isAdmin) )
					{
				?>
				    <span class="jsIcon1 icon-user">
				    	<a href="javascript:void(0);" onclick="jax.call('community','groups,ajaxAddAdmin','<?php echo $member->id;?>','<?php echo $groupid;?>');">
							<?php echo JText::_('COM_COMMUNITY_GROUPS_ADMIN'); ?>
						</a>
				    </span> 
			    <?php
			    	}
			    	else if( ($isMine && !$member->isMe && $member->isAdmin) || (!$member->isOwner && $member->isAdmin && $isMember) || ($isSuperAdmin && $member->isAdmin) )
			    	{
			    ?>
				    <span class="jsIcon1 icon-user">
				    	<a href="javascript:void(0);" onclick="jax.call('community','groups,ajaxRemoveAdmin','<?php echo $member->id;?>','<?php echo $groupid;?>');">
							<?php echo JText::_('COM_COMMUNITY_GROUPS_REVERT_ADMIN'); ?>
						</a>
				    </span> 
			    <?php
					}
				?>

			    <?php if( $member->id != $group->ownerid && !$group->isAdmin( $member->id ) && $my->id != $member->id && !COwnerHelper::isCommunityAdmin($member->id) ){ ?>
			    <?php if( !$member->isBanned && ( $isAdmin || $isSuperAdmin ) ){ ?>
			    <span class="icon-ban" id="groups-ban-<?php echo $member->id;?>">
				    <a href="javascript:void(0);" onclick="jax.call('community','groups,ajaxBanMember', '<?php echo $member->id;?>' , '<?php echo $groupid;?>');">
					    <?php echo JText::_('COM_COMMUNITY_GROUPS_BAN_MEMBER'); ?>
				    </a>
			    </span>
			    <?php }else if( $member->isBanned == COMMUNITY_GROUP_BANNED && ( $isAdmin || $isSuperAdmin ) ){ ?>
			    <span class="icon-unban" id="groups-ban-<?php echo $member->id;?>">
				    <a href="javascript:void(0);" onclick="jax.call('community','groups,ajaxUnbanMember', '<?php echo $member->id;?>' , '<?php echo $groupid;?>');">
					    <?php echo JText::_('COM_COMMUNITY_GROUPS_MEMBER_UNBAN'); ?>
				    </a>
			    </span>
			    <?php } ?>
			    <?php } ?>
			    
			</div>
			
			<?php if($member->isOnline()): ?>
			<span class="icon-online-overlay">
		    	<?php echo JText::_('COM_COMMUNITY_ONLINE'); ?>
		    </span>
		    <?php endif; ?>			
			
		</div>
		
		<div class="jsAbs jsFriendRespond">
			<?php if( ($isMine || $isAdmin || $isSuperAdmin) && $my->id != $member->id ): ?>
		    <input type="submit" class="button" style="margin:0" onclick="joms.groups.confirmMemberRemoval(<?php echo $member->id; ?>, <?php echo $groupid;?>);" value="<?php echo JText::_('COM_COMMUNITY_GROUPS_REMOVE_MEMBER_MESSAGE');?>" />
			<?php endif; ?>
		</div>

		<div class="clr"></div>
	</div>
	<?php
		}
	?>
	<div class="pagination-container">
		<?php echo $pagination->getPagesLinks(); ?>
	</div>
	<?php endif; ?>
<?php
}
?>