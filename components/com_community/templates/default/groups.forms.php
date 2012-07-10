<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @params	categories Array	An array of categories
 */
defined('_JEXEC') or die();
?>
<form method="post" action="<?php echo CRoute::getURI(); ?>" id="createGroup" name="jsform-groups-create" class="community-form-validate">
<div id="community-groups-wrap">
<?php if($isNew) { ?>
	<p>
		<?php echo JText::_('COM_COMMUNITY_GROUPS_CREATE_DESC'); ?>
	</p>
	<?php
	if( $groupCreationLimit != 0 && $groupCreated/$groupCreationLimit>=COMMUNITY_SHOW_LIMIT) {
	?>
	<div class="hints">
		<?php echo JText::sprintf('COM_COMMUNITY_GROUPS_LIMIT_STATUS', $groupCreated, $groupCreationLimit ); ?>
	</div>
	<?php } ?>
<?php } ?>

	<table class="formtable" cellspacing="1" cellpadding="0">
	<?php echo $beforeFormDisplay;?>
	<!-- group name -->
	<tr>
		<td class="key">
			<label for="name" class="label">
				*<?php echo JText::_('COM_COMMUNITY_GROUPS_TITLE'); ?>
			</label>
		</td>
		<td class="value">
			<input name="name" id="name" maxlength="255" type="text" size="45" class="required inputbox title jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_TITLE_TIPS'); ?>" value="<?php echo $this->escape($group->name); ?>" />
		</td>
	</tr>
	<!-- group description -->
	<tr>
		<td class="key">
			<label for="description" class="label">
				*<?php echo JText::_('COM_COMMUNITY_GROUPS_DESCRIPTION');?>
			</label>
		</td>
		<td class="value">
                    <span class="jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_BODY_TIPS');?>">
			<?php if( $config->get( 'htmleditor' ) == 'none' && $config->getBool('allowhtml') ) { ?>
   				<div class="htmlTag"><?php echo JText::_('COM_COMMUNITY_HTML_TAGS_ALLOWED');?></div>
			<?php } ?>

			<?php
			if( !CStringHelper::isHTML($group->description)
				&& $config->get('htmleditor') != 'none'
				&& $config->getBool('allowhtml') )
			{
				$event->description = CStringHelper::nl2br($group->description);
			}
			?>

			<?php echo $editor->displayEditor( 'description',  $group->description , '95%', '350', '10', '20' , false ); ?>
                    </span>
		</td>
	</tr>
	<!-- group category -->
	<tr>
		<td class="key">
			<label for="categoryid" class="label">
				*<?php echo JText::_('COM_COMMUNITY_GROUPS_CATEGORY');?>
			</label>
		</td>
		<td class="value">
                    <span class="label jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_CATEGORY_TIPS');?>">
			<?php echo $lists['categoryid']; ?>
                    </span>
		</td>
	</tr>
	<!-- group type -->
	<tr>
		<td class="key">
		</td>
		<td>
                    <span class="value label jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_APPROVAL_TIPS');?>">
                            <input type="checkbox" name="approvals" id="approve-private" value="1"<?php echo ($group->approvals == COMMUNITY_PRIVATE_GROUP ) ? ' checked="checked"' : '';?> />
                            <label for="approve-private" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUPS_PRIVATE_LABEL');?></label>
                    </span>
		</td>
	</tr>
	
	
	<!-- group ordering -->
	<tr class="toggle" style="display:none">
		<td class="key">
		</td>
		<td class="value">
                    <span class="value label jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_ORDERING_TIPS');?>">
				<input type="checkbox" name="discussordering" id="discussordering-creation" value="1"<?php echo ($group->discussordering == 1 ) ? ' checked="checked"' : '';?> />
				<label for="discussordering-creation" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUPS_DISCUSS_ORDER_CREATION_DATE');?></label>
                    </span>
		</td>
	</tr>	
	
	<?php if($config->get('enablephotos') && $config->get('groupphotos')): ?>
	<!-- group photos -->
	<tr class="toggle" style="display:none">
		<td class="key">
			<label for="grouprecentphotos-admin" class="label">
				<?php echo JText::_('COM_COMMUNITY_GROUPS_RECENT_PHOTO');?>
			</label>
		</td>
		<td class="value">
            <span class="value jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_RECENT_PHOTOS_TIPS');?>">
				<input type="text" name="grouprecentphotos" id="grouprecentphotos-admin" size="1" value="<?php echo $group->grouprecentphotos;?>" />
            </span>
            <br/>
            <span class="value jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_PHOTO_PERMISSION_TIPS');?>">
            	<input type="checkbox" name="photopermission-admin" id="photopermission-admin" onclick="checkPhotoPermission()" value="1" <?php echo ($params->get('photopermission') >= 1) ? ' checked="checked"' : '';?> />
            	<label for="photopermission-admin" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUPS_PHOTO_UPLOAD_ALOW_ADMIN');?></label>
            <br/>
			<div id="photopermission" style="<?php echo ($params->get('photopermission') >= 1)? '':'display:none' ?>">
			    <input type="checkbox" name="photopermission-member" id="photopermission-member" value="1" <?php echo ( $params->get('photopermission') == GROUP_PHOTO_PERMISSION_ALL ) ? ' checked="checked"' : '';?> />
			    <label for="photopermission-member" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUPS_PHOTO_UPLOAD_ALLOW_MEMBER');?></label>
			</div>
			<script type="text/javascript">
			    function checkPhotoPermission(){
				if(joms.jQuery('#photopermission-admin').prop('checked')==true){
				    joms.jQuery('#photopermission').show();
				}else{
				    joms.jQuery('#photopermission').hide();
				}
			    }
			</script>                
			</span>
		</td>
	</tr>
	<?php endif;?>
        
	<?php if($config->get('enablevideos') && $config->get('groupvideos')): ?>
	<!-- group videos -->
	<tr class="toggle" style="display:none">
		<td class="key">
			<label for="grouprecentvideos-admin" class="label">
				<?php echo JText::_('COM_COMMUNITY_GROUPS_RECENT_VIDEO');?>
			</label>
		</td>
		<td class="value">
                    <span class="value jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_RECENT_VIDEO_TIPS');?>">
			<input type="text" name="grouprecentvideos" id="grouprecentvideos-admin" size="1" value="<?php echo $group->grouprecentvideos;?>" />
                    </span>
                    <br/>
                    <span class="value jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_VIDEOS_PERMISSION_TIPS');?>">
			
                        <input type="checkbox" name="videopermission-admin" onclick="checkVideoPermission()" id="videopermission-admin" value="1"<?php echo ($params->get('videopermission') >= 1) ? ' checked="checked"' : '';?> />
                        <label for="videopermission-admin" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUPS_VIDEO_UPLOAD_ALLOW_ADMIN');?></label>
                        <br/>
			<div id="videopermission" style="<?php echo ($params->get('videopermission') >= 1 ) ? '' : 'display:none' ?>">
			    <input type="checkbox" name="videopermission-member" id="videopermission-member" value="0" <?php echo ($params->get('videopermission') == GROUP_VIDEO_PERMISSION_ALL ) ? ' checked="checked"' : '';?> />
			    <label for="videopermission-member" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUPS_VIDEO_UPLOAD_ALLOW_MEMBER');?></label>
			</div>
			<script type="text/javascript">
			    function checkVideoPermission(){
				if(joms.jQuery('#videopermission-admin').prop('checked')==true){
				    joms.jQuery('#videopermission').show();
				}else{
				    joms.jQuery('#videopermission').hide();
				}
			    }
			</script>
                    </span>
		</td>
	</tr>
	<?php endif;?>

        <?php if($config->get('enableevents') && $config->get('group_events')): ?>
        <!-- Group event -->
	<tr class="toggle" style="display:none">
		<td class="key">
			<label for="grouprecentvideos-admin" class="label">
				<?php echo JText::_('COM_COMMUNITY_GROUP_EVENTS');?>
			</label>
		</td>
		<td class="value">
                    <span class="value jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_EVENT_TIPS');?>">
			<input type="text" name="grouprecentevents" id="grouprecentevents-admin" size="1" value="<?php echo $group->grouprecentevents;?>" />
                    </span>
                    <br/>
                    <span class="value jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_GROUP_EVENTS_PERMISSIONS');?>">
			
                        <input type="checkbox" name="eventpermission-admin" onclick="checkEventPermission()" id="eventpermission-admin" value="1" <?php echo ( $params->get('eventpermission') >= 1 ) ? ' checked="checked"' : '';?> />
                        <label for="eventpermission-admin" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUP_EVENTS_ADMIN_CREATION');?></label>
                        <br/>
			<div id="eventpermission" style="<?php echo ($params->get('eventpermission') >= 1 ) ? '' : 'display:none' ?>">
			    <input type="checkbox" name="eventpermission-member" id="eventpermission-member" value="0"<?php echo ($params->get('eventpermission') == GROUP_EVENT_PERMISSION_ALL ) ? ' checked="checked"' : '';?> />
			    <label for="eventpermission-member" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUP_EVENTS_MEMBERS_CREATION');?></label>
			</div>
			<script type="text/javascript">
			    function checkEventPermission(){
				if(joms.jQuery('#eventpermission-admin').prop('checked')==true){
				    joms.jQuery('#eventpermission').show();
				}else{
				    joms.jQuery('#eventpermission').hide();
				}
			    }
			</script>
                        
                    </span>
		</td>
	</tr>
        <?php endif;?>
	<tr class="toggle" style="display:none">
		<td class="key">
                    <label class="label"><?php echo JText::_('COM_COMMUNITY_GROUPS_NOTIFICATION');?></label>
		</td>
		<td class="value">
                    
                    <!-- NEW MEMBER -->
                    <span class="value jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_NEW_MEMBER_NOTIFICATION_TIPS');?>">
                        
                        <input type="checkbox" name="newmembernotification" id="newmembernotification-enable" value="1"<?php echo ($params->get('newmembernotification', '1') == true ) ? ' checked="checked"' : '';?> />
                        <label for="newmembernotification-enable" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUPS_NEW_MEMBER_NOTIFICATION');?></label>
			
                    </span>
                    <br/>
                    
                    <!-- JOIN REQUEST -->
                    <span class="value jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_JOIN_REQUEST_NOTIFICATION_TIPS');?>">
                        
                        <input type="checkbox" name="joinrequestnotification" id="joinrequestnotification-enable" value="1"<?php echo ($params->get('joinrequestnotification', '1') == true ) ? ' checked="checked"' : '';?> />
                        <label for="joinrequestnotification-enable" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUPS_JOIN_REQUEST_NOTIFICATION');?></label>
                                
                    </span>
                    <br/>
                    
                    <!-- WALL -->
                    <span class="value jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_WALL_NOTIFICATION_TIPS');?>">
                        
                        <input type="checkbox" name="wallnotification" id="wallnotification-enable" value="1"<?php echo ($params->get('wallnotification', '1') == true ) ? ' checked="checked"' : '';?> />
                        <label for="wallnotification-enable" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUPS_WALL_NOTIFICATION');?></label>
                                
                    </span>			
		</td>
	</tr>
	<?php if(! $isNew): ?>
	<tr class="toggle" style="display:none">
		<td class="key">
		</td>
		<td class="value">
                    <span class="value jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_GROUPS_REMOVE_ACTIVITIES_TIPS');?>">
                        
			<input type="checkbox" name="removeactivities" id="removeactivities" value="1" <?php echo ($params->get('removeactivities', '1') == true ) ? 'checked="checked"' : '';?>  />
                        <label for="removeactivities" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_GROUPS_REMOVE_ACTIVITIES');?></label>
                        <br/>
			<span class="small"><?php echo JText::_('COM_COMMUNITY_GROUPS_REMOVE_ACTIVITIES_TIPS');?></span>
                        
                    </span>
		</td>
	</tr>
	<?php endif;?>
	<!-- group hint -->
	<tr>
		<td class="key"></td>
		<td class="value"><span class="hints"><?php echo JText::_( 'COM_COMMUNITY_REGISTER_REQUIRED_FILEDS' ); ?></span></td>
	</tr>
	<?php echo $afterFormDisplay;?>

	<!-- Toggle buttons -->
	<tr class="toggleBtn">
	    <td class="key"></td>
	    <td class="value">
		    <a id="js_Group-expand" class="js_Group-expandLink" href="javascript:void(0);"><?php echo JText::_('COM_COMMUNITY_GROUPS_ADVANCED_OPTIONS'); ?></a>
	    </td>
	</tr>
	
	<!-- group buttons -->
	<tr>
		<td class="key"></td>
		<td class="value">
			<?php if($isNew): ?>
			<input name="action" type="hidden" value="save" />
			<?php endif;?>
			<input type="hidden" name="groupid" value="<?php echo $group->id;?>" />
			<input type="submit" value="<?php echo ($isNew) ? JText::_('COM_COMMUNITY_GROUPS_CREATE_GROUP') : JText::_('COM_COMMUNITY_SAVE_BUTTON');?>" class="button validateSubmit" />
			<input type="button" class="button" onclick="history.go(-1);return false;" value="<?php echo JText::_('COM_COMMUNITY_CANCEL_BUTTON');?>" /> 
			<?php echo JHTML::_( 'form.token' ); ?> 
		</td>
	</tr>
	</table>

</div>

</form>
<script type="text/javascript">
	cvalidate.init();
	cvalidate.setSystemText('REM','<?php echo addslashes(JText::_("COM_COMMUNITY_ENTRY_MISSING")); ?>');
	cvalidate.setMaxLength('#createGroup #description', 65000);

	joms.jQuery('#js_Group-expand').click(function() {
		joms.jQuery('.toggle').toggle('slow');
		joms.jQuery('.toggleBtn').remove();
	});
</script>