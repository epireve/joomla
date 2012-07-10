<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

$params	= $this->group->getParams();
?>

<script type="text/javascript">
	function jSelectUser_jform_user_id_to(id,name){
	    joms.jQuery("#sbox-window, #sbox-overlay").hide();
	    joms.jQuery("#creator_name").val(name);
	    joms.jQuery("#creator_id").val(id);
	}

	function js_Show(){
	    joms.jQuery("#sbox-window, #sbox-overlay").show();
	}
</script>

<form name="adminForm" id="adminForm" action="index.php?option=com_community" method="POST">
<table  width="100%" class="paramlist admintable" cellspacing="1">
	<tr>
		<td class="paramlist_key">
			<label for="name" class="label title jomTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_TITLE');?>::<?php echo JText::_('COM_COMMUNITY_GROUPS_TITLE_TIPS'); ?>">
				<?php echo JText::_('COM_COMMUNITY_GROUPS_TITLE'); ?>
			</label>
		</td>
		<td class="paramlist_value">
			<input type="text" name="name" value="<?php echo $this->group->name; ?>" style="width: 200px;" />
		</td>
	</tr>
	<tr>
		<td class="paramlist_key">
			<label for="avatar"><?php echo JText::_( 'COM_COMMUNITY_GROUPS_AVATAR' );?></label>
		</td>
		<td>
			<img src="<?php echo $this->group->getThumbAvatar();?>" />
		</td>
	</tr>
	<tr>
		<td class="paramlist_key">
			<label for="published"><?php echo JText::_( 'COM_COMMUNITY_PUBLISH' );?></label>
		</td>
		<td class="paramlist_value">
			<?php echo JHTML::_('select.booleanlist' , 'published' , null , $this->group->published , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
		</td>
	</tr>
	<tr>
		<td class="paramlist_key">
			<label for="creator"><?php echo JText::_( 'COM_COMMUNITY_GROUPS_CREATOR');?></label>
		</td>
		<td class="paramlist_value">
		<?php
		   

		    $creator	= CFactory::getUser( $this->group->ownerid );
		    ?>
			<div style="float: left;"><input type="text" name="creator-display" id="creator_name" value="<?php echo $creator->getDisplayName();?>" disabled="disabled"/></div>
			<div class="button2-left">
				<div class="blank">
					<a class="modal" title="<?php echo JText::_( 'Select a user');?>"  rel="{handler: 'iframe', size: {x: 750, y: 450}}" href="<?php echo $this->url; ?>">
					<?php echo JText::_( 'COM_COMMUNITY_GROUPS_SELECT_CREATOR');?>
					</a>
				</div>
			</div>
			<input type="hidden" name="creator" id="creator_id" value="<?php echo $creator->id;?>" />
		</td>
	</tr>
	<tr>
		<td class="paramlist_key">
			<label for="description" class="label title jomTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_DESCRIPTION');?>::<?php echo JText::_('COM_COMMUNITY_GROUPS_BODY_TIPS');?>">
				<?php echo JText::_('COM_COMMUNITY_GROUPS_DESCRIPTION');?>
			</label>
		</td>
		<td class="paramlist_value">
			<textarea rows="5" style="width: 200px;" name="description"><?php echo $this->group->description;?></textarea>
		</td>
	</tr>
	<tr>
		<td class="paramlist_key">
			<label for="categoryid" class="label title jomTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_CATEGORY');?>::<?php echo JText::_('COM_COMMUNITY_GROUPS_CATEGORY_TIPS');?>">
				<?php echo JText::_('COM_COMMUNITY_GROUPS_CATEGORY');?>
			</label>
		</td>
		<td class="paramlist_value">
		<?php
		$select	= '<select name="categoryid">';

		$select	.= ( $this->group->categoryid == 0 ) ? '<option value="0" selected="true">' : '<option value="0">';
		$select .= JText::_('COM_COMMUNITY_GROUPS_SELECT_CATEGORY') . '</option>';

		for( $i = 0; $i < count( $this->categories ); $i++ )
		{
			$selected	= ( $this->group->categoryid == $this->categories[$i]->id ) ? ' selected="true"' : '';
			$select	.= '<option value="' . $this->categories[$i]->id . '"' . $selected . '>' . $this->categories[$i]->name . '</option>';
		}
		$select	.= '</select>';
		
		echo $select;	
		?>
		</td>
	</tr>
	<tr>
		<td class="paramlist_key">
			<label class="label title jomTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_TYPE');?>::<?php echo JText::_('COM_COMMUNITY_GROUPS_APPROVAL_TIPS');?>">
				<?php echo JText::_('COM_COMMUNITY_GROUPS_TYPE'); ?>
			</label>
		</td>
		<td class="paramlist_value">
			<div>
				<input type="radio" name="approvals" id="approve-open" value="0"<?php echo ($this->group->approvals == COMMUNITY_PUBLIC_GROUP ) ? ' checked="checked"' : '';?> />
				<label for="approve-open" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUPS_OPEN');?></label>
			</div>
			<div style="margin-bottom: 10px;" class="small">
				<?php echo JText::_('COM_COMMUNITY_GROUPS_OPEN_DESCRITPION');?>
			</div>
			<div>
				<input type="radio" name="approvals" id="approve-private" value="1"<?php echo ($this->group->approvals == COMMUNITY_PRIVATE_GROUP ) ? ' checked="checked"' : '';?> />
				<label for="approve-private" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUPS_PRIVATE');?></label>
			</div>
			<div class="small">
				<?php echo JText::_('COM_COMMUNITY_GROUPS_PRIVATE_DESCRIPTION');?>
			</div>
		</td>
	</tr>
	<tr>
		<td class="paramlist_key">
			<label class="label title jomTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_DISCUSS_ORDER');?>::<?php echo JText::_('COM_COMMUNITY_GROUPS_ORDERING_TIPS');?>">
				<?php echo JText::_('COM_COMMUNITY_GROUPS_DISCUSS_ORDER'); ?>
			</label>
		</td>
		<td class="paramlist_value">
			<div>
				<input type="radio" name="discussordering" id="discussordering-lastreplied" value="0"<?php echo ($params->get('discussordering') == 0 ) ? ' checked="checked"' : '';?> />
				<label for="discussordering-lastreplied" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUPS_DISCUSS_ORDER_LAST_REPLIED');?></label>
			</div>
			<div>
				<input type="radio" name="discussordering" id="discussordering-creation" value="1"<?php echo ($params->get('discussordering') == 1 ) ? ' checked="checked"' : '';?> />
				<label for="discussordering-creation" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUPS_DISCUSS_ORDER_CREATION_DATE');?></label>
			</div>
		</td>
	</tr>
	<tr>
		<td class="paramlist_key">
			<label class="label title jomTips" title="<?php echo JText::_('COM_COMMUNITY_PHOTOS');?>::<?php echo JText::_('COM_COMMUNITY_GROUPS_PHOTO_PERMISSION_TIPS');?>">
				<?php echo JText::_('COM_COMMUNITY_PHOTOS'); ?>
			</label>
		</td>
		<td class="paramlist_value">
			<div>
				<input type="radio" name="photopermission" id="photopermission-disabled" value="-1"<?php echo ($params->get('photopermission') == GROUP_PHOTO_PERMISSION_DISABLE ) ? ' checked="checked"' : '';?> />
				<label for="photopermission-disabled" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUPS_PHOTO_DISABLED');?></label>
			</div>
			<div>
				<input type="radio" name="photopermission" id="photopermission-admin" value="1"<?php echo ($params->get('photopermission') == GROUP_PHOTO_PERMISSION_ADMINS ) ? ' checked="checked"' : '';?> />
				<label for="photopermission-admin" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUPS_PHOTO_UPLOAD_ALOW_ADMIN');?></label>
			</div>
			<div>
				<input type="radio" name="photopermission" id="photopermission-members" value="2"<?php echo ($params->get('photopermission') == GROUP_PHOTO_PERMISSION_ALL ) ? ' checked="checked"' : '';?> />
				<label for="photopermission-members" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUPS_PHOTO_UPLOAD_ALLOW_MEMBER');?></label>
			</div>
		</td>
	</tr>
	<tr>
		<td class="paramlist_key">
			<label for="grouprecentphotos-admin" class="label title jomTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_RECENT_PHOTO');?>::<?php echo JText::_('COM_COMMUNITY_GROUPS_RECENT_PHOTOS_TIPS');?>">
				<?php echo JText::_('COM_COMMUNITY_GROUPS_RECENT_PHOTO');?>
			</label>
		</td>
		<td class="paramlist_value">
			<input type="text" name="grouprecentphotos" id="grouprecentphotos-admin" size="1" value="<?php echo $params->get('grouprecentphotos', GROUP_PHOTO_RECENT_LIMIT);?>" />
		</td>
	</tr>
	<tr>
		<td class="paramlist_key">
			<label for="discussordering" class="label title jomTips" title="<?php echo JText::_('COM_COMMUNITY_VIDEOS');?>::<?php echo JText::_('COM_COMMUNITY_GROUPS_VIDEOS_PERMISSION_TIPS');?>">
				<?php echo JText::_('COM_COMMUNITY_VIDEOS'); ?>
			</label>
		</td>
		<td class="paramlist_value">
			<div>
				<input type="radio" name="videopermission" id="videopermission-disabled" value="-1"<?php echo ($params->get('videopermission') == GROUP_VIDEO_PERMISSION_DISABLE ) ? ' checked="checked"' : '';?> />
				<label for="videopermission-disabled" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUPS_VIDEO_DISABLED');?></label>
			</div>
			<div>
				<input type="radio" name="videopermission" id="videopermission-admin" value="1"<?php echo ($params->get('videopermission') == GROUP_VIDEO_PERMISSION_ADMINS ) ? ' checked="checked"' : '';?> />
				<label for="videopermission-admin" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUPS_VIDEO_UPLOAD_ALLOW_ADMIN');?>
			</div>
			<div>
				<input type="radio" name="videopermission" id="videopermission-members" value="2"<?php echo ($params->get('videopermission') == GROUP_VIDEO_PERMISSION_ALL ) ? ' checked="checked"' : '';?> />
				<label for="videopermission-members" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUPS_VIDEO_UPLOAD_ALLOW_MEMBER');?></label>
			</div>
		</td>
	</tr>
	<tr>
		<td class="paramlist_key">
			<label for="grouprecentvideos-admin" class="label title jomTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_RECENT_VIDEO');?>::<?php echo JText::_('COM_COMMUNITY_GROUPS_RECENT_VIDEO_TIPS');?>">
				<?php echo JText::_('COM_COMMUNITY_GROUPS_RECENT_VIDEO');?>
			</label>
		</td>
		<td class="paramlist_value">
			<input type="text" name="grouprecentvideos" id="grouprecentvideos-admin" size="1" value="<?php echo $params->get('grouprecentvideos', GROUP_VIDEO_RECENT_LIMIT);?>" />
		</td>
	</tr>
	<tr>
		<td class="paramlist_key">
			<label class="label title jomTips" title="<?php echo JText::_('COM_COMMUNITY_EVENTS');?>::<?php echo JText::_('COM_COMMUNITY_GROUP_EVENTS_PERMISSIONS');?>"><?php echo JText::_('COM_COMMUNITY_EVENTS');?></label>
		</td>
		<td class="paramlist_value">
			<div>
				<input type="radio" name="eventpermission" id="eventpermission-disabled" value="-1"<?php echo ($params->get('eventpermission') == GROUP_EVENT_PERMISSION_DISABLE ) ? ' checked="checked"' : '';?> />
				<label for="eventpermission-disabled" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUP_EVENTS_DISABLE');?></label>
			</div>
			<div>
				<input type="radio" name="eventpermission" id="eventpermission-admin" value="1"<?php echo ($params->get('eventpermission') == GROUP_EVENT_PERMISSION_ADMINS ) ? ' checked="checked"' : '';?> />
				<label for="eventpermission-admin" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUP_EVENTS_ADMIN_CREATION');?></label>
			</div>
			<div>
				<input type="radio" name="eventpermission" id="eventpermission-members" value="2"<?php echo ($params->get('eventpermission') == GROUP_EVENT_PERMISSION_ALL ) ? ' checked="checked"' : '';?> />
				<label for="eventpermission-members" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUP_EVENTS_MEMBERS_CREATION');?></label>
			</div>
		</td>
	</tr>
	<tr>
		<td class="paramlist_key">
			<label for="grouprecentevents-admin" class="label title jomTips" title="<?php echo JText::_('COM_COMMUNITY_GROUP_EVENTS');?>::<?php echo JText::_('COM_COMMUNITY_GROUPS_EVENT_TIPS');?>">
				<?php echo JText::_('COM_COMMUNITY_GROUP_EVENTS');?>
			</label>
		</td>
		<td class="paramlist_value">
			<input type="text" name="grouprecentevents" id="grouprecentevents-admin" size="1" value="<?php echo $params->get('grouprecentevents', GROUP_EVENT_RECENT_LIMIT);?>" />
		</td>
	</tr>
	<tr>
		<td class="paramlist_key">
			<label class="label title jomTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_NEW_MEMBER_NOTIFICATION');?>::<?php echo JText::_('COM_COMMUNITY_GROUPS_NEW_MEMBER_NOTIFICATION_TIPS');?>">
				<?php echo JText::_('COM_COMMUNITY_GROUPS_NEW_MEMBER_NOTIFICATION'); ?>
			</label>
		</td>
		<td class="paramlist_value">
			<div>
				<input type="radio" name="newmembernotification" id="newmembernotification-enable" value="1"<?php echo ($params->get('newmembernotification', '1') == true ) ? ' checked="checked"' : '';?> />
				<label for="newmembernotification-enable" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_ENABLE');?></label>
			</div>
			<div>
				<input type="radio" name="newmembernotification" id="newmembernotification-disable" value="0"<?php echo ($params->get('newmembernotification', '1') == false ) ? ' checked="checked"' : '';?> />
				<label for="newmembernotification-disable" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_DISABLE');?></label>
			</div>
		</td>
	</tr>
	<tr>
		<td class="paramlist_key">
			<label class="label title jomTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_JOIN_REQUEST_NOTIFICATION');?>::<?php echo JText::_('COM_COMMUNITY_GROUPS_JOIN_REQUEST_NOTIFICATION_TIPS');?>">
				<?php echo JText::_('COM_COMMUNITY_GROUPS_JOIN_REQUEST_NOTIFICATION'); ?>
			</label>
		</td>
		<td class="paramlist_value">
			<div>
				<input type="radio" name="joinrequestnotification" id="joinrequestnotification-enable" value="1"<?php echo ($params->get('joinrequestnotification', '1') == true ) ? ' checked="checked"' : '';?> />
				<label for="joinrequestnotification-enable" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_ENABLE');?></label>
			</div>
			<div>
				<input type="radio" name="joinrequestnotification" id="joinrequestnotification-disable" value="0"<?php echo ($params->get('joinrequestnotification', '1') == false ) ? ' checked="checked"' : '';?> />
				<label for="joinrequestnotification-disable" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_DISABLE');?></label>
			</div>
		</td>
	</tr>
	<tr>
		<td class="paramlist_key">
			<label class="label title jomTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_WALL_NOTIFICATION');?>::<?php echo JText::_('COM_COMMUNITY_GROUPS_WALL_NOTIFICATION_TIPS');?>">
				<?php echo JText::_('COM_COMMUNITY_GROUPS_WALL_NOTIFICATION'); ?>
			</label>
		</td>
		<td class="paramlist_value">
			<div>
				<input type="radio" name="wallnotification" id="wallnotification-enable" value="1"<?php echo ($params->get('wallnotification', '1') == true ) ? ' checked="checked"' : '';?> />
				<label for="wallnotification-enable" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_ENABLE');?></label>
			</div>
			<div>
				<input type="radio" name="wallnotification" id="wallnotification-disable" value="0"<?php echo ($params->get('wallnotification', '1') == false ) ? ' checked="checked"' : '';?> />
				<label for="wallnotification-disable" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_DISABLE');?></label>
			</div>
		</td>
	</tr>
</table>
<input type="hidden" name="view" value="groups" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="groupid" value="<?php echo $this->group->id; ?>" />
<input type="hidden" name="option" value="com_community" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>