<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 **/
defined('_JEXEC') OR DIE();
?>

<script type="text/javascript" src="<?php echo JURI::root();?>components/com_community/assets/ajaxfileupload.pack.js"></script>
<script type="text/javascript" src="<?php echo JURI::root();?>components/com_community/assets/imgareaselect/scripts/jquery.imgareaselect.pack.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo JURI::root();?>components/com_community/assets/imgareaselect/css/imgareaselect-default.css" />

<script type="text/javascript"> joms.filters.bind();</script>

<!-- begin: #cProfileWrapper -->
<div id="cProfileWrapper">
	<?php echo $adminControlHTML; ?>
	<!-- begin: .cLayout -->
	<div class="cLayout clrfix">
	
		<?php $this->renderModules( 'js_profile_top' ); ?>
		<?php if($isMine) $this->renderModules( 'js_profile_mine_top' ); ?>		

		<!-- begin: .cSidebar -->
	    <div class="cSidebar clrfix">
	    	<?php $this->renderModules( 'js_side_top' ); ?>
	    	<?php $this->renderModules( 'js_profile_side_top' ); ?>
			<?php echo $sidebarTop; ?>
			
			<?php if($isMine) $this->renderModules( 'js_profile_mine_side_top' ); ?>
			<?php echo $about; ?>
			<?php echo $this->view('profile')->modGetFriendsHTML(); ?>
			<?php if( $config->get('enablegroups')){ ?>
			<?php echo $this->view('profile')->modGetGroupsHTML(); ?>
			<?php } ?>
			<?php if($isMine) $this->renderModules( 'js_profile_mine_side_bottom' ); ?>
			
			<?php echo $sidebarBottom; ?>
			<?php $this->renderModules( 'js_profile_side_bottom' ); ?>
			<?php $this->renderModules( 'js_side_bottom' ); ?>
	    </div>
	    <!-- end: .cSidebar -->




        <!-- begin: .cMain -->
	    <div class="cMain">
	    
			<div class="page-actions">
			  <?php echo $blockUserHTML;?>  
			  <?php echo $reportsHTML;?>
			  <?php echo $bookmarksHTML;?>
			  <div id="editLayout-stop" class="page-action" style="display: none;">
			  	<a onclick="joms.editLayout.stop()" href="javascript: void(0)"><?php echo JText::sprintf('COM_COMMUNITY_STOP_EDIT_PROFILE_APPS_LAYOUT') ?></a>
			  </div>
			</div>
				
			<?php echo @$header; ?>
			
			<?php $this->renderModules( 'js_profile_feed_top' ); ?>
			<div class="activity-stream-front">
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
				<?php endif; ?>
				<div class="activity-stream-profile">
					<div id="activity-stream-container">
				  	<?php echo $newsfeed; ?>
				  	</div>
				</div>
				
				<?php $this->renderModules( 'js_profile_feed_bottom' ); ?>
				<div id="apps-sortable" class="connectedSortable" >
				<?php echo $content; ?>
				</div>
			</div>
		</div>
	    <!-- end: .cMain -->

		<?php if($isMine) $this->renderModules( 'js_profile_mine_bottom' ); ?>
		<?php $this->renderModules( 'js_profile_bottom' ); ?>
		
	</div>
	<!-- end: .cLayout -->
</div>
<!-- begin: #cProfileWrapper -->