<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	my	Current browser's CUser object.
 **/
defined('_JEXEC') or die();
?>

<div class="video-full">
	<div class="video-head">
		<div class="ctitle">
			<h2><?php echo JText::_('COM_COMMUNITY_VIDEOS_CURRENT_PROFILE_VIDEO_HEADING');?></h2>
			<?php if(!empty($video->id)){ ?>
			<span class="video-remove">
			<a onclick="joms.videos.removeConfirmProfileVideo(<?php echo $video->creator; ?>, <?php echo $video->getId(); ?>);" href="javascript:void(0);" class="icon-videos-remove"><?php echo JText::_('COM_COMMUNITY_VIDEOS_REMOVE_PROFILE_VIDEO'); ?></a>
			</span>
			<?php } ?>
		</div>
	</div>
	
	<div class="cRow clrfix">               
	<?php if(!empty($video->id)){ ?>
		<div class="video-player">
			<?php echo $video->getPlayerHTML(); ?>
			<div class="clr"></div>
		</div>
		<div class="ctitle"><h2><?php echo JText::_('COM_COMMUNITY_VIDEOS_PROFILE_VIDEO_DESCRIPTION'); ?></h2></div>
		<p><?php echo $this->escape($video->getDescription()); ?></p>

	<?php } else { ?>
		<div style="text-align: center;"><img src="<?php echo JURI::root(); ?>/components/com_community/assets/video_thumb.png" alt="<?php echo JText::_('COM_COMMUNITY_VIDEOS_PROFILE_VIDEO_NOT_EXIST'); ?>" class="jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_VIDEOS_PROFILE_VIDEO_NOT_EXIST'); ?>" /></div>
		<p align="center"><?php echo JText::_('COM_COMMUNITY_VIDEOS_NO_USER_PROFILE_VIDEO'); ?></p>
	<?php } ?>
	</div>


</div>

<?php

echo $sortings;

if ($videos) { ?>
	<div class="video-index">
		<div class="video-items">
			<?php
			$x = 1;
			foreach($videos as $vid) { 
				$v = JTable::getInstance( 'Video' , 'CTable' );
				$v->load($vid->id);
				$v->_wallcount = $vid->wallcount;
			?>

			<div class="video-item" id="<?php echo "video-" . $v->getId() ?>">
				<div class="video-item">

					<div class="video-thumb" style="width: <?php echo $videoThumbWidth+22; ?>px; height:<?php echo $videoThumbHeight+22; ?>px;">
						<?php if ($v->status=='pending'): ?>
							<img src="<?php echo JURI::root(); ?>/components/com_community/assets/video_thumb.png" width="<?php echo $videoThumbWidth; ?>" height="<?php echo $videoThumbHeight; ?>" alt="" />
						<?php else: ?>            
							<a class="video-thumb-url" href="<?php echo $v->getURL(); ?>" style="width: <?php echo $videoThumbWidth; ?>px; height:<?php echo $videoThumbHeight; ?>px;">
								<img src="<?php echo $v->getThumbnail(); ?>" width="<?php echo $videoThumbWidth; ?>" height="<?php echo $videoThumbHeight; ?>" alt="" />
								<span class="video-durationHMS"><?php echo $v->getDurationInHMS(); ?></span>
							</a>
						<?php endif; ?>
						
						<div class="album-actions small">
							<a class="album-action linkprofile" href="javascript:void(0);" onclick="joms.videos.linkConfirmProfileVideo('<?php echo $v->getId(); ?>', '<?php echo $redirectUrl;?>');" title="<?php echo JText::_('COM_COMMUNITY_VIDEOS_PROFILE_VIDEO_LINK') ?>"><?php echo JText::_('COM_COMMUNITY_VIDEOS_PROFILE_VIDEO_LINK') ?></a>
						</div><!-- end .album-actions -->
						
					</div> <!-- end .video-thumb -->

					

					<div class="video-summary">
						<div class="video-title">
							<?php
								if ($v->status=='pending') {
									echo $v->getTitle();
								} else {
								?>
								<a href="<?php echo $v->getURL(); ?>"><?php echo $v->getTitle(); ?></a>
							<?php } ?>
						</div><!-- end .video-title-->

						<div class="video-details small">
							<div class="video-hits"><?php echo JText::sprintf('COM_COMMUNITY_VIDEOS_HITS_COUNT', $v->getHits()) ?></div>                    
							<div class="video-lastupdated"><?php echo JText::sprintf('COM_COMMUNITY_VIDEOS_LAST_UPDATED', $v->getLastUpdated());?></div>
						</div><!-- end .video-details -->

						<div class="clr"></div>
					</div><!-- end .video summary -->
			
				</div><!-- end inner .video-item -->
			</div><!-- end outer .video-item -->
				<?php   
				if ($x % 4 == 0) {
				?>
					<div class="clr"></div>
				<?php
				}
				$x++;
				?>
		
			<?php 
			} // end foreach
			?>
		</div><!-- end .video-items -->
	</div><!-- end .video-index-->
	<div class="clr"></div>

<?php 
}
else 
{
	$isMine	= ( isset($video) && $video->creator==$my->id);
	$msg	= $isMine ? JText::_('COM_COMMUNITY_VIDEOS_NO_VIDEO') : JText::sprintf('COM_COMMUNITY_VIDEOS_NO_VIDEOS', $my->getDisplayName());
	?>
		<div><?php echo $msg; ?></div>
	<?php
}
?>

<div class="pagination-container">
	<?php echo $pagination->getPagesLinks(); ?>
</div>
