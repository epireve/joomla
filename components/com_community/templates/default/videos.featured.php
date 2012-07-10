<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * 
 */
defined('_JEXEC') or die();
?>

<?php
if ($videos)
{
?>
<div class="ctitle featuredTitle"><?php echo JText::_('COM_COMMUNITY_VIDEOS_FEATURED_TITLE');?></div>
		<div id="cFeatured" class="listBy4 clrfix">
		<?php
		$x = 0;
		foreach($videos as $video)
		{
		?>
		<!--div class="jomTips tipFullWidth" id="<?php echo "video-" . $video->getId() ?>" title="<?php echo $this->escape($video->title) . '::' . $this->escape($video->description); ?>"-->
		<div class="cFeaturedItem video-featured">
			<div class="cBoxPad cBoxBorder cBoxBorderLow">
				<div class="cFeaturedImgWrap" style="width: <?php echo $videoThumbWidth + 22; ?>px; height:<?php echo $videoThumbHeight+22; ?>px;">
					<a class="cFeaturedImg video-thumb cFeaturedImgBorder" href="<?php echo $video->getURL(); ?>" style="width: <?php echo $videoThumbWidth+10; ?>px; height:<?php echo $videoThumbHeight+10; ?>px;">
						<img src="<?php echo $video->getThumbnail(); ?>" alt="<?php echo $this->escape($video->title);?>" style="width: <?php echo $videoThumbWidth; ?>px; height:<?php echo $videoThumbHeight; ?>px;"/>
						<span class="video-durationHMS"><?php echo $video->getDurationInHMS(); ?></span>
						<span class="cFeaturedOverlay">star</span>
					</a>
					<?php
					if( $isCommunityAdmin )
					{
					?>
					<div class="album-actions small">
	            <a class="album-action remove-featured" title="<?php echo JText::_('COM_COMMUNITY_REMOVE_FEATURED'); ?>" onclick="joms.featured.remove('<?php echo $video->getId();?>','videos');" href="javascript:void(0);">	            	            
	            <?php echo JText::_('COM_COMMUNITY_REMOVE_FEATURED'); ?>
	            </a>
	        </div>
			    <?php
	        }
	        ?>
				</div>
        	
					<div class="cFeaturedTitle">
            <?php
            if ($video->isPending()) {
                echo $video->getTitle();
            } else {
            ?>
                <a href="<?php echo $video->getURL(); ?>"><?php echo $video->getTitle(); ?></a>
            <?php } ?>
        	</div>
        	
					<div class="video-details small">
            <div class="video-hits"><?php echo JText::sprintf('COM_COMMUNITY_VIDEOS_HITS_COUNT', $video->getHits()) ?></div>                    
            <div class="video-lastupdated"><?php echo $video->getLastUpdated();?> <?php echo JText::_('COM_COMMUNITY_BY') ?> <a href="<?php echo CRoute::_('index.php?option=com_community&view=profile&userid='.$video->creator); ?>"><?php echo $video->getCreatorName(); ?></a></div>
        	</div>
	            
        
		    <br class="clr" />
      </div>
		</div>
		<?php
			$x++;
	    if( $x % 4 == 0 )
			{
		?>
		<div class="clr"></div>
		<?php
			}
		?>
		<?php
		} // end foreach
		?>
		
		</div> <!-- END cFeatured -->

		<div class="clr"></div>
<?php
}