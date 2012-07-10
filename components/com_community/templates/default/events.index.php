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
<div id="community-events-wrap">
    
	<?php
        
	if( $featuredList )
	{
	?>
	<div class="cRow">
	<div class="ctitle"><?php echo JText::_('COM_COMMUNITY_FEATURED_EVENTS');?></div>
		<div id="cFeatured" class="forGroup">
			<?php
				foreach($featuredList as $event)
				{
			?>
			<div class="cFeaturedItem">
				<div class="cBoxPad clrfix">
					<a href="<?php echo CRoute::_('index.php?option=com_community&view=events&task=viewevent&eventid=' . $event->id );?>" class="cFeaturedThumb jomNameTips" title="<?php echo $this->escape($event->title);?>">
						<img src="<?php echo $event->getThumbAvatar();?>" alt="<?php echo $this->escape($event->title);?>" />
						<span class="cFeaturedOverlay">star</span>
					</a>
					<?php
					if( $isCommunityAdmin )
					{
					?>
					<div class="album-actions small" style="display: none;">	        
						<a onclick="joms.featured.remove('<?php echo $event->id;?>','events');" href="javascript:void(0);" title="<?php echo JText::_('COM_COMMUNITY_REMOVE_FEATURED'); ?>" class="album-action remove-featured"><?php echo JText::_('COM_COMMUNITY_REMOVE_FEATURED'); ?></a>
					</div>
					<?php
					}
					?>
				</div>
			</div>
			<?php
				}
			?>
		</div>
		<div class="clr"></div>
	</div>
	<?php
	}
	?>
	
	<?php echo $sortings; ?>
    
        <div class="cLayout clrfix">
			<!-- START Sidebar -->
            <div class="cSidebar clrfix">
					<!-- START nearby event search -->
					<?php echo $this->view('events')->modEventNearby(); ?>
					<!-- END nearby event search -->
					
					<!-- Categories -->
					<?php if ( $index && $handler->showCategories() ) : ?>
					<div class="cModule clrfix">
						<h3><?php echo JText::_('COM_COMMUNITY_CATEGORIES');?></h3>
						<ul class="cResetList cCategories">
							<li>
								<?php if( $category->parent == COMMUNITY_NO_PARENT && $category->id == COMMUNITY_NO_PARENT ){ ?>
									<a href="<?php echo CRoute::_('index.php?option=com_community&view=events');?>"><?php echo JText::_( 'COM_COMMUNITY_EVENTS_ALL' ); ?> </a>
								<?php }else{ ?>
									<a href="<?php echo CRoute::_('index.php?option=com_community&view=events&task=display&categoryid=' . $category->parent ); ?>"><?php echo JText::_('COM_COMMUNITY_BACK_TO_PARENT'); ?></a>
								<?php }  ?>
							</li>
							<?php if( $categories ): ?>
								<?php foreach( $categories as $row ): ?>
								<li>
									<a href="<?php echo CRoute::_('index.php?option=com_community&view=events&task=display&categoryid=' . $row->id ); ?>"><?php echo JText::_( $this->escape($row->name) ); ?><span class="cCount"><?php echo $row->count; ?></span></a> <?php if( $row->count > 0 ){ ?><?php } ?>
								</li>
								<?php endforeach; ?>
							<?php else: ?>
								<li><?php echo JText::_('COM_COMMUNITY_GROUPS_CATEGORY_NOITEM'); ?></li>
							<?php endif; ?>
						</ul>
						<div class="clr"></div>
					</div>
					<?php endif; ?>
					
					<!-- START event calendar -->
					<?php echo $this->view('events')->modEventCalendar(); ?>
					<!-- END event calendar -->
            </div>
			<!-- END Sidebar -->
			
            
			<!-- START event list -->
            <div id="community-events-results-wrapper" class="cMain jsApLf mvLf jsItms">
                    <?php echo $eventsHTML;?>
            </div>
			<!-- END event list -->
			
			<script type="text/javascript">
                    joms.jQuery(document).ready(function(){
                            // Get the Current Location from cookie
                            var location =	joms.geolocation.getCookie( 'currentLocation' );

                            if( location.length != 0 )
                            {
                                    joms.jQuery('#showNearByEventsLoading').show();
                                    joms.geolocation.showNearByEvents( location );
                            }

                            // Check if the browsers support W3C Geolocation API
                            // If yes, show the auto-detect link
                            if( navigator.geolocation )
                            {
                                joms.jQuery('#autodetectLocation').show();
                            }
                    });
            </script>
        </div>
    
</div>