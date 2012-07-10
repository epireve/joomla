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


<form method="post" action="<?php echo CRoute::getURI();?>" name="jsform-profile-privacy">

<div class="ctitle"><h2><?php echo JText::_('COM_COMMUNITY_EDIT_YOUR_PRIVACY');?></h2></div>
<p><?php echo JText::_('COM_COMMUNITY_EDIT_PRIVACY_DESCRIPTION');?></p>

<table class="formtable" cellspacing="1" cellpadding="0">
<?php echo $beforeFormDisplay;?>
<!-- profile privacy -->
<tr>
	<td class="key" style="width: 200px;">
		<label class="label"><?php echo JText::_('COM_COMMUNITY_PRIVACY_PROFILE_FIELD');?></label>
	</td>
	<td class="privacyc"><?php echo CPrivacy::getHTML( 'privacyProfileView' , $params->get( 'privacyProfileView' ) , COMMUNITY_PRIVACY_BUTTON_LARGE , array( 'public' => true , 'members' => true , 'friends' => true , 'self' => false ) ); ?></td>
	<td></td>
</tr>


<!-- friends privacy -->
<tr>
	<td class="key" style="width: 200px;">
		<label class="label"><?php echo JText::_('COM_COMMUNITY_PRIVACY_FRIENDS_FIELD'); ?></label>
	</td>
	<td class="privacy"><?php echo CPrivacy::getHTML( 'privacyFriendsView' , $params->get( 'privacyFriendsView' ) , COMMUNITY_PRIVACY_BUTTON_LARGE ); ?></td>
	<td></td>
</tr>


<!-- photos privacy -->
<?php if($config->get('enablephotos')): ?>
<tr>
	<td class="key" style="width: 200px;">
		<label class="label"><?php echo JText::_('COM_COMMUNITY_PRIVACY_PHOTOS_FIELD'); ?></label>
	</td>
	<td class="privacy"><?php echo CPrivacy::getHTML( 'privacyPhotoView' , $params->get( 'privacyPhotoView' ) , COMMUNITY_PRIVACY_BUTTON_LARGE ); ?></td>
	<td class="value"><input type="checkbox" name="resetPrivacyPhotoView" /> <?php echo JText::_('COM_COMMUNITY_PHOTOS_PRIVACY_APPLY_TO_ALL'); ?></td>
</tr>
<?php endif;?>

<!-- videos privacy -->
<?php if($config->get('enablevideos')): ?>
<tr>
	<td class="key" style="width: 200px;">
		<label class="label"><?php echo JText::_('COM_COMMUNITY_PRIVACY_VIDEOS_FIELD'); ?></label>
	</td>
	<td class="privacy"><?php echo CPrivacy::getHTML( 'privacyVideoView' , $params->get( 'privacyVideoView' ) , COMMUNITY_PRIVACY_BUTTON_LARGE ); ?></td>
	<td class="value"><input type="checkbox" name="resetPrivacyVideoView" /> <?php echo JText::_('COM_COMMUNITY_VIDEOS_PRIVACY_RESET_ALL'); ?></td>
</tr>
<?php endif; ?>


<?php if( $config->get( 'enablegroups' ) ){ ?>
<!-- groups privacy -->
<tr>
	<td class="key" style="width: 200px;">
		<label class="label"><?php echo JText::_('COM_COMMUNITY_PRIVACY_GROUPS_FIELD'); ?></label>
	</td>
	<td class="privacy"><?php echo CPrivacy::getHTML( 'privacyGroupsView' , $params->get( 'privacyGroupsView' ) , COMMUNITY_PRIVACY_BUTTON_LARGE ); ?></td>
	<td></td>
</tr>
<?php } ?>
</table>


<div class="ctitle"><h2><?php echo JText::_('COM_COMMUNITY_EDIT_EMAIL_PRIVACY'); ?></h2></div>

<table class="formtable" cellspacing="1" cellpadding="0">

<?php
if( $config->get('privacy_search_email') == 1 )
{
?>
<tr>
	<td class="key" style="width: 200px;">
		<input type="hidden" name="search_email" value="0" />
		<input type="checkbox" value="1" id="email-email-yes" name="search_email" <?php if($my->get('_search_email') == 1) { ?>checked="checked" <?php } ?>/>
	</td>
	<td class="value">
		<label for="search_email"><?php echo JText::_('COM_COMMUNITY_PRIVACY_EMAIL'); ?></label>
	</td>
</tr>
</tr>
<?php
}
?>
<!-- Start New email preference -->
<?php
	$isadmin = COwnerHelper::isCommunityAdmin();
	foreach($emailtypes->getEmailTypes() as $group){
		if ($emailtypes->isAdminOnlyGroup($group->description) && !$isadmin) {
			continue;
		}
?>
<tr>
	<td class="key" style="width: 200px;"><h3><?php echo JText::_($group->description); ?></h3>  
	</td>

	<?php foreach($group->child as $id => $type){
		
		if($type->adminOnly && !$isadmin) continue;

		$emailset = $params->get($id, $config->get($id));
?>
	<tr>
		<td class="key" style="width: 200px;">
		<input type="hidden" name="<?php echo $id; ?>" value="0" />
		<input id="<?php echo $id; ?>" type="checkbox" name="<?php echo $id; ?>" value="1" <?php if( $emailset == 1) echo 'checked="checked"'; ?> />
		</td>
		<td class="value">
		<label for="<?php echo $id; ?>"><?php echo JText::_($type->description); ?></label>    
		</td>
	</tr>
<?php		
	}
	}
?>
</tr>

<!-- End New email preference -->

<?php echo $afterFormDisplay;?>
<tr>
	<td class="key"></td>
	<td class="value">
		<input type="hidden" value="save" name="action" />
		<input type="submit" class="button" value="<?php echo JText::_('COM_COMMUNITY_SAVE_BUTTON'); ?>" />
	</td>
</tr>
</table>

</form>

<div id="community-banlists-wrap" style="padding-top: 20px;">
	
	<div id="community-banlists-news-items" class="app-box" style="width: 100%; float: left;margin-top: 0px;">
		<div class="ctitle"><h2><?php echo JText::_('COM_COMMUNITY_MY_BLOCKED_LIST');?></h2></div>
		<ul id="friends-list">
		<?php
			foreach( $blocklists as $row )
			{
				$user	= CFactory::getUser( $row->blocked_userid );
		?>
			<li id="friend-<?php echo $user->id;?>" class="friend-list">
				<span><img width="45" height="45" src="<?php echo $user->getThumbAvatar();?>" alt="" /></span>
				<span class="friend-name">
					<?php echo $user->getDisplayName(); ?>
					<a class="remove" href="javascript:void(0);" onclick="joms.users.unBlockUser('<?php echo $row->blocked_userid;  ?>','privacy');">
					   <?php echo JText::_('COM_COMMUNITY_BLOCK'); ?>
					</a>
				</span>
			</li>
		<?php
			}
		?>
		</ul>
	</div>
</div>
<script type="text/javascript">
joms.jQuery( document ).ready( function(){
  	joms.privacy.init();
});
</script>
