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
<script type="text/javascript" language="javascript">
joms.jQuery(document).ready(function(){
 	if(!joms.flash.enabled() )
 	{
 		joms.jQuery( '#community-flash-notice' ).show();
 		joms.jQuery( '#community-photo-wrap' ).hide();
 	}
});
</script>
<div id="community-flash-notice" style="display:none;">
	<?php echo JText::_('COM_COMMUNITY_NO_FLASH_DETECTED_NOTICE');?>
</div>
<div id="community-photo-wrap">
<?php
if( $albums )
{
?>
	<script type="text/javascript" language="javascript">	
	function submitForm()
	{
		joms.jQuery('#changeAlbum').submit();
	}
	</script>
	<div>
		<?php echo JText::_('COM_COMMUNITY_PHOTOS_MULTIPLE_UPLOAD_DESCRIPTION');?>
	</div>
	<div>
		<form name="changeAlbum" id="changeAlbum" action="<?php echo CRoute::getURI();?>" method="POST">
		<select name="albumid" onchange="submitForm();" class="inputbox">
		<?php
		$selected	= ( $albumId == -1 ) ? 'selected="selected"' : '';
		?>
			<option value="-1"<?php echo $selected;?>><?php echo JText::_('COM_COMMUNITY_PHOTOS_SELECT_ALBUM');?></option>
		<?php
		foreach($albums as $album)
		{
			if($albumId != '' && ($album->id == $albumId))
			{
		?>
			<option value="<?php echo $album->id;?>" selected="selected"><?php echo $this->escape($album->name); ?></option>
		<?php
			}
			else
			{
		?>
			<option value="<?php echo $album->id;?>"><?php echo $this->escape($album->name); ?></option>
		<?php
			}
		}
		?>
		</select>
		<?php
		if(!empty($albumId) && $albumId != -1 )
		{
		?>
		<span><a class="jsIcon1 icon-photos" id="view-albums" href="<?php echo $viewAlbumLink;?>" target="_self"><?php echo JText::_('COM_COMMUNITY_UPLOAD_VIEW_ALBUM');?></a></span>
		<?php
		}
		?>
		</form>
	</div>

	<div id="community-photo-items" class="photo-list-item" style="display:none">
		<div class="container"></div>
	</div>

	<div id="photoUploadedCounter" class="hints">
		<?php 
			if($photoUploadLimit > 0 && !COwnerHelper::isCommunityAdmin() )
				echo JText::sprintf('COM_COMMUNITY_UPLOAD_LIMIT_STATUS', $photoUploaded, $photoUploadLimit );
		?>
	</div>
	<?php
	// This section only proceeds when user selects an album
	if( !empty( $albumId ) && $albumId != -1 )
	{
	?>
	<script type="text/javascript" src="<?php echo JURI::base() . 'components/com_community/assets/uploader/swfupload.js';?>"></script>
	<script type="text/javascript" src="<?php echo JURI::base() . 'components/com_community/assets/uploader/handlers.js';?>"></script>
	<script type="text/javascript" src="<?php echo JURI::base() . 'components/com_community/assets/uploader/plugins/queue.js';?>"></script>
	<script type="text/javascript" src="<?php echo JURI::base() . 'components/com_community/assets/uploader/progress.js';?>"></script>
	<script type="text/javascript">
	var uploader;
	
	joms.jQuery(document).ready(function() {
		pendingText			= '<?php echo addslashes( JString::trim( JText::_('COM_COMMUNITY_PHOTO_UPLOAD_PENDING') ) );?>';
		filesExceededText	= '<?php echo addslashes( JString::trim( JText::_('COM_COMMUNITY_PHOTO_UPLOAD_TOO_MANY_FILES') ) );?>';
		uploadExceededText	= '<?php echo addslashes( JString::trim( JText::_('COM_COMMUNITY_PHOTO_UPLOAD_LIMIT_EXCEEDED') ) );?>';
		fileTooBigText		= '<?php echo addslashes( JString::trim( JText::_('COM_COMMUNITY_PHOTO_UPLOAD_FILE_TOO_BIG') ) );?>';
		unhandledErrorText	= '<?php echo addslashes( JString::trim( JText::_('COM_COMMUNITY_PHOTO_UPLOAD_UNHANDLED_ERROR') ) );?>';
		uploadingText		= '<?php echo addslashes( JString::trim( JText::_('COM_COMMUNITY_PHOTO_UPLOADING') ) ); ?>';
		completeText		= '<?php echo addslashes( JString::trim( JText::_('COM_COMMUNITY_PHOTO_UPLOAD_COMPLETED') ) );?>';
		uploadErrorText		= '<?php echo addslashes( JString::trim( JText::_('COM_COMMUNITY_PHOTO_UPLOAD_ERROR') ) );?>';
		uploadFailedText	= '<?php echo addslashes( JString::trim( JText::_('COM_COMMUNITY_PHOTO_UPLOAD_FAILED') ) );?>';
		zeroByteFileText	= '<?php echo addslashes( JString::trim( JText::_('COM_COMMUNITY_ZERO_BYTE_FILE') ) );?>';
		invalidFileText		= '<?php echo addslashes( JString::trim( JText::_('COM_COMMUNITY_PHOTOS_INVALID_FILE_ERROR') ) );?>';
		serverErrorText		= '<?php echo addslashes( JString::trim( JText::_('COM_COMMUNITY_PHOTOS_UPLOAD_SERVER_IO_ERROR') ) );?>';
		securityErrorText	= '<?php echo addslashes( JString::trim( JText::_('COM_COMMUNITY_PHOTOS_UPLOAD_SECURITY_ERROR') ) );?>';
		failedValidationText= '<?php echo addslashes( JString::trim( JText::_('COM_COMMUNITY_PHOTOS_UPLOAD_VALIDATION_ERROR') ) );?>';
		uploadCancelledText	= '<?php echo addslashes( JString::trim( JText::_('COM_COMMUNITY_PHOTOS_UPLOAD_CANCELLED') ) );?>';
		uploadStoppedText	= '<?php echo addslashes( JString::trim( JText::_('COM_COMMUNITY_PHOTOS_UPLOAD_STOPPED') ) );?>';
		fileUploadedText	= '<?php echo addslashes( JString::trim( JText::_('COM_COMMUNITY_PHOTOS_UPLOAD_SUCCESS') ) );?>';
		filesUploadedText	= '<?php echo addslashes( JString::trim( JText::_('COM_COMMUNITY_PHOTOS_UPLOADED') ) );?>';
		buttonBrowseText    = '<?php echo addslashes( JString::trim( JText::_('COM_COMMUNITY_BROWSE_BUTTON') ) );?>';

		var btnBrowse = joms.jQuery('#btnBrowse')
		                .css({
		                	'float'      : 'left',
		                	'position'   : 'relative'
		                });
		                	
		var btnBrowseRef = joms.jQuery('#btnCancel').clone()
							.attr({
								'id': 'btnBrowseRef',
								'value'  : buttonBrowseText
							})
							.removeAttr('onclick')
							.removeAttr('disabled')
							.unbind('click')
							.appendTo(btnBrowse);

		var settings = {
			flash_url : "<?php echo JURI::base() . 'components/com_community/assets/uploader/swfupload.swf';?>",
			upload_url: "<?php echo $uploadURI;?>", 
			file_post_name : "Filedata",
			file_size_limit : "<?php echo $uploadLimit ?>MB",
			file_types : "*.png;*.jpg;*.gif",
			file_types_description : "<?php echo addslashes( JString::trim( JText::_('COM_COMMUNITY_ALL_IMAGE_TYPES_ALLOWED') ) ); ?>",
			file_upload_limit : 100,
			file_queue_limit : 0,
			custom_settings : {
				progressTarget : "uploadProgress",
				cancelButtonId : "btnCancel"
			},
			debug: false,

			// button_image_url: "<?php echo JURI::base() . 'components/com_community/assets/uploader/button.png';?>",	// Relative to the Flash file
			//button_image_url: buttonRef.css('background-image');

			button_placeholder_id: "uploadButton",
			button_width: btnBrowseRef.outerWidth(),
			button_height: btnBrowseRef.outerHeight(),
			button_text: '',
			button_text_style: '',
			button_text_left_padding: 0,
			button_text_top_padding: 0,
			button_cursor: SWFUpload.CURSOR.HAND,
			button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
 			mouse_click_handler: function()
 			{
 				btnBrowseRef.click();
 			},
 			mouse_over_handler: function()
 			{
 				btnBrowseRef.mouseover();
 				btnBrowseRef.mouseenter();
	 		},
 			mouse_out_handler: function()
 			{
 				btnBrowseRef.mouseout();
 				btnBrowseRef.mouseleave();
	 		},
			
			// The event handler functions are defined in handlers.js
			file_queued_handler : fileQueued,
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : fileDialogComplete,
			upload_start_handler : uploadStart,
			upload_progress_handler : uploadProgress,
			upload_error_handler : uploadError,
			upload_success_handler : uploadSuccess,
			upload_complete_handler : uploadComplete,
			queue_complete_handler : queueComplete	// Queue plugin event
		};

		uploader = new SWFUpload(settings);

	});
	</script>
	<form name="jsform-photos-uploader-flash" id="uploadPhotos" action="#" method="post" enctype="multipart/form-data">
		<div class="flash fieldset" id="uploadProgress">
			<span class="legendTitle">
				<?php echo JText::_('COM_COMMUNITY_IMAGE_UPLOAD_QUEUE');?>
			</span>

			<div id="divStatus" style="text-align: right;">
				0 <?php echo JText::_('COM_COMMUNITY_PHOTOS_UPLOADED');?>
			</div>
		</div>
	<?php
		if( $uploadLimit != 0 )
		{
	?>
		<div class="small">
			<?php echo JText::sprintf('COM_COMMUNITY_MAXIMUM_UPLOAD_LIMIT', $uploadLimit);?>
		</div>
	<?php
		}
	?>
		<div>
			<span id="btnBrowse">
				<span id="uploadButton"></span>
			</span>
			<input class="button" id="btnCancel" type="button" value="<?php echo JText::_('COM_COMMUNITY_CANCEL_BUTTON');?>" onclick="uploader.cancelQueue();" disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 29px;" />
		</div>
		<div class="clr"></div>
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
<?php
	}
}
else
{
?>
	<div>
		<span><?php echo JText::_('COM_COMMUNITY_PHOTOS_NO_ALBUM_CREATED'); ?></span>
		<span>
			<a href="<?php echo $createAlbumLink;?>">
			<?php echo JText::_('COM_COMMUNITY_PHOTOS_CREATE_ONE_NOW');?>
			</a>
		</span>
	</div>
<?php
}
?>
</div>
