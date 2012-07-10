<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	my	Current browser's CUser object.
 **/
defined('_JEXEC') or die();
?>

<?php if ($firstLogin) { ?>
<div class="skipLink">
	<a href="<?php echo $skipLink; ?>"class="saveButton"><span><?php echo JText::_('COM_COMMUNITY_SKIP_UPLOAD_AVATAR'); ?></span></a>
</div>
<?php } ?>

<!-- JS and CSS for imagearea selection -->
<link rel="stylesheet" type="text/css" href="<?php echo JURI::root(); ?>components/com_community/assets/imgareaselect/css/imgareaselect-default.css" />
<script type="text/javascript" src="<?php echo JURI::root(); ?>components/com_community/assets/imgareaselect/scripts/jquery.imgareaselect.pack.js"></script>


<div class="cModule">
	<form name="jsform-profile-uploadavatar" action="<?php echo CRoute::getURI(); ?>" id="uploadForm" method="post" enctype="multipart/form-data">
    	<input class="inputbox button" type="file" id="file-upload" name="Filedata" />
		<input class="button" size="30" type="submit" id="file-upload-submit" value="<?php echo JText::_('COM_COMMUNITY_BUTTON_UPLOAD_PICTURE'); ?>">
	    <input type="hidden" name="action" value="doUpload" />
	    <input type="hidden" name="profileType" value="<?php echo $profileType;?>" />
	</form>
	<?php if( $uploadLimit != 0 ){ ?>
	<p class="info"><?php echo JText::sprintf('COM_COMMUNITY_MAX_FILE_SIZE_FOR_UPLOAD' , $uploadLimit ); ?></p>
	<?php } ?>
        <?php if (!$firstLogin) {?>
	<div style="margin-top: 15px;"><a href="javascript:void(0);" onclick="joms.profile.confirmRemoveAvatar();"><?php echo JText::_('COM_COMMUNITY_REMOVE_PROFILE_PICTURE');?></a></div>
        <?php } ?>
</div>


<div class="cModule avatarPreview leftside">	
	<h3><?php echo JText::_('COM_COMMUNITY_PICTURE_LARGE_HEADING');?></h3>
	
	<div id="imagePreview" class="imagePreview">
		<img id="large-profile-pic" src="<?php echo $user->getAvatar();?>" alt="<?php echo JText::_('COM_COMMUNITY_LARGE_PICTURE_DESCRIPTION'); ?>" class="jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_LARGE_PICTURE_DESCRIPTION'); ?>" />
	</div>
	<p>
        <?php if (!$firstLogin) { ?>
	<a href="javascript:updateThumbnail()" id="update-thumbnail"><?php echo JText::_('COM_COMMUNITY_UPDATE_THUMBNAIL'); ?></a>
	<a href="javascript:saveThumbnail()" id="update-thumbnail-save" style="display:none"><?php echo JText::_('COM_COMMUNITY_THUMBNAIL_SAVE'); ?></a>
        <?php }
        else{
        ?>
        <a style="float:right;" href="<?php echo $skipLink; ?>"class="saveButton"><span><?php echo JText::_('COM_COMMUNITY_NEXT'); ?></span></a>
        <?php }?>
        <br />
	</p>
	<div id="update-thumbnail-guide" style="display: none;"><?php echo JText::_('COM_COMMUNITY_UPDATE_THUMBNAIL_GUIDE'); ?></div>
</div>

<div class="cModule avatarPreview rightside">		
	<h3><?php echo JText::_('COM_COMMUNITY_PICTURE_THUMB_HEADING');?></h3>
	<img id="thumbnail-profile-pic" src="<?php echo $user->getThumbAvatar();?>" alt="" title="" />
</div>

<!-- Start thumbnail selection -->
<script type="text/javascript">
joms.jQuery('#large-profile-pic').load(function () {
	// Recalculate max height of the large avatar. We know the max width is 160
	// but for landscape, height can be smaller 
	var img = document.getElementById('large-profile-pic'); 
	var imgH = img.clientHeight;
	var imgW = 160;
	if(imgH < 160){imgW = imgH;}
	if(imgH > 160){imgH = 160;}
 
	// Create select object
	joms.jQuery('#large-profile-pic').imgAreaSelect(
		{ 
                  parent:'.cModule.avatarPreview.leftside',
                  maxWidth: 160, maxHeight: 160, handles: true ,aspectRatio: '1:1',
		  x1: 0, y1: 0, x2: imgW, y2: imgH,
		  show: false, hide: true, enable: false,
		  minHeight:<?php echo COMMUNITY_SMALL_AVATAR_WIDTH; ?>, minWidth:<?php echo COMMUNITY_SMALL_AVATAR_WIDTH; ?> 
		}
	);
	
});

function saveThumbnail(){
	var ias = joms.jQuery('#large-profile-pic').imgAreaSelect({ instance: true });
	var obj = ias.getSelection();
	jax.call('community', 'profile,ajaxUpdateThumbnail', obj.x1, obj.y1, obj.width, obj.height );
	
	// Hide it
	ias.setOptions({ show: false, hide: true, enable:false });
	ias.update();
	
	// Show the update button, but hide the save button
	joms.jQuery('#update-thumbnail').show();
	joms.jQuery('#update-thumbnail-save').hide();
	joms.jQuery('#update-thumbnail-guide').hide();
}

function updateThumbnail()
{
	var ias = joms.jQuery('#large-profile-pic').imgAreaSelect({ instance: true });
	ias.setOptions({ show: true, hide: false, enable:true });
	ias.update();
	
	// Show the save button, but hide the update button
	joms.jQuery('#update-thumbnail').hide();
	joms.jQuery('#update-thumbnail-save').show();
	joms.jQuery('#update-thumbnail-guide').show();
}

function refreshThumbnail(){
	var src = joms.jQuery('#thumbnail-profile-pic').attr('src');
	joms.jQuery('#thumbnail-profile-pic').attr('src', src+'?'+Math.random());
}
</script>