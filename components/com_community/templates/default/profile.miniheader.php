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

<div class="profile-toolbox-bl" id="miniheader">
    <div class="profile-toolbox-br">
        <div class="profile-toolbox-tl">
            <div style="float: left; padding: 5px">
				<div style="float: left; width: 40px;">
				    <a href="<?php echo CRoute::_('index.php?option=com_community&view=profile&userid='.$user->id); ?>">
						<img width="32" src="<?php echo $user->getThumbAvatar(); ?>" alt="<?php echo $user->getDisplayName(); ?>" class="cAvatar" />
					</a>
				</div>
				<div style="margin: 0 0 0 50px;">
					<div><strong><span class="profile-toolbox-name"><?php echo $user->getDisplayName(); ?></span></strong></div>
					<a href="<?php echo CRoute::_('index.php?option=com_community&view=profile&userid='.$user->id); ?>">
						<?php echo JText::_('COM_COMMUNITY_GO_TO_PROFILE'); ?>
					</a>
				</div>
			</div>
			
			<div style="float: right;">
				<ul class="small-button" style="padding-top:5px;">
					<?php if(!$isFriend && !$isMine) { ?>
				    <li class="btn-add-friend">
						<a href="javascript:void(0)" onclick="joms.friends.connect('<?php echo $user->id;?>')"><span><?php echo JText::_('COM_COMMUNITY_PROFILE_ADD_AS_FRIEND'); ?></span></a>
					</li>
					<?php } ?>

					<?php if($config->get('enablephotos')): ?>
				    <li class="btn-gallery">
						<a href="<?php echo CRoute::_('index.php?option=com_community&view=photos&task=myphotos&userid='.$user->id); ?>">
							<span><?php echo JText::_('COM_COMMUNITY_PHOTOS'); ?></span>
						</a>
					</li>
					<?php endif; ?>

					<?php if($config->get('enablevideos')): ?>
				    <li class="btn-videos">
						<a href="<?php echo CRoute::_('index.php?option=com_community&view=videos&task=myvideos&userid='.$user->id); ?>">
							<span><?php echo JText::_('COM_COMMUNITY_VIDEOS_GALLERY'); ?></span>
						</a>
					</li>
					<?php endif; ?>

					<?php if( !$isMine && $config->get('enablepm') ): ?>
				    <li class="btn-write-message">
						<a onclick="<?php echo $sendMsg; ?>" href="javascript:void(0);">
							<span><?php echo JText::_('COM_COMMUNITY_INBOX_SEND_MESSAGE'); ?></span>
						</a>
					</li>
					<?php endif; ?>
				</ul>
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
</div>
