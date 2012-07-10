<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	album	An object of CTableAlbum
 */
defined('_JEXEC') or die();
?>
<form name="newalbum" id="newalbum" method="post" action="<?php echo CRoute::getURI(); ?>" class="community-form-validate">

<table class="formtable" cellspacing="1" cellpadding="0">
<?php echo $beforeFormDisplay;?>

<!-- name -->
<tr>
	<td class="key">
		<label for="name" class="label title">
			*<?php echo JText::_('COM_COMMUNITY_PHOTOS_ALBUM_NAME');?>
		</label>
	</td>
	<td class="value">
		<input type="text" id="name" name="name" class="required" size="35" value="<?php echo $this->escape($album->name); ?>" />
	</td>
</tr>
<!-- location -->
<?php if ($enableLocation) { ?>
<tr>
	<td class="key">
		<label for="location" class="label title">
			<?php echo JText::_('COM_COMMUNITY_PHOTOS_ALBUM_LOCATION');?>
		</label>
	</td>
	<td class="value">
		<input name="location" id="location" type="text" size="35" value ="<?php echo $this->escape($album->location); ?>"/>
		<div class="small"><?php echo JText::_('COM_COMMUNITY_PHOTOS_ALBUM_LOCATION_DESC'); ?></div>
	</td>
</tr>
<?php } ?>

<!-- description -->
<tr>
	<td class="key">
		<label for="description" class="label title">
			<?php echo JText::_('COM_COMMUNITY_PHOTOS_ALBUM_DESC');?>
		</label>
	</td>
	<td class="value">
		<textarea name="description" id="description" class="description"><?php echo $this->escape($album->description); ?></textarea>
	</td>
</tr>

<!-- permission -->
<?php if ($type == 'group') { ?>
<tr>
	<td class="key"></td>
	<td class="value"><span class="hints"><?php echo JText::_( 'COM_COMMUNITY_PHOTOS_GROUP_MEDIA_PRIVACY_TIPS' ); ?></span></td>
</tr>
<?php } else { ?>
<tr>
	<td class="cWindowFormKey">
		<label for="privacy" class="label title">
			<?php echo JText::_('COM_COMMUNITY_PHOTOS_PRIVACY_VISIBILITY');?>
		</label>
	</td>
	<td class="cWindowFormVal">
		<?php echo CPrivacy::getHTML( 'permissions', $permissions, COMMUNITY_PRIVACY_BUTTON_LARGE ); ?>
	</td>
</tr>
<?php } ?>

<!-- hint -->
<tr>
	<td class="key"></td>
	<td class="value"><span class="hints"><?php echo JText::_( 'COM_COMMUNITY_REGISTER_REQUIRED_FILEDS' ); ?></span></td>
</tr>
<?php echo $afterFormDisplay;?>

<!-- button -->
<tr>
	<td class="key"></td>
	<td class="value">
		<input type="hidden" name="albumid" value="<?php echo $album->id; ?>" />
		<input type="hidden" name="referrer" value="<?php echo $referrer; ?>" />
		<input type="hidden" name="type" value="<?php echo $type;?>" />
		<?php if(empty($album->id)) { ?>
		<input type="submit" class="button validateSubmit" value="<?php echo JText::_('COM_COMMUNITY_PHOTOS_CREATE_ALBUM_BUTTON');?>" />
		<?php } else { ?>
		<input type="submit" class="button validateSubmit" value="<?php echo JText::_('COM_COMMUNITY_PHOTOS_SAVE_ALBUM_BUTTON');?>" />
		<?php } ?>
		<input type="button" class="button" onclick="history.go(-1);return false;" value="<?php echo JText::_('COM_COMMUNITY_CANCEL_BUTTON');?>" />
		<?php echo JHTML::_( 'form.token' ); ?>	
	</td>
</tr>
</table>
</form>
<script type="text/javascript">
	joms.jQuery( document ).ready( function(){
    	joms.privacy.init();
	});
	cvalidate.init();
	cvalidate.setSystemText('REM','<?php echo addslashes(JText::_("COM_COMMUNITY_ENTRY_MISSING")); ?>');
</script>