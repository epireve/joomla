<?php
/**
 * @package		JomSocial
 * @subpackage 	Template
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 *
 */
defined('_JEXEC') or die(); ?>
<script type="text/javascript" src="<?php echo JURI::root();?>components/com_community/assets/ajaxfileupload.pack.js"></script>
<script type="text/javascript" src="<?php echo JURI::root();?>components/com_community/assets/imgareaselect/scripts/jquery.imgareaselect.pack.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo JURI::root();?>components/com_community/assets/imgareaselect/css/imgareaselect-default.css" />
<script type="text/javascript">joms.filters.bind();</script>


<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 **/
defined('_JEXEC') OR DIE();
?>

<script type="text/javascript"> joms.filters.bind();</script>

<!-- begin: #cProfileWrapper -->
<div id="cProfileWrapper" class="cBlueface">
	<?php echo $adminControlHTML; ?>
	<!-- begin: .cLayout -->
	<div class="cLayout clrfix">
	
		<?php $this->renderModules( 'js_profile_top' ); ?>
		<?php if($isMine) $this->renderModules( 'js_profile_mine_top' ); ?>		

		<!-- begin: .cSidebar -->
		<div class="cSidebar clrfix">
			<!-- User avatar -->
                         <div class="profile-avatar" onMouseOver="joms.jQuery('.rollover').toggle()" onmouseout="joms.jQuery('.rollover').toggle()">
                            <img src="<?php echo $user->getAvatar(); ?>" alt="<?php echo $this->escape( $user->getDisplayName() ); ?>" />
                            <?php if( $isMine ): ?>
                            <div class="rollover"><a href="javascript:void(0)" onclick="joms.photos.uploadAvatar('profile','<?php echo $user->id?>')"><?php echo JText::_('COM_COMMUNITY_CHANGE_AVATAR')?></a></div>
                            <?php endif; ?>
                        </div>
			<!-- end Avatar -->

			<div class="clr"></div>
			
			<!-- Profile video link -->
			<?php if( $config->get('enablevideos') ){ ?>
				<?php if( $config->get('enableprofilevideo') && ($videoid != 0) ){ ?>
					<div class="profile-video-link" style="text-align: center; padding-bottom: 10px">
						<a class="icon-videos" onclick="joms.videos.playProfileVideo( <?php echo $videoid; ?> , <?php echo $user->id; ?> )" href="javascript:void(0);"><?php echo JText::_('COM_COMMUNITY_VIDEOS_MY_PROFILE');?></a>
					</div>
				<?php } ?>
			<?php } ?>
			<!-- end profile video link-->
			
			<!-- like box -->
			<div class="profile-likes"><span id="like-container"><?php echo $likesHTML; ?></span></div>
			<!-- end like box -->
			
			<div class="clr"></div>

			<?php $this->renderModules( 'js_side_top' ); ?>
			<?php $this->renderModules( 'js_profile_side_top' ); ?>
			<?php echo $sidebarTop; ?>            
        
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
			  <?php echo $blockUserHTML;?>  
			  <?php echo $reportsHTML;?>
			  <?php echo $bookmarksHTML;?>
			  <div id="editLayout-stop" class="page-action" style="display: none;">
			  	<a onclick="joms.editLayout.stop()" href="javascript: void(0)"><?php echo JText::sprintf('COM_COMMUNITY_STOP_EDIT_PROFILE_APPS_LAYOUT') ?></a>
			  </div>
			</div>
			
			<?php echo @$header; ?>
			
			
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="cBlueface-table">
              <tr>
                  <?php if($config->get('enablekarma')){ ?>
                  <td align="center" valign="top" style="width: 20%">
                      <div class="number"><?php echo $user->_points; ?></div>
                      <div class="text"><?php echo JText::sprintf( (CStringHelper::isPlural($user->_points)) ? 'COM_COMMUNITY_POINTS' : 'COM_COMMUNITY_SINGULAR_POINT' ); ?></div>
                  </td>
                  <?php } ?>
                  <td align="center" valign="top" style="width: 20%">
                      <a href="<?php echo CRoute::_('index.php?option=com_community&view=groups&userid='.$user->id); ?>">
                          <div class="number"><?php echo $totalgroups; ?></div>
                          <div class="text"><?php echo JText::sprintf( (CStringHelper::isPlural($totalgroups)) ? 'COM_COMMUNITY_GROUPS_PLURAL_GROUP' : 'COM_COMMUNITY_SINGULAR_GROUP' ); ?></div>
                      </a>
                  </td>
                  
                  <td align="center" valign="top" style="width: 20%">
                      <a href="<?php echo CRoute::_('index.php?option=com_community&view=friends&userid='.$user->id); ?>">
                          <div class="number"><?php echo $totalfriends; ?></div>
                          <div class="text"><?php echo JText::sprintf( (CStringHelper::isPlural($totalfriends)) ? 'COM_COMMUNITY_FRIENDS' : 'COM_COMMUNITY_SINGULAR_FRIEND' ); ?></div>
                      </a>
                  </td>
                  <?php
                      if( $config->get('enablephotos') )
                      {
                  ?>
                  <td align="center" valign="top" style="width: 20%">
                      <a href="<?php echo CRoute::_('index.php?option=com_community&view=photos&task=myphotos&userid='.$user->id); ?>">
                          <div class="number"><?php echo $totalphotos; ?></div>
                          <div class="text"><?php echo JText::sprintf( (CStringHelper::isPlural($totalphotos)) ? 'COM_COMMUNITY_PHOTOS' : 'COM_COMMUNITY_SINGULAR_PHOTO' ); ?></div>
                      </a>
                  </td>
                  <?php
                      }
                  ?>
                  <td align="center" valign="top" style="width: 20%">
                      <div class="number">
                      <?php
                      if ( !$totalactivities == '' OR $totalactivities > 0 ) {
                          echo $totalactivities;
                      }
                      else {
                          echo 0;
                      }
                       ?>
                       </div>
                       <div class="text"><?php echo JText::sprintf( (CStringHelper::isPlural($totalactivities)) ? 'COM_COMMUNITY_ACTIVITIES' : 'COM_COMMUNITY_ACTIVITY' ); ?></div>
                  </td>
              </tr>
          </table>

			<?php echo $about; ?>
				
				
			<div class="cModule" style="clear: left; padding-top: 20px">
				
				<h3><?php echo JText::sprintf('COM_COMMUNITY_FRONTPAGE_RECENT_ACTIVITIES') ?></h3>
				
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
			
		</div>
	    <!-- end: .cMain -->

		<?php if($isMine) $this->renderModules( 'js_profile_mine_bottom' ); ?>
		<?php $this->renderModules( 'js_profile_bottom' ); ?>
		
	</div>
	<!-- end: .cLayout -->
</div>
<!-- begin: #cProfileWrapper -->

<?php /* Insert plugin javascript at the bottom */ echo $jscript; ?>