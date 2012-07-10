<?php
/**
 * @package		JomSocial
 * @subpackage	Template
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 *
 * @params	isMine		boolean is this group belong to me
 * @params	categories	Array An array of categories object
 * @params	members		Array An array of members object
 * @params	event		Event A group object that has the property of a group
 * @params	wallForm	string A html data that will output the walls form.
 * @params	wallContent string A html data that will output the walls data.
 **/
defined('_JEXEC') or die();
?>

<script type="text/javascript" src="<?php echo JURI::root();?>components/com_community/assets/ajaxfileupload.pack.js"></script>
<script type="text/javascript" src="<?php echo JURI::root();?>components/com_community/assets/imgareaselect/scripts/jquery.imgareaselect.pack.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo JURI::root();?>components/com_community/assets/imgareaselect/css/imgareaselect-default.css" />
<script type="text/javascript" src="<?php JURI::root()?>components/com_community/assets/joms.jomSelect.js"></script>
<div class="event">
	<div class="page-actions">
		<?php echo $reportHTML;?>
		<?php echo $bookmarksHTML;?>
	</div>
	<!-- begin: .cLayout -->
	<div class="cLayout clrfix">
		<!-- begin: .cSidebar -->
			<div class="cSidebar clrfix">

				<!-- event administration -->
				<?php if($isMine || $isCommunityAdmin || $isAdmin || $handler->manageable()) { ?>
				<div id="community-event-option" class="cModule collapse">
					<h3 href="javascript: void(0)" onclick="joms.apps.toggle('#community-event-option');"><?php echo JText::_('COM_COMMUNITY_EVENTS_ADMIN_OPTION'); ?></h3>
					<div class="app-box-menus">
						<div class="app-box-menu toggle">
							<a class="app-box-menu-icon" href="javascript: void(0)" onclick="joms.apps.toggle('#community-event-option');"></a>
						</div>
					</div>

					<div class="app-box-content" style="display:none">
						<ul class="event-menus clrfix">
							<?php if( $isMine || $isCommunityAdmin || $isAdmin) {?>
								<!-- Send email to participants -->
								<li class="event-menu">
									<a class="event-invite-email" href="<?php echo $handler->getFormattedLink('index.php?option=com_community&view=events&task=sendmail&eventid=' . $event->id );?>"><?php echo JText::_('COM_COMMUNITY_EVENTS_EMAIL_SEND');?></a>
								</li>
								<!-- Edit Event -->
								<li class="event-menu">
									<a class="event-edit-info" href="<?php echo $handler->getFormattedLink('index.php?option=com_community&view=events&task=edit&eventid=' . $event->id );?>"><?php echo JText::_('COM_COMMUNITY_EVENTS_EDIT');?></a>
								</li>
							<?php } ?>

							<?php if( ($event->permission != COMMUNITY_PRIVATE_EVENT) && ($isMine || $isCommunityAdmin || $isAdmin) ){ ?>
								<!-- Copy Event -->
								<li class="event-menu">
										<a class="event-copy" href="<?php echo $handler->getFormattedLink('index.php?option=com_community&view=events&task=create&eventid=' . $event->id );?>"><?php echo JText::_('COM_COMMUNITY_EVENTS_DUPLICATE');?></a>
								</li>
							<?php } ?>


							<?php if( $handler->manageable() ) { ?>
								<!-- Delete Event -->
								<li class="event-menu important">
									<a class="event-delete" href="javascript:void(0);" onclick="javascript:joms.events.deleteEvent('<?php echo $event->id;?>');"><?php echo JText::_('COM_COMMUNITY_EVENTS_DELETE'); ?></a>
								</li>
							<?php } ?>
						</ul>
					</div>
				</div>
				<?php } ?>
				<!-- end event administration -->

				<!-- New Event Response -->
				<?php if( $handler->isAllowed() && !$isPastEvent ) { ?>
				<div id="community-event-rsvp" class="cModule">
                                        <h3><?php echo JText::_('COM_COMMUNITY_EVENTS_YOUR_RSVP'); ?></h3>
					<p><?php echo JText::_('COM_COMMUNITY_EVENTS_ATTENDING_QUESTION'); ?></p>
                                        
					<select onchange="joms.events.submitRSVP(<?php echo $event->id;?>,this)">
                                                                                    <?php if($event->getMemberStatus($my->id)==0) { ?><option class="noResponse" selected="selected"><?php echo JText::_('COM_COMMUNITY_GROUPS_INVITATION_RESPONSE')?></option> <?php }?>
                                                                                    <option class="attend" <?php if($event->getMemberStatus($my->id) == COMMUNITY_EVENT_STATUS_ATTEND){echo "selected='selected'"; }?> value="<?php echo COMMUNITY_EVENT_STATUS_ATTEND; ?>"><?php echo JText::_('COM_COMMUNITY_EVENTS_RSVP_ATTEND')?></option>
                                                                                    <option class="notAttend" <?php if($event->getMemberStatus($my->id) >= COMMUNITY_EVENT_STATUS_WONTATTEND ){echo "selected='selected'"; }?> value="<?php echo COMMUNITY_EVENT_STATUS_WONTATTEND; ?>"><?php echo JText::_('COM_COMMUNITY_EVENTS_RSVP_NOT_ATTEND')?></option>
					</select>
					<div class="clr"></div>
				</div>
				<?php }?>
				<!-- Event Response -->

				<div id="community-event-members" class="cModule">

					<h3><?php echo JText::sprintf('COM_COMMUNITY_EVENTS_CONFIRMED_GUESTS'); ?></h3>
					<?php if($eventMembersCount>0){ ?>
						<div class="app-box-content">
							<ul class="cResetList cThumbList clrfix">
								<?php
								if($eventMembers) {
									foreach($eventMembers as $member) {
								?>
									<li>
										<a href="<?php echo CUrlHelper::userLink($member->id); ?>">
											<img border="0" height="45" width="45" class="cAvatar jomNameTips" src="<?php echo $member->getThumbAvatar(); ?>" title="<?php echo cAvatarTooltip($member);?>" alt="" />
										</a>
									</li>
								<?php
									}
								}
								?>
							</ul>
						</div>
						<div class="app-box-footer">
							<a href="<?php echo $handler->getFormattedLink('index.php?option=com_community&view=events&task=viewguest&eventid=' . $event->id . '&type='.COMMUNITY_EVENT_STATUS_ATTEND );?>">
								<?php echo JText::_('COM_COMMUNITY_VIEW_ALL');?> (<?php echo $eventMembersCount; ?>)
							</a>
							<?php if( ( ($isEventGuest && ($event->allowinvite)) || $isMine || $isCommunityAdmin || $isAdmin ) && $handler->hasInvitation() && $handler->isExpired()) { ?>
								<span style="float:right;"><?php echo $inviteHTML; ?></span>
							<?php } ?>
						</div>
					<?php } 
					else
					echo JText::_('COM_COMMUNITY_EVENTS_NO_USER_ATTENDING_MESSAGE')
					?>
				</div>


				<!-- begin: map -->
				<?php if( $config->get('eventshowmap') && ( $handler->isAllowed() || $event->permission != COMMUNITY_PRIVATE_EVENT ) ) {	?>
					<?php
					CFactory::load('libraries', 'mapping');
					if(CMapping::validateAddress($event->location)){
						?>
						<div id="community-event-map" class="cModule">
							<h3><?php echo JText::_('COM_COMMUNITY_MAP_LOCATION');?></h3>
							<div class="app-box-content event-description">
								<!-- begin: dynamic map -->
								<?php echo CMapping::drawMap('event-map', $event->location); ?>
								<div id="event-map" style="height:210px;width:100%;margin:5px 0;">
									<?php echo JText::_('COM_COMMUNITY_MAPS_LOADING'); ?>
								</div>
								<!-- end: dynamic map -->
								<?php echo CMapping::getFormatedAdd($event->location); ?>
							</div>
							<div class="app-box-footer">
								<a href="http://maps.google.com/?q=<?php echo urlencode($event->location); ?>" target="_blank"><?php echo JText::_('COM_COMMUNITY_EVENTS_FULL_MAP'); ?></a>
							</div>
						</div>
					<?php } ?>
				<?php } ?>
				<!-- end: map -->
                            
		<?php $this->renderModules( 'js_side_top' ); ?>
				<?php $this->renderModules( 'js_events_side_top' ); ?>
				<!-- Event Menu -->
				<?php if($memberStatus != COMMUNITY_EVENT_STATUS_BLOCKED) { ?>
				<div id="community-event-action" class="cModule">
					<h3><?php echo JText::_('COM_COMMUNITY_EVENTS_OPTION'); ?></h3>
						<div class="app-box-content">
						<!-- Event Menu List -->
						<ul class="event-menus clrfix">

								<?php if( $handler->showPrint() ) { ?>
								<!-- Print Event -->
								<li class="event-menu">
										<a class="event-print" href="javascript:void(0)" onclick="window.open('<?php echo $handler->getFormattedLink('index.php?option=com_community&view=events&task=printpopup&eventid='.$event->id); ?>','', 'menubar=no,width=600,height=700,toolbar=no');"><?php echo JText::_('COM_COMMUNITY_EVENTS_PRINT');?></a>
								</li>
								<?php } ?>

								<?php if( $handler->showExport() && $config->get('eventexportical') ) { ?>
								<!-- Export Event -->
								<li class="event-menu">
										<a class="event-export-ical" href="<?php echo $handler->getFormattedLink('index.php?option=com_community&view=events&task=export&format=raw&eventid=' . $event->id); ?>" ><?php echo JText::_('COM_COMMUNITY_EVENTS_EXPORT_ICAL');?></a>
								</li>
								<?php } ?>

								<?php if( (!$isEventGuest) && ($event->permission == COMMUNITY_PRIVATE_EVENT) && (!$waitingApproval)) { ?>
								<!-- Join Event -->
								<li class="event-menu">
										<a class="event-join" href="javascript:void(0);" onclick="javascript:joms.events.join('<?php echo $event->id;?>');"><?php echo JText::_('COM_COMMUNITY_EVENTS_INVITE_REQUEST'); ?></a>
								</li>
								<?php } ?>

								<?php if( (!$isMine) && !($waitingRespond) && (COwnerHelper::isRegisteredUser()) ) { ?>
								<!-- Leave Event -->
								<li class="event-menu important">
										<a class="event-leave" href="javascript:void(0);" onclick="joms.events.leave('<?php echo $event->id;?>');"><?php echo JText::_('COM_COMMUNITY_EVENTS_IGNORE');?></a>
								</li>
								<?php } ?>
						</ul>
						<!-- Event Menu List -->

						</div>
				</div>
				<!-- end #community-event-action -->
                                
				<?php } ?>
				<!-- Event Menu -->

		<?php $this->renderModules( 'js_events_side_bottom' ); ?>
		<?php $this->renderModules( 'js_side_bottom' ); ?>
		</div>
		<!-- end: .cSidebar -->

		<!-- begin: .cMain -->
		<div class="cMain clrfix">

	<?php if( $isInvited ){ ?>
	<div id="events-invite-<?php echo $event->id; ?>" class="com-invitation-msg">
		<div class="com-invite-info">
			<?php echo JText::sprintf( 'COM_COMMUNITY_EVENTS_YOUR_INVITED', $join ); $test = 1; ?><br />
			<?php echo JText::sprintf( (CStringHelper::isPlural($friendsCount)) ? 'COM_COMMUNITY_EVENTS_FRIEND' : 'COM_COMMUNITY_EVENTS_FRIEND_MANY', $friendsCount ); ?>
		</div>
		<div class="com-invite-action">
			<?php echo JText::_( 'COM_COMMUNITY_EVENTS_RSVP_NOTIFICATION' ) . JText::_('COM_COMMUNITY_OR'); ?>
			<a href="javascript:void(0);" onclick="jax.call('community','events,ajaxRejectInvitation','<?php echo $event->id; ?>');">
				<?php echo JText::_('COM_COMMUNITY_EVENTS_REJECT'); ?>
			</a>
		</div>
	</div>
	<?php } ?>

		<div class="event-top">
				<!-- Event Top: Event Left -->
				<div class="event-left">
						<!-- Event Avatar -->
						<div id="community-event-avatar" class="event-avatar" onMouseOver="joms.jQuery('.rollover').toggle()" onmouseout="joms.jQuery('.rollover').toggle()">
								<img src="<?php echo $event->getAvatar( 'avatar' ); ?>" border="0" alt="<?php echo $this->escape($event->title);?>" />
								<!-- Group Buddy -->
								<?php if( $isAdmin && !$isMine ) { ?>
									<div class="cadmin tag-this" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_USER_ADMIN'); ?>">
											<?php echo JText::_('COM_COMMUNITY_GROUPS_USER_ADMIN'); ?>
									</div>
								<?php } else if( $isMine || COwnerHelper::isCommunityAdmin()) { ?>
									<div class="cowner tag-this" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_USER_CREATOR'); ?>">
											<?php echo JText::_('COM_COMMUNITY_GROUPS_USER_CREATOR'); ?>
									</div>
									<div class="rollover"><a href="javascript:void(0)" onclick="joms.photos.uploadAvatar('event','<?php echo $event->id?>')"><?php echo JText::_('COM_COMMUNITY_CHANGE_AVATAR')?></a></div>
								<?php } ?>
								<!-- Group Buddy -->
						</div>
						<!-- Event Avatar -->
				</div>
				<!-- Event Top: Event Left -->                                
                                
				<div class="event-category">
					<div class="clabel"><?php echo JText::_('COM_COMMUNITY_EVENTS_CATEGORY'); ?>:</div>
					<div class="cdata" id="community-event-data-category">
						<a href="<?php echo CRoute::_('index.php?option=com_community&view=events&categoryid=' . $event->catid);?>"><?php echo JText::_( $event->getCategoryName() ); ?></a>
					</div>
				</div>

				<!-- Event Top: Event Main -->
				<div class="event-main">
					<!-- Event Approval -->
					<div class="event-approval">
						<?php if( ( $isMine || $isAdmin || $isCommunityAdmin) && ( $unapproved > 0 ) ) { ?>
						<div class="info">
							<a class="friend" href="<?php echo $handler->getFormattedLink('index.php?option=com_community&view=events&task=viewguest&type='.COMMUNITY_EVENT_STATUS_REQUESTINVITE.'&eventid=' . $event->id);?>">
								<?php echo JText::sprintf((CStringHelper::isPlural($unapproved)) ? 'COM_COMMUNITY_EVENTS_PENDING_INVITE_MANY'	 :'COM_COMMUNITY_EVENTS_PENDING_INVITE' , $unapproved ); ?>
							</a>
						</div>
						<?php } ?>

						<?php if( $waitingApproval ) { ?>
						<div class="info">
							<span class="jsIcon1 icon-waitingapproval"><?php echo JText::_('COM_COMMUNITY_EVENTS_APPROVEL_WAITING'); ?></span>
						</div>
						<?php }?>
					</div>

						<!-- Event Information -->
						<div id="community-event-info" class="event-info">
							<div class="cparam event-created">
                                                                <div class="clabel"><?php echo JText::_('COM_COMMUNITY_EVENTS_TIME')?></div>
								<div class="cdata"><?php echo ($allday) ? JText::sprintf('COM_COMMUNITY_EVENTS_ALLDAY_DATE',$event->startdateHTML) : JText::sprintf('COM_COMMUNITY_EVENTS_DURATION',$event->startdateHTML,$event->enddateHTML); ?></div>
								<?php if( $config->get('eventshowtimezone') ) { ?>
									<div class="small"><?php echo $timezone; ?></div>
								<?php } ?>
							</div>

					    		<!-- Location info -->
							<div class="cparam event-location">
								<div class="clabel"><?php echo JText::_('COM_COMMUNITY_EVENTS_LOCATION');?></div>
								<div class="cdata" id="community-event-data-location"><a href="http://maps.google.com/?q=<?php echo urlencode($event->location); ?>" target="_blank"><?php echo $event->location; ?></a></div>
							</div>

							<!--Event Summary-->
							<div class="cparam event-summary">
								<div class="clabel"><?php echo JText::_('COM_COMMUNITY_EVENTS_VIEW_SUMMARY');?></div>
								<div class="cdata"><?php echo $event->summary; ?></div>
							</div>

							<!--Event Admins-->
							<div class="cparam event-owner">
								<div class="clabel"><?php echo JText::_('COM_COMMUNITY_EVENTS_ADMINS')?></div>
								<div class="cdata"><?php echo $adminsList;?></div>
							</div>

							<!-- Number of tickets -->
							<div class="cparam event-tickets">
								<div class="clabel"><?php echo JText::_('COM_COMMUNITY_EVENTS_SEATS_AVAILABLE');?></div>
								<div class="cdata">
									<?php
										if($event->ticket)
											echo JText::sprintf('COM_COMMUNITY_EVENTS_TICKET_STATS', $event->ticket, $eventMembersCount, ($event->ticket - $eventMembersCount));
										else
											echo JText::sprintf('COM_COMMUNITY_EVENTS_UNLIMITED_SEAT');
										?>
									</div>
								</div>
						</div>
						<!-- Event Information -->
						<div style="clear: left;"></div>
				</div>
				<!-- start: Event Main -->

	<!-- Event Top: App Like -->
	<div class="jsApLike">
		<span id="like-container">
			<?php echo $likesHTML; ?>
		</span>
		<div class="clr"></div>
	</div>
	<!-- end: App Like -->
	
	<!-- Global Application Tab bar framework -->
	<div class="cTabsBar">
		<ul class="cResetList">
			<li <?php if($isEventGuest) {echo 'class="cTabCurrent"';} else {echo 'class="cTabDisabled"';} ?>><a href="javascript:void(0)"><?php echo JText::_('COM_COMMUNITY_FRONTPAGE_RECENT_ACTIVITIES');?></a></li>
			<li <?php if(!$isEventGuest) {echo 'class="cTabCurrent"';} ?>><a href="javascript:void(0)"><?php echo JText::_('COM_COMMUNITY_EVENTS_DETAIL');?></a></li>
			<!--li <?php if(!$isEventGuest) {echo 'class="cTabDisabled"';} ?>><a href="javascript:void(0)">Event Program</a></li-->
		</ul>
		<div class="clr"></div>
	</div>
	<!-- END: Global Application Tab bar framework -->
	
	<!-- START: Global Application Tab bar contents -->
	<div class="cTabsContentWrap">
			
			<!-- Tab 1: Activity Stream Container -->
			<?php if( $handler->isAllowed() ) { ?>
			<div class="cTabsContent  <?php if($isEventGuest) {echo 'cTabsContentCurrent';} ?>">
				<!-- Stream -->
				 <?php if($isEventGuest) { $status->render(); } ?>
				<div id="activity-stream-container">
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
							   joms.activities.getLatestAppContent(joms.jQuery('.cFeed-item').attr('id').substring(21),true); 
							}
						}
					</script>
					<?php endif; ?>
					
					<?php echo $streamHTML; ?>
				</div>
				<!-- end: stream -->
			</div>
			<?php } ?>
			<!-- Tab 1: END -->
			
			<!-- Tab 2: Event Details -->
			<div class="cTabsContent <?php if(!$isEventGuest) {echo 'cTabsContentCurrent';} ?>">
				<div class="event-desc">
					<?php 
					if( !CStringHelper::isHTML($event->description) )
					{
						echo CStringHelper::nl2br($event->description);
					}
					else
					{
						echo $event->description;
					} 
					?>
				</div>
			</div>
			<!-- Tab 2: END -->
			
			<!-- Tab 3: Event Program -->
			<!--div class="cTabsContent">
				Event Program
			</div-->
			<!-- Tab 3: END -->
			
		<div class="clr"></div>
	</div>
	<!-- END: Global Application Tab bar contents -->
	
		
	</div>


	</div>

</div>
<!-- end: .cLayout -->
</div>
<script type="text/javascript">
      joms.jQuery(function(){
        joms.jQuery("select").jomSelect();
      });
</script>
<?php if($editEvent) {?>
<script type="text/javascript">
	joms.events.edit();
</script>
<?php } ?>
