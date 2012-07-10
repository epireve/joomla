<?php
/**
 * @package		JomSocial
 * @subpackage 	Template
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 *
 */
defined('_JEXEC') or die(); ?>

<script type="text/javascript">joms.filters.bind();</script>

<?php $this->renderModules( 'js_profile_top' ); ?>

<?php echo $adminControlHTML; ?>
<div class="page-actions">
	<?php echo $blockUserHTML;?>
    <?php echo $reportsHTML;?>
    <?php echo $bookmarksHTML;?>
    <div class="clr"></div>
</div>

<div class="profile-right">
    <!-- Avatar -->
    <div class="profile-avatar">
	<?php if( $isMine ): ?><a href="<?php echo CRoute::_('index.php?option=com_community&view=profile&task=uploadAvatar'); ?>"><?php endif; ?><img src="<?php echo $user->getAvatar(); ?>" alt="<?php echo $user->getDisplayName(); ?>" width="160" /><?php if( $isMine ): ?></a><?php endif; ?>
    </div>
    
    <?php if( $config->get('enablevideos') ){ ?>
    <?php 	if( $config->get('enableprofilevideo') && ($videoid != 0) ){ ?>
	<div class="profile-video"><a class="icon-videos" onclick="joms.videos.playProfileVideo( <?php echo $videoid; ?> , <?php echo $user->id; ?> )" href="javascript:void(0);"><?php echo JText::_('COM_COMMUNITY_VIDEOS_MY_PROFILE');?></a></div>
	<?php 	} ?>
    <?php } ?>
    
    <div class="profile-likes">
		<span id="like-container"><?php echo $likesHTML; ?></span>
	</div>
    
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

<div class="profile-main">
    <?php echo @$header; ?>

    <div id="user-info-button">
        <?php if($config->get('enablekarma')){ ?>
        <div class="user-green">
            <div class="user-green-inner">
                <div class="number"><?php echo $user->_points; ?></div>
                <div class="text"><?php echo JText::sprintf( (CStringHelper::isPlural($user->_points)) ? 'COM_COMMUNITY_POINTS' : 'COM_COMMUNITY_SINGULAR_POINT' ); ?></div>
            </div>
        </div>
        <?php } ?>
        
        <?php if($config->get('enablegroups')){ ?>
        <div class="user-blue">
            <div class="user-blue-inner">
                <a href="<?php echo CRoute::_('index.php?option=com_community&view=groups&task=mygroups&userid='.$user->id); ?>">
                    <div class="number"><?php echo $totalgroups; ?></div>
                    <div class="text"><?php echo JText::sprintf( (CStringHelper::isPlural($totalgroups)) ? 'COM_COMMUNITY_GROUPS_PLURAL_GROUP' : 'COM_COMMUNITY_SINGULAR_GROUP' ); ?></div>
                </a>
            </div>
        </div>
        <?php } ?>
    
        <div class="user-grey">
            <div class="user-grey-inner">
                <a href="<?php echo CRoute::_('index.php?option=com_community&view=friends&userid='.$user->id); ?>">
                    <div class="number"><?php echo $totalfriends; ?></div>
                    <div class="text"><?php echo JText::sprintf( (CStringHelper::isPlural($totalfriends)) ? 'COM_COMMUNITY_FRIENDS' : 'COM_COMMUNITY_SINGULAR_FRIEND' ); ?></div>
                </a>
            </div>
        </div>
        
        <?php if( $config->get('enablephotos') ) { ?>
        <div class="user-orange">
            <div class="user-orange-inner">
                <a href="<?php echo CRoute::_('index.php?option=com_community&view=photos&task=myphotos&userid='.$user->id); ?>">
                    <div class="number"><?php echo $totalphotos; ?></div>
                    <div class="text"><?php echo JText::sprintf( (CStringHelper::isPlural($totalphotos)) ? 'COM_COMMUNITY_PHOTOS' : 'COM_COMMUNITY_SINGULAR_PHOTO' ); ?></div>
                </a>
            </div>
        </div>
        <?php } ?>
        
        <div class="user-red">
            <div class="user-red-inner">
                <div class="number"><?php echo (!$totalactivities == 0) ? $totalactivities : 0; ?></div>
                <div class="text"><?php echo JText::sprintf( (CStringHelper::isPlural($totalactivities)) ? 'COM_COMMUNITY_ACTIVITIES' : 'COM_COMMUNITY_ACTIVITY' ); ?></div>
            </div>
        </div>
        <div class="clr"></div>
    </div>   
    
    <?php echo $about; ?>
    <?php $this->renderModules( 'js_profile_feed_top' ); ?>    
    <div style="position: relative;">
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
        <div id="activity-stream-container">
        <?php echo $newsfeed; ?>
        </div>
    </div>
    <?php $this->renderModules( 'js_profile_feed_bottom' ); ?>    
    <?php echo $content; ?>
</div>

<div class="clr"></div>

<?php $this->renderModules( 'js_profile_bottom' ); ?>

<?php /* Insert plugin javascript at the bottom */ echo $jscript; ?>