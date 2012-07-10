
 <?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>
<div class="invitation-bg">
<form name="invitation-form" id="community-invitation-form">
<div id="invitation-error"></div>
<?php
if( $displayFriends )
{
?>

<script type="text/javascript">
joms.jQuery(document).ready(function() {
	//When page loads...
	joms.jQuery(".cTab").hide(); //Hide all content
	joms.jQuery(".cInvitationTab li:first").addClass("active").show(); //Activate first tab
	joms.jQuery(".cTab:first").show(); //Show first tab content

	//// //On Click Event
	joms.jQuery(".cInvitationTab li").click(function() {
	joms.jQuery(".cInvitationTab li").removeClass("active"); //Remove any "active" class
	joms.jQuery(this).addClass("active"); //Add "active" class to selected tab
	joms.jQuery(".cTab").hide(); //Hide all tab content
	var activeTab = joms.jQuery(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
	joms.jQuery(activeTab).fadeIn(); //Fade in the active ID content
	return false;
	});
});
</script>

<div class="friendSearchWrap">
	<input type="text" onkeyup="joms.friends.loadFriend(this.value,'<?php echo $callback;?>','<?php echo $cid;?>','0','<?php echo $limit;?>');" value="" placeholder="<?php echo JText::_('COM_COMMUNITY_INVITE_TYPE_YOUR_FRIEND_NAME');?>" name="friendsearch" id="friend-search-filter">
</div>

<ul class="cInvitationTab" >
	<li id="ctab-result" onclick="joms.invitation.showResult();"><a href="#community-invitation"><?php echo JText::_('COM_COMMUNITY_INVITATION_SEARCH_RESULT');?></a></li>
	<li id="ctab-selected" onclick="joms.invitation.showSelected();"><a href="#community-invited"><?php echo JText::_('COM_COMMUNITY_INVITATION_SELECTED_FRIENDS');?></a></li>
</ul>

<div id="cInvitationTabContainer">
	<div id="community-invitation" class="cTab clrfix">
		<ul id="community-invitation-list">			
		<!-- HERE -->
		</ul>
		<div id="community-invitation-loadmore">
			<a onClick="joms.friends.loadMoreFriend('<?php echo $callback;?>','<?php echo $cid;?>','0','<?php echo $limit;?>');" href="javascript:void(0)"><?php echo JText::_('load more');?> </a>
		</div>
	</div>
	<div id="community-invited" class="cTab clrfix">
		<ul id="community-invited-list">			
		<!-- HERE -->
		</ul>
	</div>
</div>

<div class="invitation-option">
<?php
}
if( $displayEmail )
{
?>
		<script type="text/javascript">
		joms.jQuery(document).ready(function() {	

		joms.jQuery("#selectAll,#unselectAll").hide();
		  joms.jQuery("#emailContainer").hide();
		  joms.jQuery("#emailToggle").click(function()
		  {
		    joms.jQuery("#emailContainer").slideToggle(300);
		  });
		
		  joms.jQuery(".cInvitationTab li").click(function() {
			if (joms.jQuery('#ctab-result').hasClass('active'))
				{
					joms.jQuery("#selectAll").show();
					joms.jQuery("#unselectAll").hide();
				}
			else if (joms.jQuery('#ctab-selected').hasClass('active'))
				{
					joms.jQuery("#unselectAll").show();
					joms.jQuery("#selectAll").hide();
				}
		  });
		});
		</script>

		<div class="textarea-label">
			<div id="emailToggle" ><?php echo JText::_('COM_COMMUNITY_INVITE_BY_EMAIL_TIPS');?></div>
          	<div class="textarea-label-right">					
				<a id="selectAll" onClick="joms.invitation.selectAll('#community-invitation-list');" href="javascript:void(0)"><?php echo JText::_('COM_COMMUNITY_SELECT_ALL');?></a> 
				<a id="unselectAll" onClick="joms.invitation.selectNone('#community-invited-list');" href="javascript:void(0)"><?php echo JText::_('COM_COMMUNITY_UNSELECT_ALL');?></a>
			</div>
		</div>
		<div id="emailContainer" class="option">
			<div class="textarea-wrap">
				<textarea name="emails" id="emails"></textarea>
			</div>
		</div>
<?php
}
?>
		<div class="option invitation-message-container">
			<div id="messageToggle" class="textarea-label">
				<?php echo JText::_('COM_COMMUNITY_INVITE_PERSONAL_MESSAGE');?>
			</div>
			<div id="messageContainer" class="textarea-wrap">
				<textarea name="message" id="message"></textarea>
			</div>
		</div>
	</div>
</form>
</div>
