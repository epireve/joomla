<?php
defined('_JEXEC') or die();
?>
<h2 style="text-decoration: underline;margin-bottom: 10px;"><?php echo JText::_('COM_COMMUNITY_NEW_MEMBER');?></h2>
<div style="margin-bottom: 5px;"><?php echo JText::_('COM_COMMUNITY_NEW_MEMBER_DESCRIPTION');?></div>
<table width="100%">
	<tr>
	    <td width="30%" valign="top"><label for="newname"><?php echo JText::_('COM_COMMUNITY_NAME');?></label></td>
	    <td><input type="text" id="newname" class="inputbox" size="30" value="<?php echo $userInfo['name'];?>" onblur="joms.connect.checkRealname(this.value);"/><div id="error-newname" class="small" style="display: none;color: red;"></div></td>
	</tr>
	<tr>
		<td valign="top"><label for="newusername"><?php echo JText::_('COM_COMMUNITY_USERNAME');?></label></td>
		<td><input type="text" id="newusername" class="inputbox required" size="30" onblur="joms.connect.checkUsername(this.value)"/><div id="error-newusername" class="small" style="display: none;color: red;"></div></td>
	</tr>
	<tr>
		<td valign="top"><label for="newemail"><?php echo JText::_('COM_COMMUNITY_EMAIL');?></label></td>
		<td><input type="text" id="newemail" value="<?php echo $userInfo['email'];?>" class="inputbox required" size="30" onblur="joms.connect.checkEmail(this.value);" /><div id="error-newemail" class="small" style="display: none;color: red;"></div></td>
	</tr>
</table>
<?php if (isset($profileTypes) && count($profileTypes) > 0) { ?>
<div class="jsProfileType">
	<div><a href="javascript: void(0);" class="fb-hideshow-profiletype" style="font-style: none;"><?php echo JText::_('Select a profile type:');?></a></div>
	<?php
		foreach($profileTypes as $profile)
		{
	?>
	<div class="fb-connect-profiletype" style="display:none;">
		<label class="lblradio-block" style="font-weight: 700;"><input type="radio" value="<?php echo $profile->id;?>" id="profile-<?php echo $profile->id;?>" name="profiletype" style="margin-right:5px" />
			<span>
				<?php echo $profile->name;?>
				<?php if (false) {//( $profile->approvals ){?>
				<sup><?php echo JText::_('COM_COMMUNITY_REQUIRE_APPROVAL');?></sup>
				<?php } ?>
			</span>

			<?php if( $default == $profile->id ){?>
			<br />
			<span style="margin-left: 25px; font-style: none;">
				<?php echo JText::_('COM_COMMUNITY_ALREADY_USING_THIS_PROFILE_TYPE');?>
			</span>
			<?php } ?>
			<br />
			<span style="margin-left: 25px; font-weight: normal;">
				<?php echo $profile->description;?>
			</span>
		</label>
	</div>
	<?php
		}
	?>
</div>
<?php } ?>
<script type="text/javascript">
	

joms.jQuery(document).ready(function() {
	joms.jQuery('.fb-hideshow-profiletype').click(function() {
		joms.jQuery('.fb-connect-profiletype').toggle();
		var cWindowContent = joms.jQuery('#cWindow #cWindowContent');
		var h = cWindowContent.outerHeight();

		var maxH = joms.jQuery(window).height() * 0.7;

		if (h > maxH) h = maxH;
		cWindowResize(h);
	})
})
</script>