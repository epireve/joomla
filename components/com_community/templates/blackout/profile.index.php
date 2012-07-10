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

<?php echo @$header; ?>

<script type="text/javascript"> joms.filters.bind();</script>
<?php echo $adminControlHTML; ?>


<!-- begin: #cProfileWrapper -->
<div id="cProfileWrapper">

	<!-- begin: .cLayout -->
	<div class="cLayout clrfix">
		<?php $this->renderModules( 'js_profile_top' ); ?>	

		<!-- begin: .cSidebar -->
	    <div class="cSidebar clrfix">
			<?php $this->renderModules( 'js_side_top' ); ?>
			<?php $this->renderModules( 'js_profile_side_top' ); ?>
			<?php echo $sidebarTop; ?>
			
			<?php echo $about; ?>
			<?php echo $this->view('profile')->modGetFriendsHTML(); ?>
			<?php if( $config->get('enablegroups')){ ?>
			<?php echo $this->view('profile')->modGetGroupsHTML(); ?>
			<?php } ?>
			<?php echo $sidebarBottom; ?>
			<?php $this->renderModules( 'js_profile_side_bottom' ); ?>
			<?php $this->renderModules( 'js_side_bottom' ); ?>		
	    </div>
	    <!-- end: .cSidebar -->
	    
        <!-- begin: .cMain -->
	    <div class="cMain">
	    
			<div class="page-actions">
				<?php echo $reportsHTML;?>
				<?php echo $bookmarksHTML;?>
				<?php echo $blockUserHTML;?>
				<div style="clear: right;"></div>
			</div>
			
			
			<?php $this->renderModules( 'js_profile_feed_top' ); ?>
				<!-- Recent Activities -->
				<div class="app-box" id="recent-activities">
			        <div class="app-box-header">
			        <div class="app-box-header">
			            <h2 class="app-box-title"><?php echo JText::_('COM_COMMUNITY_FRONTPAGE_RECENT_ACTIVITIES'); ?></h2>
			            <div class="app-box-menus">
			                <div class="app-box-menu toggle">
			                    <a class="app-box-menu-icon"
			                       href="javascript: void(0)"
			                       onclick="joms.apps.toggle('#recent-activities');"><span class="app-box-menu-title"><?php echo JText::_('COM_COMMUNITY_VIDEOS_EXPAND');?></span></a>
			                </div>
			            </div>
					</div>
			        </div>
			        
			        <div class="app-box-content">
						<div class="joms-latest-activities-container">
							<a id="activity-update-click" href="javascript:void(0);">1 new update </a>
						</div>
						
						<?php if($config->get('enable_refresh') == 1 && $isMine) : ?>
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
						<div id="activity-stream-nav" class="filterlink" style="top: -28px;">
							<div class="loading"></div>
						</div>
						
						<div style="position: relative;">
							<div id="activity-stream-container">
						  	<?php echo $newsfeed; ?>
						  	</div>
						</div>
					
					</div>
				</div>
				
				<?php $this->renderModules( 'js_profile_feed_bottom' ); ?>
				<?php echo $content; ?>
		</div>
	    <!-- end: .cMain -->
	    
		<?php $this->renderModules( 'js_profile_bottom' ); ?>	    
	</div>
	<!-- end: .cLayout -->

</div>
<!-- begin: #cProfileWrapper -->

<?php /* Insert plugin javascript at the bottom */ echo $jscript; ?>