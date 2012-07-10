<?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

if( !$isCommunityAdmin || !$isDefaultPhoto )
{
?>
<div id="community-admin-wrapper">
	<ul id="community-admin-controls">
		<?php
		if( !$isCommunityAdmin )
		{
		?>
		<li>
			<?php
				if( !$blocked )
				{
			?>
					<a href="javascript:void(0);" onclick="joms.users.banUser('<?php echo $userid; ?>', '0' );"><span><?php echo JText::_('COM_COMMUNITY_BAN_USER');?></span></a>
			<?php
				}
				else
				{
			?>
				<a href="javascript:void(0);" onclick="joms.users.banUser('<?php echo $userid; ?>' , '1');"><span><?php echo JText::_('COM_COMMUNITY_UNBAN_USER');?></span></a>
			<?php
				}
			?>
		</li>
		<?php
		}
		?>
		
		<?php
			if( !$isFeatured )
			{
		?>
				<li>
					<a onclick="joms.featured.add('<?php echo $userid;?>','search');" href="javascript:void(0);"><span><?php echo JText::_('COM_COMMUNITY_MAKE_FEATURED'); ?></span></a>
				</li>
		<?php
			}
			else
			{
		?>
				<li>
					<a onclick="joms.featured.remove('<?php echo $userid;?>','search');" href="javascript:void(0);"><span><?php echo JText::_('COM_COMMUNITY_REMOVE_FEATURED'); ?></span></a>
				</li>
		<?php
			}
		?>
		

		<li>
			<a href="javascript:void(0);" onclick="joms.users.uploadNewPicture('<?php echo $userid;?>');"><span><?php echo JText::_('COM_COMMUNITY_PROFILE_AVATAR_EDIT');?></span></a>
		</li>
		<?php if( $jConfig->getValue('sef') ){ ?>
		<li>
			<a href="javascript:void(0);" onclick="joms.users.updateURL('<?php echo $userid;?>');"><span><?php echo JText::_('COM_COMMUNITY_PROFILE_CHANGE_ALIAS');?></span></a>
		</li>
		<?php } ?>
		<?php
		if( !$isDefaultPhoto )
		{
		?>
		<li>
			<a href="javascript:void(0);" onclick="joms.users.removePicture('<?php echo $userid;?>');"><span><?php echo JText::_('COM_COMMUNITY_REMOVE_PROFILE_PICTURE');?></span></a>
		</li>
		<?php
		}
		?>
		<?php
		if($videoid)
		{
		?>
		<li>
			<a href="javascript:void(0);" onclick="joms.videos.removeConfirmProfileVideo('<?php echo $userid;?>', '<?php echo $videoid;?>');"><span><?php echo JText::_('COM_COMMUNITY_VIDEOS_REMOVE_PROFILE_VIDEO');?></span></a>
		</li>
		<?php
		}
		?>
	</ul>
</div>
<?php
}
?>