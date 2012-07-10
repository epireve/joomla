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
<script type="text/javascript">joms.filters.bind();</script>
<!-- begin: #cFrontpageWrapper -->
<div id="cFrontpageWrapper">
	<?php 
	/**
	 * if user logged in 
	 * 		load frontpage.members.php
	 * else 
	 * 		load frontpage.guest.php
	 */  
	echo $header;
	?>
	
	
	<!-- begin: .cLayout -->
	<div class="cLayout clrfix">
		<!-- begin: .cSidebar -->
		<div class="cSidebar clrfix">
			<?php $this->renderModules( 'js_side_top' ); ?>	    
			<?php if( $this->params->get('showsearch') == '1' || ($this->params->get('showsearch') == '2' && $my->id != 0 ) ) { ?>
			<?php
			/**
			 * ----------------------------------------------------------------------------------------------------------			
			 * Searchbox section here
			 * ----------------------------------------------------------------------------------------------------------			 
			 */			 			
			?>
			<!-- Search -->
			<?php if($this->params->get('showsearch')) { ?>
				<div class="cModule searchbox">
					<h3><span><?php echo JText::_('COM_COMMUNITY_SEARCH'); ?></span></h3>
					<form name="search" id="cFormSearch" method="get" action="<?php echo CRoute::_('index.php?option=com_community&view=search');?>">
						<fieldset class="fieldset">
							<div class="input_wrap clrfix">
								<a href="javascript:void(0);" onclick="joms.jQuery('#cFormSearch').submit();" class="search_button"><span><?php echo JText::_('COM_COMMUNITY_SEARCH_BUTTON_TEMP'); ?></span></a>
								<input type="text" class="inputbox" id="keyword" name="q" />
								<input type="hidden" name="option" value="com_community" />
								<input type="hidden" name="view" value="search" />
							</div>

							<div class="small">
								<?php echo JText::sprintf('COM_COMMUNITY_TRY_ADVANCED_SEARCH', CRoute::_('index.php?option=com_community&view=search&task=advancesearch') ); ?>
							</div>
						</fieldset>
					</form>
				</div>
			<?php } ?>
			<!-- Search -->
			<?php } ?>
			
            
			
			<?php
			/**
			 * ----------------------------------------------------------------------------------------------------------			
			 * Latest members section here
			 * ----------------------------------------------------------------------------------------------------------			 
			 */			 			
			?>
			<?php if($this->params->get('showlatestmembers') ){ ?>
				<?php if ( ($this->params->get( 'showlatestmembers' ) == '1' || ( $this->params->get('showlatestmembers') == '2' && $my->id != 0 )) && !empty($latestMembers) ) { ?>
					<div id="latest-members" class="cModule">
						<?php echo $latestMembers; ?>
					</div>
				<?php } ?>
			<?php } ?>

			<?php
			/**
			 * ----------------------------------------------------------------------------------------------------------			
			 * Latest groups section here
			 * ----------------------------------------------------------------------------------------------------------			 
			 */	
			?>
			<?php if($this->params->get('showlatestgroups')) { ?>
			<?php if($config->get('enablegroups') ) { ?>
			<?php if( !empty($latestGroups) && ( $this->params->get('showlatestgroups') == '1' || ($this->params->get('showlatestgroups') == '2' && $my->id != 0 ) ) ) { ?>
			<!-- Latest Groups -->
			<div class="cModule latest-groups">
				<?php echo $latestGroups; ?>
				<div class="app-box-footer">
					<a href="<?php echo CRoute::_('index.php?option=com_community&view=groups'); ?>"><?php echo JText::_('COM_COMMUNITY_GROUPS_VIEW_ALL'); ?></a>
				</div>
			</div>
			<!-- Latest Groups -->
			<?php } ?>
			<?php } ?>
			<?php } ?>


			
			<?php
			/**
			 * ----------------------------------------------------------------------------------------------------------			
			 * Latest events section here
			 * ----------------------------------------------------------------------------------------------------------			 
			 */			 			
			?>
			<?php if($this->params->get('frontpage_latest_events')) { ?>
					<?php if($config->get('enableevents') ) { ?>
					<?php if( !empty($latestEvents) && ( $this->params->get('frontpage_latest_events') == '1' || ($this->params->get('frontpage_latest_events') == '2' && $my->id != 0 ) ) ) { ?>
					<!-- Latest Events -->
					<div class="cModule latest-events"><?php echo $latestEvents; ?></div>
					<!-- Latest Events -->
					<?php } ?>
					<?php } ?>
			<?php } ?>
			
			<?php
			/**
			 * ----------------------------------------------------------------------------------------------------------			
			 * Latest photos section here
			 * ----------------------------------------------------------------------------------------------------------			 
			 */			 			
			?>
			<?php if($this->params->get('showlatestphotos')) { ?>
			<?php if($config->get('enablephotos')){ ?>
			<?php if( $this->params->get('showlatestphotos') == '1' || ($this->params->get('showlatestphotos') == '2' && $my->id != 0 ) ) { ?>
					<div class="cModule latest-photos">
						<?php echo $latestPhotosHTML; ?>
					</div>
			<?php } ?>
			<?php } ?>
			<?php } ?>


			<?php
			/**
			 * ----------------------------------------------------------------------------------------------------------			
			 * Latest videos section here
			 * ----------------------------------------------------------------------------------------------------------			 
			 */			 			
			?>
			<?php if($config->get('enablevideos')) { ?>
				<?php if($this->params->get('showlatestvideos') ){ ?>
					<?php if( $this->params->get('showlatestvideos') == '1' || ($this->params->get('showlatestvideos') == '2' && $my->id != 0 ) ) { ?>
						<div class="cModule latest-video">
							<!-- Latest Video -->
							<h3><span><?php echo JText::_('COM_COMMUNITY_VIDEOS'); ?></span></h3>

							<div class="app-box-content">
								<div id="latest-videos-nav" class="filterlink">
									<div>
										<a class="newest-videos active-state" href="javascript:void(0);"><?php echo JText::_('COM_COMMUNITY_VIDEOS_NEWEST') ?></a>
										<a class="featured-videos" href="javascript:void(0);"><?php echo JText::_('COM_COMMUNITY_FEATURED') ?></a>
										<a class="popular-videos" href="javascript:void(0);"><?php echo JText::_('COM_COMMUNITY_VIDEOS_POPULAR') ?></a>
									</div>
									<div class="loading"></div>
									<div class="clr"></div>
								</div>
								<div class="clr"></div>
								<div id="latest-videos-container" class="clrfix">
									<?php echo $latestVideosHTML;?>
								</div>
							</div>
						
							<div class="app-box-footer">
								<a href="<?php echo CRoute::_('index.php?option=com_community&view=videos'); ?>"><?php echo JText::_('COM_COMMUNITY_VIDEOS_ALL'); ?></a>
							</div>
							<!-- Latest Video -->
						</div>
					<?php } ?>
				<?php } ?>
			<?php } ?>


			<?php
			/**
			 * ----------------------------------------------------------------------------------------------------------			
			 * Whos online section here
			 * ----------------------------------------------------------------------------------------------------------			 
			 */			 			
			?>
			<?php if($this->params->get('showonline') && count( $onlineMembers )>0) { ?>
				<?php if( $this->params->get('showonline') == '1' || ($this->params->get('showonline') == '2' && $my->id != 0 ) ) { ?>
				<div class="cModule whos-online">
					<h3><span><?php echo JText::_('COM_COMMUNITY_FRONTPAGE_WHOSE_ONLINE'); ?></span></h3>
					<ul class="cResetList cThumbList clrfix">
						<?php for ( $i = 0; $i < count( $onlineMembers ); $i++ ) { ?>
							<?php $row =& $onlineMembers[$i]; ?>
							<li>
								<a href="<?php echo CRoute::_('index.php?option=com_community&view=profile&userid='.$row->id ); ?>"><img class="cAvatar jomNameTips cAvatar-sidebar" src="<?php echo $row->user->getThumbAvatar(); ?>" title="<?php echo cAvatarTooltip($row->user); ?>" alt="<?php echo $this->escape( $row->user->getDisplayName() );?>" /></a>
							</li>
						<?php } ?>
					</ul>
				</div>
				<?php } ?>
			<?php } ?>
			<?php $this->renderModules( 'js_side_bottom' ); ?>

		</div>
		<!-- end: .cSidebar -->


			<!-- begin: .cMain -->
			<div class="cMain clrfix">				
			<?php
			/**
			 * ----------------------------------------------------------------------------------------------------------			
			 * Activity stream section here
			 * ----------------------------------------------------------------------------------------------------------			 
			 */			 			
			?>
			<?php if( $config->get('showactivitystream') == '1' || ($config->get('showactivitystream') == '2' && $my->id != 0 ) ) { ?>
			<!-- Recent Activities -->
			<h2 class="componentheading"><?php echo JText::_('COM_COMMUNITY_FRONTPAGE_RECENT_ACTIVITIES'); ?></h2>
			<div class="app-box" id="recent-activities">
				<div class="app-box-content">

					<?php $userstatus->render(); ?>
					
					<?php if ( $alreadyLogin == 1 ) : ?>
						<div id="activity-stream-nav" class="filterlink">
							<div style="float: right;">
								<a class="all-activity<?php echo $config->get('frontpageactivitydefault') == 'all' ? ' active-state': '';?>" href="javascript:void(0);"><?php echo JText::_('COM_COMMUNITY_VIEW_ALL') ?></a>
								<a class="me-and-friends-activity<?php echo $config->get('frontpageactivitydefault') == 'friends' ? ' active-state': '';?>" href="javascript:void(0);"><?php echo JText::_('COM_COMMUNITY_ME_AND_FRIENDS') ?></a>
							</div>
							<div class="loading"></div>
							<div class="clr"></div>
						</div>
					<?php endif; ?>

					<div class="activity-stream-front">
					<div class="joms-latest-activities-container">
						<a id="activity-update-click" href="javascript:void(0);">1 new update </a>
					</div>
					<?php if($config->get('enable_refresh') == 1) : ?>
					<script type="text/javascript">
						joms.jQuery(document).ready(function(){
							joms.jQuery('#activity-update-click').click(function(){
								joms.jQuery('.joms-latest-activities-container').hide();
								joms.jQuery('.newly-added').show();
								joms.jQuery('.newly-added').removeClass('newly-added');
							});
							
							joms.activities.nextActivitiesCheck(<?php echo $config->get('stream_refresh_interval');?> );
						});
						
						function reloadActivities(){
							if(joms.jQuery('.cFeed-item').size() > 0){
							   joms.activities.getLatestContent(joms.jQuery('.cFeed-item').attr('id').substring(21),true); 
							}
						}
					</script>
					<?php endif ?>

						<div id="activity-stream-container">
							<?php echo $userActivities; ?>
						</div>
					</div>

				</div>
			</div>
			<!-- Recent Activities -->
			<?php } ?>

		</div>
		<!-- end: .cMain -->

	</div>
	<!-- end: .cLayout -->
	
</div>
<!-- begin: #cFrontpageWrapper -->