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
            <div class="goLft">
				<div class="profile-toolbox-thumb">
				    <a href="<?php echo CRoute::_('index.php?option=com_community&view=groups&task=viewgroup&groupid='.$group->id); ?>">
						<img src="<?php echo $group->getThumbAvatar(); ?>" alt="<?php echo $this->escape($group->name); ?>" class="cAvatar cAvatar-Small" />
					</a>
				</div>
				<div class="profile-toolbox-meta">
					<span class="profile-toolbox-name">
						<b><?php echo $this->escape($group->name); ?></b>
					</span>
					<br>
					<a href="<?php echo CRoute::_('index.php?option=com_community&view=groups&task=viewgroup&groupid='.$group->id); ?>" class="small">
						<?php echo JText::_('COM_COMMUNITY_GROUPS_BACK_BUTTON'); ?>
					</a>
				</div>
			</div>
			
			<div class="goRgt">
				<ul class="small-button cResetList clrfix">

					<?php if($config->get('group_events')): ?>
				    <li class="btn-events">
						<a href="<?php echo CRoute::_('index.php?option=com_community&view=events&groupid='.$group->id); ?>">
							<span><?php echo JText::_('COM_COMMUNITY_EVENTS'); ?></span>
						</a>
					</li>
					<?php endif; ?>
					
					<?php if($config->get('groupphotos')): ?>
				    <li class="btn-gallery">
						<a href="<?php echo CRoute::_('index.php?option=com_community&view=photos&groupid='.$group->id); ?>">
							<span><?php echo JText::_('COM_COMMUNITY_PHOTOS'); ?></span>
						</a>
					</li>
					<?php endif; ?>

					<?php if($config->get('groupvideos')): ?>
				    <li class="btn-videos">
						<a href="<?php echo CRoute::_('index.php?option=com_community&view=videos&groupid='.$group->id); ?>">
							<span><?php echo JText::_('COM_COMMUNITY_VIDEOS_GALLERY'); ?></span>
						</a>
					</li>
					<?php endif; ?>

					<?php if($config->get('creatediscussion')): ?>
				    <li class="btn-discussions">
						<a href="<?php echo CRoute::_('index.php?option=com_community&view=groups&task=viewdiscussions&groupid='.$group->id); ?>">
							<span><?php echo JText::_('COM_COMMUNITY_GROUPS_DISCUSSION'); ?></span>
						</a>
					</li>
					<?php endif; ?>

				    <li class="btn-members">
						<a href="<?php echo CRoute::_('index.php?option=com_community&view=groups&task=viewmembers&groupid='.$group->id); ?>">
							<span><?php echo JText::_('COM_COMMUNITY_GROUPS_MEMBERS'); ?></span>
						</a>
					</li>

				</ul>
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
</div>
