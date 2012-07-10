<?php
/**
 * @packageJomSocial
 * @subpackage Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();

?>

<?php
	// 1. for append list put this into an unordered list
	if($latestId == 0) : ?>
		<ul class="cResetList cFeed">
<?php endif; ?>

<?php 
	// 2. welcome message for new installation
	if(isset($freshInstallMsg)) : 
?>
	<div class="welcome-msg-container">
		<?php echo $freshInstallMsg;?>
	</div>
<?php endif; ?>

<?php 
	// 3. all activities iteration
	foreach($activities as $act): 		
?>
	<?php
		//skip to the next iteration if there is no id for this activity
		if(!isset($act->id)){
			continue;
		}
	?>
	
    <?php 
		// 4. append 
		if($act->id > $latestId) : 
	?>
	<?php 
		// 5. Title
		if($act->type =='title'): 
	?>
		<?php if($config->get('activitydateformat') == COMMUNITY_DATE_FIXED ){ ?>
			<li class="ctitle newly-added" style="display:none"><?php echo $act->title; ?></li>
		<?php } ?>
	<?php else: 
		$actor = CFactory::getUser($act->actor); $target = CFactory::getUser($act->target);
		
		// Disallow User who is non-friend to Administrator from Commenting on Custom Post
		$allowComment = ( $act->commentAllowed && ( $act->isMyEvent || $act->isMyGroup || $act->isFriend || $isSuperAdmin) && $showComment ) ;
		$allowComment = ( $allowComment || ($config->get( 'allmemberactivitycomment' ) == 1 && COwnerHelper::isRegisteredUser()));
		
		$allowLike	  = ($act->likeAllowed && $showLike);
		// Order of replacement
		$order   = array("\r\n", "\n", "\r");
		$replace = '<br/>';
		// Processes \r\n's first so they aren't converted twice.
		$messageDisplay = str_replace($order, $replace, $act->title);
	?>
		
		<?php
		if($act->compactView) : ?>
			<!-- start compact view -->
			<li <?php if($latestId>0)echo 'style="display:none"'; ?> id="<?php echo $idprefix; ?>profile-newsfeed-item<?php echo $act->id; ?>" class="cFeed-item cFeed-itemCompact <?php echo $act->app;?>-feed <?php if($latestId > 0)echo "newly-added"; ?> <?php if($my->id == $act->actor) { echo 'isMine'; } ?> <?php if($isSuperAdmin) { echo 'isSuperAdmin'; } ?> <?php if(!$config->get('showactivityavatar')) { echo 'no-avatar'; } ?>">
				<div class="newsfeed-content">
					<img src="<?php echo $act->favicon; ?>" class="newsfeed-icon <?php echo $act->app;?>-icon" alt="<?php echo $act->app;?>" /> <?php echo $messageDisplay; ?>
					<?php
					if ($act->group_access == 1 || $act->event_access == 1) {
						echo ' <span title="'.JText::_('COM_COMMUNITY_GROUPS_PRIVATE').'" class="com_icons com_icons12 com_icons-inline com_icons-private jomNameTips">'.JText::_('COM_COMMUNITY_GROUPS_PRIVATE').'</span>';
					}
					?>
				</div>
				<!--NEWS FEED REMOVE-->
				<?php 
					// user can remove their own post
					if($my->id == $act->actor):
				?>
					<div class="newsfeed-remove"><a class="cIcon cIcon-Hide jomNameTips" onclick="jax.call('community', 'activities,ajaxHideActivity' , '<?php echo $my->id; ?>' , '<?php echo $act->id; ?>','<?php echo $act->app; ?>');" href="javascript:void(0);" title="<?php echo JText::_('COM_COMMUNITY_HIDE');?>"><?php echo JText::_('COM_COMMUNITY_HIDE');?></a></div>
				<?php endif; ?>
				<!--NEWS FEED REMOVE-->
				
				<!--NEWS FEED DELETE-->
				<?php if($isSuperAdmin): ?>
				<div class="newsfeed-remove"><a class="cIcon jomNameTips remove" onclick="joms.activities.remove('<?php echo $act->app; ?>', '<?php echo $act->id; ?>');" href="javascript:void(0);" title="<?php echo JText::_('COM_COMMUNITY_DELETE');?>"><?php echo JText::_('COM_COMMUNITY_DELETE');?></a></div>
				<?php endif; ?>
				<!--NEWS FEED DELETE-->
			</li>
			<!-- end compact view -->
		<?php
		else : ?>
		<li <?php if($latestId>0)echo 'style="display:none"'; ?> id="<?php echo $idprefix; ?>profile-newsfeed-item<?php echo $act->id; ?>" class="cFeed-item <?php echo $act->app;?>-feed <?php if($latestId > 0)echo "newly-added"; ?> <?php if($my->id == $act->actor) { echo 'isMine'; } ?> <?php if($isSuperAdmin) { echo 'isSuperAdmin'; } ?> <?php if(!$config->get('showactivityavatar')) { echo 'no-avatar'; } ?>">
	    <!--NEWS FEED AVATAR-->
		<div class="newsfeed-avatar">
		<?php if($config->get('showactivityavatar')) { ?>
			<?php if(!empty($actor->id)) { ?>
				<a href="<?php echo CUrlHelper::userLink($actor->id); ?>"><img class="cAvatar" src="<?php echo $actor->getThumbAvatar(); ?>" width="42" border="0" alt=""  author="<?php echo $actor->id;?>"/></a>
			<?php } else { ?>
				<img class="cAvatar" src="<?php echo $actor->getThumbAvatar(); ?>" width="36" border="0" alt="" author="<?php echo $actor->id;?>"/>
			<?php } ?>
		<?php } ?>
		</div>
	    <!--NEWS FEED AVATAR-->

		<!--NEWS FEED CONTENT-->
	    <div class="newsfeed-content">
			<div class="newsfeed-content-top">
			<?php 
					// Put user header link if necessary
					if( $act->eventid ){
						echo '<div class="newsfeed-content-actor">';
							echo '<strong><a class="actor-link" href="'.CUrlHelper::userLink($act->actor).'">'.$actor->getDisplayName().'</a></strong> <span class="com_icons com_icons12 com_icons-inline com_icons-rarr">»</span> <a href="'.CUrlHelper::eventLink($act->eventid).'">'.$act->appTitle.'</a>';
							if ($act->event_access == 1) {
								echo ' <span title="'.JText::_('COM_COMMUNITY_GROUPS_PRIVATE').'" class="com_icons com_icons12 com_icons-inline com_icons-private jomNameTips">'.JText::_('COM_COMMUNITY_GROUPS_PRIVATE').'</span>';
							}
						echo '</div>';
					}
					else if( $act->groupid ){
						echo '<div class="newsfeed-content-actor">';
							echo '<strong><a class="actor-link" href="'.CUrlHelper::userLink($act->actor).'">'.$actor->getDisplayName().'</a></strong> <span class="com_icons com_icons12 com_icons-inline com_icons-rarr">»</span> <a href="'.CUrlHelper::groupLink($act->groupid).'">'.$act->appTitle.'</a>';
							if ($act->group_access == 1) {
								echo ' <span title="'.JText::_('COM_COMMUNITY_GROUPS_PRIVATE').'" class="com_icons com_icons12 com_icons-inline com_icons-private jomNameTips">'.JText::_('COM_COMMUNITY_GROUPS_PRIVATE').'</span>';
							}
						echo '</div>';
					}
					else if( !empty($act->target) && ( $act->target != $act->actor ) && ($act->app == 'profile') && ($act->target != $actorId) ){
						// Actor doing something to target
						echo '<div class="newsfeed-content-actor">';
							echo '<strong><a class="actor-link" href="'.CUrlHelper::userLink($act->actor).'">'.$actor->getDisplayName().'</a></strong> ';
							echo  '<span class="com_icons com_icons12 com_icons-inline com_icons-rarr">»</span> ';
							echo '<a class="actor-link" href="'.CUrlHelper::userLink($act->target).'">'.$target->getDisplayName().'</a>';
						echo '</div>';
					} 
					else 
					{
						// Everythings else should have the actor's title
						$actorNeedle = '<a class="actor-link" href="'.CUrlHelper::userLink($act->actor).'">'.$actor->getDisplayName().'</a>';
						
						// Will do a preg search for the actor's name and avoid a duplicated display
						if (!preg_match('/^<a(.)+class="(.)?actor-link(.)?"(.)+>'.$actor->getDisplayName().'<\/a>/', $messageDisplay))
						{
							echo '<div class="newsfeed-content-actor">';
								echo '<strong>'.$actorNeedle.'</strong>';
							echo '</div>';
						}
					} 

					
					echo $messageDisplay;
			?>
			</div>
			<?php if(!empty($act->content) && $showMore ){ ?>
				<?php if( $config->getBool('showactivitycontent')) { ?>
					<div id="<?php echo $idprefix; ?>profile-newsfeed-item-content-<?php echo $act->id;?>" class="newsfeed-content-hidden" style="display:block"><?php echo $act->content; ?></div>
				<?php } else { ?>
					<div id="<?php echo $idprefix; ?>profile-newsfeed-item-content-<?php echo $act->id;?>" class="small profile-newsfeed-item-action" style="display:block">
						<a href="javascript:void(0);" id="newsfeed-content-<?php echo $act->id;?>" onclick="joms.activities.getContent('<?php echo $act->id;?>');"><?php echo JText::_('COM_COMMUNITY_MORE');?></a>
					</div>
				<?php } ?>
			<?php } ?>
			
			<?php if( $config->get('stream_show_map') && !empty($act->location)) { ?>
				<div class="clear"></div>

				<div class="newsfeed-map" id="newsfeed-map-<?php echo $act->id; ?>">
					<div class="newsfeed-mapFade">
						<div id="newsfeed-map-heatzone-<?php echo $act->id; ?>" class="newsfeed-map-heatzone">&#160;</div>
						<div class="newsfeed-mapFiller"></div>
					</div>
					<small class="newsfeed-mapLoc"><span><?php echo $act->location; ?></span></small>
					<small class="newsfeed-mapBigger"><a target="_blank" href="http://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=<?php echo urlencode($act->location); ?>"><?php echo JText::_('COM_COMMUNITY_VIEW_LARGER_MAP');?></a></small>
					<div class="clear"></div>
				</div>		
			<?php } ?>
			
			<!-- NEWS FEED DATE, ICON & ACTIONS -->
			<div class="newsfeed-meta small">		
				<img src="<?php echo $act->favicon; ?>" class="newsfeed-icon <?php echo $act->app;?>-icon" alt="<?php echo $act->app;?>" />
				<?php echo $act->created; ?>
				
				<!-- if no one likes yet, then show: -->
				<?php if($allowLike) { ?>
					<?php if($act->userLiked!=COMMUNITY_LIKE) { ?>
						&#x2022; <a id="like_id<?php echo $act->id?>" href="#like" onclick="jax.call('community','system,ajaxStreamAddLike', '<?php echo $act->id; ?>');return false;"><?php echo JText::_('COM_COMMUNITY_LIKE');?></a>
					<?php } else { ?>
						&#x2022; <a id="like_id<?php echo $act->id?>" href="#unlike" onclick="jax.call('community','system,ajaxStreamUnlike', '<?php echo $act->id; ?>');return false;"><?php echo JText::_('COM_COMMUNITY_UNLIKE');?></a>						
					<?php } ?>
				<?php } ?>
				
				<!-- Show if it is explicitly allowed: -->
				<?php if($allowComment ) { ?>
				&#x2022; <a href="javascript:void(0);" onclick="joms.miniwall.show('<?php echo $act->id; ?>');return false;"><?php echo JText::_('COM_COMMUNITY_COMMENT');?></a>
				<?php } ?>
				
				<?php if( $config->get('stream_show_map') && !empty($act->location) ) { ?>
					<a onclick="joms.activities.showMap(<?php echo $act->id; ?>, '<?php echo urlencode($act->location); ?>');" class="newsfeed-location" title="<?php echo JText::_('COM_COMMUNITY_VIEW_LOCATION_TIPS');?>" href="javascript: void(0)"><?php echo JText::_('COM_COMMUNITY_VIEW_LOCATION');?></a>
				<?php } ?>
				
				<div class="clr"></div>
			</div>
			
			<div>
				<?php if( $allowComment ) { ?>
				
				<script type="text/javascript" charset="utf-8">
				joms.jQuery(document).ready(function(){
					var p = joms.jQuery("button.wall-coc-form-action.add.button").outerHeight() + joms.jQuery("button.wall-coc-form-action.add.button").height();
					joms.jQuery("a.wall-coc-form-action.cancel").css('line-height', p+'px');

				});
				</script>
				
					<div class="wall-cocs" id="wall-cmt-<?php echo $act->id; ?>">
						<?php if($act->likeCount > 0 && $showLike) { /* hide count if no one like it */?>
						<div class="cComment wallinfo wallicon-like">
							<a onclick="jax.call('community','system,ajaxStreamShowLikes', '<?php echo $act->id; ?>');return false;" href="#showLikes"><?php echo ($act->likeCount > 1) ? JText::sprintf('COM_COMMUNITY_LIKE_THIS_MANY', $act->likeCount) : JText::sprintf('COM_COMMUNITY_LIKE_THIS', $act->likeCount); ?></a>
						</div>
						<?php } ?>
						<?php if( $act->commentCount > 1 ) { ?>
						<div class="cComment wallinfo wallmore wallicon-comment">
							<a onclick="jax.call('community','system,ajaxStreamShowComments', '<?php echo $act->id; ?>');return false;" href="#showallcomments"><?php echo JText::sprintf('COM_COMMUNITY_ACTIVITY_NO_COMMENT',$act->commentCount,'wall-cmt-count') ?></a>
						</div>
						<?php } ?>
						<?php if( $act->commentCount > 0 ) { ?>
							<?php echo $act->commentLast; ?>
						<?php } ?>

						<?php if($allowComment ) : ?>
						<div class="cComment wallinfo wallform <?php if($act->commentCount == 0): echo 'wallnone'; endif; ?>">
							<!-- post new comment form -->
							<form action="" class="wall-coc-form">
								<textarea cols="" rows="" style="height: 40px; margin-bottom: 4px" name="comment"></textarea>
								<div class="wall-coc-form-actions">
								<ul>
									<li><a class="wall-coc-form-action cancel" onclick="joms.miniwall.cancel('<?php echo $act->id; ?>');return false;" href="#cancelPostinComment"><?php echo JText::_('COM_COMMUNITY_CANCEL_BUTTON');?></a>
									<span style="margin-left: 5px;" class="wall-coc-errors"></span></li>								
									<li><button type="submit" class="wall-coc-form-action add button" onclick="joms.miniwall.add('<?php echo $act->id; ?>');return false;"><?php echo JText::_('COM_COMMUNITY_POST_COMMENT_BUTTON');?></button></li>
								</ul>
								</div>
								<div class="clr"></div>
							</form>
							<?php /* Hide reply button if no one has post a comment */ ?>
							<?php if( $allowComment ): ?>
							  <span class="show-cmt"><a href="javascript:void(0);" onclick="joms.miniwall.show('<?php echo $act->id; ?>')" ><?php echo JText::_('COM_COMMUNITY_REPLY');?></a></span>
							<?php endif; ?>
							
						</div>
						<?php endif; ?>
						
					</div>
				<?php } ?>
			</div>
			<!-- /NEWS FEED DATE, ICON & ACTIONS -->
		</div>
		<!--/NEWS FEED CONTENT-->

		<!--NEWS FEED REMOVE-->
		<?php 
			// user can remove their own post
			if($my->id == $act->actor):
		?>
			<div class="newsfeed-remove"><a class="cIcon cIcon-Hide jomNameTips" onclick="jax.call('community', 'activities,ajaxHideActivity' , '<?php echo $my->id; ?>' , '<?php echo $act->id; ?>','<?php echo $act->app; ?>');" href="javascript:void(0);" title="<?php echo JText::_('COM_COMMUNITY_HIDE');?>"><?php echo JText::_('COM_COMMUNITY_HIDE');?></a></div>
		<?php endif; ?>
		<!--NEWS FEED REMOVE-->
	
		<!--NEWS FEED DELETE-->
		<?php if($isSuperAdmin): ?>
		<div class="newsfeed-remove"><a class="cIcon jomNameTips remove" onclick="joms.activities.remove('<?php echo $act->app; ?>', '<?php echo $act->id; ?>');" href="javascript:void(0);" title="<?php echo JText::_('COM_COMMUNITY_DELETE');?>"><?php echo JText::_('COM_COMMUNITY_DELETE');?></a></div>
		<?php endif; ?>
		<!--NEWS FEED DELETE-->
		</li>
		<!-- END Feed item -->
		<?php endif; // End full-view ?>
	<?php endif; // 4. append ?>
    <?php endif; // 5  ?>
<?php endforeach; ?>

<?php 
	// 1
	if($latestId == 0) : 
?>
	</ul>


	<?php if($showMoreActivity) { ?>
		<div class="joms-newsfeed-more" id="activity-more">
			<a class="more-activity-text" href="javascript:void(0);" onclick="joms.activities.more();"><?php echo JText::_('COM_COMMUNITY_MORE');?></a>
			<div class="loading"></div>
		</div>
	<?php } ?>

	<input type="hidden" id="activity-type" value="<?php echo $filter; ?>" />
<?php endif; ?>

<?php if($config->get('newtab')){ ?>
<script type="text/javascript">
	joms.jQuery(document).ready(function(){
	    joms.jQuery("div.newsfeed-content-top > a").attr('target', '_blank');
	});
</script>
<?php } ?>