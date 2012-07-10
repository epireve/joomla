<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 **/
defined('_JEXEC') or die();
?>	
	<!-- begin: .profile-box -->
	<div class="profile-box">

                <div class="profile-avatar" onMouseOver="joms.jQuery('.rollover').toggle()" onmouseout="joms.jQuery('.rollover').toggle()">
                    <img src="<?php echo $profile->largeAvatar; ?>" alt="<?php echo $this->escape( $user->getDisplayName() ); ?>" />
                    <?php if( $isMine ): ?>
                    <div class="rollover"><a href="javascript:void(0)" onclick="joms.photos.uploadAvatar('profile','<?php echo $profile->id?>')"><?php echo JText::_('COM_COMMUNITY_CHANGE_AVATAR')?></a></div>
                    <?php endif; ?>
                </div>
		
		<div class="profile-likes">
			<div id="like-container"><?php echo $likesHTML; ?></div>
		</div>

		<!-- Short Profile info -->
		<div class="profile-info">
			<h2 class="contentheading">
				<?php echo $user->getDisplayName(); ?>
			</h2>

			<div id="profile-status">
				<span id="profile-status-message"><?php echo $profile->status; ?></span>
				<div class="small cMeta">- <?php echo $profile->posted_on; ?></div>
			</div>

			<ul class="profile-details cResetList">
				<?php if( $config->get('enablevideos') && ($profile->profilevideo != 0 ) ){ ?>
                <?php 	if( $config->get('enableprofilevideo') ){ ?>
				<li class="label"><?php echo JText::_('COM_COMMUNITY_VIDEOS_PROFILE_VIDEO'); ?></li>
				<li class="video"><a class="jsIcon1 icon-videos" onclick="joms.videos.playProfileVideo( <?php echo $profile->profilevideo; ?> , <?php echo $user->id; ?> )" href="javascript:void(0);"><?php echo ($profile->profilevideoTitle) ? $profile->profilevideoTitle  : JText::_('COM_COMMUNITY_VIDEOS_MY_PROFILE');?></a></li>
				<?php 	} ?>
                <?php } ?>
                
				<?php if($config->get('enablekarma')){ ?>
				<li class="label"><?php echo JText::_('COM_COMMUNITY_KARMA'); ?></li>
				<li><img src="<?php echo $karmaImgUrl; ?>" alt="" /></li>
				<?php } ?>
			
				<li class="label"><?php echo JText::_('COM_COMMUNITY_MEMBER_SINCE'); ?></li>
				<li><?php echo JHTML::_('date', $registerDate , JText::_('DATE_FORMAT_LC2')); ?></li>

				<li class="label"><?php echo JText::_('COM_COMMUNITY_LAST_LOGIN'); ?></li>
				<li><?php echo $lastLogin; ?></li>

				<li class="label"><?php echo JText::_('COM_COMMUNITY_PROFILE_VIEW'); ?></li>
				<li><?php echo JText::sprintf('COM_COMMUNITY_PROFILE_VIEW_RESULT', number_format($user->getViewCount()) ) ;?></li>
				
				<?php if( $multiprofile->name && $config->get('profile_multiprofile') ){ ?>
				<li class="label"><?php echo JText::_('COM_COMMUNITY_PROFILE_TYPE'); ?></li>
				<li><?php echo $multiprofile->name;?></li>
				<?php } ?>			    
			</ul>
		</div>
		
		<div style="clear: left;"></div>
	</div>
	<!-- end: .profile-box -->


	<?php if( !$isMine ): ?>
	<div class="profile-toolbox-bl">
	<div class="profile-toolbox-br">
		<ul class="cResetList small-button profile">
			<?php if(!$isFriend && !$isMine && !$isBlocked): ?>
		    <li class="btn-add-friend">
				<a href="javascript:void(0)" onclick="joms.friends.connect('<?php echo $profile->id;?>')"><span><?php echo JText::_('COM_COMMUNITY_PROFILE_ADD_AS_FRIEND'); ?></span></a>
			</li>
			<?php endif; ?>

			<?php if($config->get('enablephotos')): ?>
		    <li class="btn-gallery">
				<a href="<?php echo CRoute::_('index.php?option=com_community&view=photos&task=myphotos&userid='.$profile->id); ?>">
					<span><?php echo JText::_('COM_COMMUNITY_PHOTOS'); ?></span>
				</a>
			</li>
			<?php endif; ?>

			<?php if($showBlogLink): ?>
		    <li class="btn-blog">
				<a href="<?php echo JRoute::_('index.php?option=com_myblog&blogger=' . $user->getDisplayName() . '&Itemid=' . $blogItemId ); ?>">
					<span><?php echo JText::_('COM_COMMUNITY_BLOG'); ?></span>
				</a>
			</li>
			<?php endif; ?>
							
			<?php if($config->get('enablevideos')): ?>
		    <li class="btn-videos">
				<a href="<?php echo CRoute::_('index.php?option=com_community&view=videos&task=myvideos&userid='.$profile->id); ?>">
					<span><?php echo JText::_('COM_COMMUNITY_VIDEOS_GALLERY'); ?></span>
				</a>
			</li>
			<?php endif; ?>

			<?php if( !$isMine && $config->get('enablepm')): ?>
		    <li class="btn-write-message">
				<a onclick="<?php echo $sendMsg; ?>" href="javascript:void(0);">
					<span><?php echo JText::_('COM_COMMUNITY_INBOX_SEND_MESSAGE'); ?></span>
				</a>
			</li>
			<?php endif; ?>
		</ul>
	</div>
	</div>
	<?php endif; ?>

	<?php $userstatus->render(); ?>
