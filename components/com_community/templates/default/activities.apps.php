<?php
/**
 * @packageJomSocial
 * @subpackage Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();
?>

<?php if($latestId == 0) : ?>
<ul class="cResetList cFeed">
<?php endif; ?>

<?php foreach($activities as $act): ?>
	<?php
		if(!isset($act->id)){
			continue;
		}
	?>
    <?php if($act->id > $latestId) : ?>
	<?php if($act->type =='title'): ?>
		<?php if($config->get('activitydateformat') == COMMUNITY_DATE_FIXED ){ ?>
			<li class="ctitle newly-added" style="display:none"><?php echo $act->title; ?></li>
		<?php } ?>
	<?php else: $actor = CFactory::getUser($act->actor);?>
		<li <?php if($latestId>0)echo 'style="display:none"'; ?> id="<?php echo $idprefix; ?>profile-newsfeed-item<?php echo $act->id; ?>" class="cFeed-item <?php echo $act->app;?> <?php if($latestId>0)echo "newly-added"; ?> <?php if($isMine) { echo 'isMine'; } ?> <?php if($isSuperAdmin && !$isMine) { echo 'isSuperAdmin'; } ?> <?php if(!$config->get('showactivityavatar')) { echo 'no-avatar'; } ?>">
	    <!--NEWS FEED AVATAR-->
			<div class="newsfeed-avatar cAvatar">
			<?php if($config->get('showactivityavatar')) { ?>
				<?php if(!empty($actor->id)) { ?>
					<a href="<?php echo CUrlHelper::userLink($actor->id); ?>"><img class="cAvatar" src="<?php echo $actor->getThumbAvatar(); ?>" border="0" alt=""/></a>
				<?php } else { ?>
					<img class="cAvatar" src="<?php echo $actor->getThumbAvatar(); ?>" border="0" alt=""/>
				<?php } ?>
			<?php } ?>
			</div>
	    <!--NEWS FEED AVATAR-->

			<!--NEWS FEED CONTENT-->
	    <div class="newsfeed-content">
				<div class="newsfeed-content-top">
					
				<?php 
				
					// Put user header link if necessary					
					if( $apptype == 'group' && $act->eventid ){
						// For group event, show the arrow indicator (using <span class="com_icons com_icons12 com_icons-inline com_icons-rarr">»</span>)
						echo '<div class="newsfeed-content-actor"><a href="'.CUrlHelper::userLink($act->actor).'">'.$actor->getDisplayName().'</a> <span class="com_icons com_icons12 com_icons-inline com_icons-rarr">»</span> <a href="'.CUrlHelper::eventLink($act->eventid).'">'.$act->appTitle.'</a></div>';
					} else {
						?>
						<div class="newsfeed-content-actor">
							<strong><a class="actor-link" href="<?php echo CUrlHelper::userLink($act->actor) ;?>"><?php echo $actor->getDisplayName(); ?></a></strong>
						</div>
						<?php
					}
					
					// Order of replacement
					$order   = array("\r\n", "\n", "\r");
					$replace = '<br/>';

					// Processes \r\n's first so they aren't converted twice.
					$messageDisplay = str_replace($order, $replace, $act->title);
						
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
							<div class="newsfeed-mapFiller"></div>
						</div>
						<small class="newsfeed-mapLoc"><span><?php echo JText::_('COM_COMMUNITY_POSTED_FROM');?> <?php echo $act->location; ?></span></small>
						<small class="newsfeed-mapBigger"><a target="_blank" href="http://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=<?php echo urlencode($act->location); ?>"><?php echo JText::_('COM_COMMUNITY_VIEW_LARGER_MAP');?></a></small>
						<div class="clear"></div>
					</div>
					
					
				<?php } ?>
				
				<!-- NEWS FEED DATE, ICON & ACTIONS -->
				<div class="newsfeed-meta small">
					
					<img src="<?php echo $act->favicon; ?>" class="newsfeed-icon <?php echo $act->app;?>-icon" alt="<?php echo $act->app;?>" />
					<?php echo $act->created; ?>
					
					<!-- if no one likes yet, then show: -->
					<?php if($act->likeAllowed && $showLike) { ?>
						<?php if($act->userLiked!=COMMUNITY_LIKE) { ?>
							&#x2022; <a id="like_id<?php echo $act->id?>" href="#like" onclick="jax.call('community','system,ajaxStreamAddLike', '<?php echo $act->id; ?>');return false;"><?php echo JText::_('COM_COMMUNITY_LIKE');?></a>
						<?php } else { ?>
							&#x2022; <a id="like_id<?php echo $act->id?>" href="#unlike" onclick="jax.call('community','system,ajaxStreamUnlike', '<?php echo $act->id; ?>');return false;"><?php echo JText::_('COM_COMMUNITY_UNLIKE');?></a>						
						<?php } ?>
					<?php } ?>
					
					<!-- Show if it is explicitly allowed: -->
					<?php if($act->commentAllowed && $isMember) { ?>
					&#x2022; <a href="javascript:void(0);" onclick="joms.miniwall.show('<?php echo $act->id; ?>');return false;"><?php echo JText::_('COM_COMMUNITY_COMMENT');?></a>
					<?php } ?>
					
					<?php if( $config->get('stream_show_map') && !empty($act->location) ) { ?>
						<a onclick="joms.activities.showMap(<?php echo $act->id; ?>, '<?php echo urlencode($act->location); ?>');" class="newsfeed-location" title="<?php echo JText::_('COM_COMMUNITY_VIEW_LOCATION_TIPS');?>" href="javascript: void(0)"><?php echo JText::_('COM_COMMUNITY_VIEW_LOCATION');?></a>
					<?php } ?>
					
					<div class="clr"></div>
				</div>
				
				<div>
					<?php if($act->commentAllowed && $showComment) { ?>
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

							<?php if( $isMember || $isSuperAdmin ): ?>
							<div class="cComment wallinfo wallform <?php if($act->commentCount == 0): echo 'wallnone'; endif; ?>">
								<!-- post new comment form -->
								<form action="" class="wall-coc-form">
									<textarea cols="" rows="" style="height: 40px; margin-bottom: 4px" name="comment"></textarea>
									<div class="wall-coc-form-actions">
										<button type="submit" class="wall-coc-form-action add button" onclick="joms.miniwall.add('<?php echo $act->id; ?>');return false;"><?php echo JText::_('COM_COMMUNITY_POST_COMMENT_BUTTON');?></button>
										<a class="wall-coc-form-action cancel" onclick="joms.miniwall.cancel('<?php echo $act->id; ?>');return false;" href="#cancelPostinComment"><?php echo JText::_('COM_COMMUNITY_CANCEL_BUTTON');?></a>
										<span style="margin-left: 5px;" class="wall-coc-errors"></span>
										
									</div>
									<div class="clr"></div>
								</form>
								<?php /* Hide reply button if no one has post a comment */ ?>
								<?php if( $isSuperAdmin || ($act->isFriend || $act->app == 'system') || $isMember): ?>
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
				if($my->id == $act->actor){
			?>
				<div class="newsfeed-remove"><a class="remove" onclick="jax.call('community', 'activities,ajaxHideActivity' , '<?php echo $my->id; ?>' , '<?php echo $act->id; ?>','<?php echo $act->app; ?>');" href="javascript:void(0);"><?php echo JText::_('COM_COMMUNITY_HIDE');?></a></div>
			<?php } else if($isSuperAdmin && !$isMine) { ?>
			<!--NEWS FEED REMOVE-->
	    
			<!--NEWS FEED DELETE-->
			<div class="newsfeed-remove"><a class="remove" onclick="joms.activities.remove('<?php echo $act->app; ?>', '<?php echo $act->id; ?>');" href="javascript:void(0);"><?php echo JText::_('COM_COMMUNITY_DELETE');?></a></div>
			<?php } ?>
			<!--NEWS FEED DELETE-->
		</li>
	<?php endif; ?>
    <?php endif; ?>
<?php endforeach; ?>

<?php if($latestId == 0) : ?>
</ul>
<?php if( $exclusions !== false && $showMoreActivity) { ?>
	<div class="joms-newsfeed-more" id="activity-more">
		<a class="more-activity-text" href="javascript:void(0);" onclick="joms.activities.more();"><?php echo JText::_('COM_COMMUNITY_MORE');?></a>
		<div class="loading"></div>
	</div>
<?php } ?>

<input type="hidden" id="activity-type" value="<?php echo $filter; ?>" />
<input type="hidden" id="activity-exclusions" value="<?php echo $exclusions;?>" />
<?php endif; ?>

<!-- application type and app id-->	
<input type="hidden" id="apptype" value="<?php echo $apptype; ?>" />
<input type="hidden" id="appid" value="<?php echo ($eventId > 0) ? $eventId : $groupId; ?>" />