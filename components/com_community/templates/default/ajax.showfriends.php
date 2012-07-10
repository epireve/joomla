
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
<?php
}
?>
</form>
</div>
