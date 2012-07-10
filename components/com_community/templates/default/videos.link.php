<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();
?>
<script type="text/javascript" src="<?php echo JURI::root(); ?>components/com_community/assets/validate-1.5.pack.js"></script>
<form name="linkVideo" id="linkVideo" class="community-form-validate" method="post" action="<?php echo CRoute::_('index.php?option=com_community&view=videos&task=link');?>">

<table class="cWindowForm" cellspacing="1" cellpadding="0">


<!-- video URL -->
<tr>
	<td class="cWindowFormKey">
		<label for="videoLinkUrl" class="label title">
			*<?php echo JText::_('COM_COMMUNITY_VIDEOS_LINK_URL');?>
		</label>
	</td>
	<td class="cWindowFormVal">
		<input type="text" id="videoLinkUrl" name="videoLinkUrl" class="inputbox required" value="" />
	</td>
</tr>

<!-- location -->
<?php if ($enableLocation) { ?>
<tr>
	<td class="cWindowFormKey">
		<label for="location" class="label title">
			<?php echo JText::_('COM_COMMUNITY_VIDEOS_LOCATION');?>
		</label>
	</td>
	<td class="cWindowFormVal">
		<input name="location" id="location" type="text" size="35" value ="" class="inputbox"/>
		<div class="small"><?php echo JText::_('COM_COMMUNITY_VIDEOS_LOCATION_DESCRIPTION'); ?></div>
	</td>
</tr>
<?php } ?>


<!-- video category -->
<tr>
	<td class="cWindowFormKey">
		<label for="category" class="label title">
			<?php echo JText::_('COM_COMMUNITY_VIDEOS_CATEGORY');?>
		</label>
	</td>
	<td class="cWindowFormVal">
		<?php echo $list['category']; ?>
	</td>
</tr>

<?php if ($creatorType != VIDEO_GROUP_TYPE) { ?>
<!-- video privacy -->
<tr>
	<td class="cWindowFormKey">
		<label class="label title">
			<?php echo JText::_('COM_COMMUNITY_VIDEOS_WHO_CAN_SEE');?>
		</label>
	</td>
	<td class="cWindowFormVal">
		<?php echo CPrivacy::getHTML( 'permissions', $permissions, COMMUNITY_PRIVACY_BUTTON_LARGE ); ?>
	</td>
</tr>
<?php }?>
<tr>
	<td class="key"></td>
	<td class="value"><span class="hints"><?php echo JText::_( 'COM_COMMUNITY_REGISTER_REQUIRED_FILEDS' ); ?></span></td>
</tr>
	<?php if($videoUploadLimit > 0 && $videoUploaded/$videoUploadLimit>=COMMUNITY_SHOW_LIMIT) {?>
<tr>
	<td class="key"></td>
	<td class="value"><div class="hints"><?php echo JText::sprintf('COM_COMMUNITY_VIDEOS_UPLOAD_LIMIT_STATUS', $videoUploaded, $videoUploadLimit ); ?></div></td>
</tr>
	<?php }?>
</table>

<input type="hidden" name="creatortype" value="<?php echo $creatorType; ?>" />
<input type="hidden" name="groupid" value="<?php echo $groupid; ?>" />
<?php echo JHTML::_( 'form.token' ); ?>

</form>

<script type="text/javascript">
	cvalidate.init();
	cvalidate.setSystemText('REM','<?php echo addslashes(JText::_("COM_COMMUNITY_ENTRY_MISSING")); ?>');
</script>
