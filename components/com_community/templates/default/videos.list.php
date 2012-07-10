<?php
/**
 * @package        JomSocial
 * @subpackage     Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * 
 */
defined('_JEXEC') or die();
?>

<?php if ($videos) { ?>
<div class="video-items">
<?php
	$i = 0;
	foreach($videos as $video) {
	?>
	
<div class="video-item">
	<div class="video-item">
			<!---VIDEO THUMB-->
	    <div class="video-thumb" style="width: <?php echo $videoThumbWidth+22; ?>px; height:<?php echo $videoThumbHeight+22; ?>px;">   
	    	<a class="video-thumb-url" href="<?php echo $video->getURL(); ?>" style="width: <?php echo $videoThumbWidth+10; ?>px; height:<?php echo $videoThumbHeight+10; ?>px;">
					<img src="<?php echo $video->getThumbnail(); ?>" width="<?php echo $videoThumbWidth; ?>" height="<?php echo $videoThumbHeight; ?>" alt="" />
	        <?php if (!$video->isPending()): ?>   
	        	<span class="video-durationHMS"><?php echo $video->getDurationInHMS(); ?></span>
	        <?php endif; ?>
				</a>
				
				<?php if ( $isCommunityAdmin || ($video->isOwner() && !$groupVideo) || ($groupVideo && $allowManageVideos) ) { ?>
        <div class="album-actions small <?php if( !in_array($video->id, $featuredList) ) { ?>featured<?php } ?> ">
            <a class="album-action edit" title="<?php echo JText::_('COM_COMMUNITY_EDIT') ?>" href="javascript:void(0);" onclick="joms.videos.showEditWindow('<?php echo $video->getId(); ?>', '<?php echo $redirectUrl;?>');"><span><?php echo JText::_('COM_COMMUNITY_EDIT') ?></span></a>
            <a class="album-action delete" title="<?php echo JText::_('COM_COMMUNITY_DELETE') ?>" href="javascript:void(0);" onclick="joms.videos.deleteVideo('<?php echo $video->getId();?>','<?php echo $currentTask;?>');"><span><?php echo JText::_('COM_COMMUNITY_DELETE') ?></span></a>
						<?php
						if( $isCommunityAdmin && !$groupVideo )
						{
							if( !in_array($video->id, $featuredList) )
							{
						?>
				      <a class="album-action featured" id="featured-<?php echo $video->getId(); ?>" onclick="joms.featured.add('<?php echo $video->getId(); ?>', 'videos');" href="javascript:void(0);">	            	            
				      	<?php echo JText::_('COM_COMMUNITY_MAKE_FEATURED'); ?>
				      </a>
						<?php			
							}
						}
						?>
        </div>
        <?php } ?>
				
	    </div>
			<!---end: VIDEO THUMB-->
		
			<!---VIDEO SUMMARY-->
	    <div class="video-summary">
	        <div class="video-title">
	            <?php
	            if ($video->isPending()) {
	                echo $video->getTitle();
	            } else {
	            ?>
	                <a href="<?php echo $video->getURL(); ?>"><?php echo $video->getTitle(); ?></a>
	            <?php } ?>
	        </div>
        
	        <div class="video-details small">
	            <div class="video-hits">
								<?php if(CStringHelper::isPlural($video->getHits())) {
									echo JText::sprintf('COM_COMMUNITY_VIDEOS_HITS_COUNT', $video->getHits());
								} else {
									echo JText::sprintf('COM_COMMUNITY_VIDEOS_HITS_COUNT_SINGULAR', $video->getHits());
								} ?>
							</div>                    
	            <div class="video-lastupdated"><?php echo $video->getLastUpdated();?>
		    <?php
			if(isset($video->location) && $video->location != "")
			{
			    echo JText::sprintf('COM_COMMUNITY_EVENTS_TIME_SHORT', $video->location );
			}
		    ?>
		    <?php if ($currentTask != 'myvideos'):?> <?php echo JText::_('COM_COMMUNITY_BY') ?> <a href="<?php echo CRoute::_('index.php?option=com_community&view=profile&userid='.$video->creator); ?>"><?php echo $video->getCreatorName(); ?></a><?php endif;?></div>
	            <?php if ( (!$video->isOwner() && !$groupVideo) || ($groupVideo && !$allowManageVideos) ) { ?>
	            <?php } ?>
	        </div>
	    </div>
	    <!---end: VIDEO SUMMARY-->
	    <div class="clr"></div>
	</div>
	<!---end: VIDEO ITEM-->
	
</div>
<!---end: VIDEO ITEM-->

<?php
	$i++;
	if( $i % 4 == 0 ) {
		?>
		<div class="clr"></div>
		<?php
	}
?>

<?php } ?>
</div>
<!---end: VIDEO ITEM(S)-->
<div class="clr"></div>

<?php 
} else {
    $task	= JRequest::getVar('task');
	switch ($task)
	{
		case 'mypendingvideos':
			$msg	= JText::_('COM_COMMUNITY_VIDEOS_PENDING_VIDEOS');
			break;
		case 'search':
			$msg	= JText::_('COM_COMMUNITY_NO_RESULT');
			break;
		case 'myvideos':
			$isMine	= ($user->id==$my->id);
			$msg	= $isMine ? JText::_('COM_COMMUNITY_VIDEOS_NO_VIDEO') : JText::sprintf('COM_COMMUNITY_VIDEOS_NO_VIDEOS', $user->getDisplayName());
			break;
		default:
			$msg	= JText::_('COM_COMMUNITY_VIDEOS_NO_VIDEO');
			break;
	}
	?>
		<div class="video-not-found"><?php echo $msg; ?></div>
	<?php
}
?>

<?php if (!is_null($pagination)) {?>
<div class="pagination-container">
	<?php echo $pagination->getPagesLinks(); ?>
</div>
<?php }?>