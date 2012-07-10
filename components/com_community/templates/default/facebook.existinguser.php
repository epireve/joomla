<?php
defined('_JEXEC') or die();
?>
<div>
	<img src="<?php echo $my->getThumbAvatar();?>" border="0" style="border: 1px solid #000; float: left; margin: 5px;line-height:0;" />
	<div><?php echo JText::sprintf('COM_COMMUNITY_FACEBOOK_SUCCESS_LOGIN' , $userInfo['name'] );?></div>
	<div class="clr"></div>
</div>
<div style="padding: 5px;">
	<?php
	if( $config->get('fbconnectupdatestatus') )
	{
	?>
	<label class="lblcheck"><input type="checkbox" checked="checked" value="1" name="importstatus" id="importstatus" /><?php echo JText::_('COM_COMMUNITY_IMPORT_PROFILE_STATUS');?></label>
	<?php
	}
	?>
	<label class="lblcheck"><input type="checkbox" checked="checked" value="1" name="importavatar" id="importavatar" /><?php echo JText::_('COM_COMMUNITY_IMPORT_PROFILE_AVATAR');?></label>
</div>
