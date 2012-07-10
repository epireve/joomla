<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();
?>
<?php
if( $albums )
{
?>
	<script type="text/javascript" src="<?php echo JURI::root(); ?>components/com_community/assets/ajaxfileupload.pack.js"></script>

<script type="text/javascript">
joms.uploader = {
	startIndex: 0,
	postUrl: '',
	originalPostUrl : '',
	uploadText: '',
	addNewUpload: function(){
		this.startIndex	+= 1;
		
		var html	= joms.jQuery('#photoupload').clone();
		html		= joms.jQuery(html).attr('id', 'photoupload-' + this.startIndex  ).css('display','block');

		// Apend data into the container
		joms.jQuery('#photoupload-container').append( html );
	
	 	// Set the input id correctly
	 	joms.jQuery('#photoupload-' + this.startIndex + ' :file').attr('id', 'Filedata-' + this.startIndex );
	 	joms.jQuery('#photoupload-' + this.startIndex + ' :file').attr('name', 'Filedata-' + this.startIndex );
	 	joms.jQuery( '#photoupload-' + this.startIndex + ' :input:hidden' ).attr('value' , this.startIndex );

		// Bind remove function
	 	joms.jQuery( '#photoupload-' + this.startIndex + ' .remove' ).bind( 'click' , function(){
	 		joms.jQuery( this ).parent().remove();
	 	} );
		  	
	},
	startUpload: function() {
		var currentIndex	= joms.jQuery('#photoupload').next().find('.elementIndex').val();

		// If this is called, we need to disable the upload button so that no duplicates will happen.
		joms.jQuery( '#upload-photos-button' ).hide();	
		joms.jQuery( '.add-new-upload' ).hide();
		joms.jQuery('#photoupload-container input').filter(function(){return joms.jQuery(this).parent().css('display') == 'block';}).attr('disabled',true);
		joms.uploader.upload( currentIndex );
		
		// Change view album link target attributes to _blank when uploading is in progress.
		joms.jQuery( 'a#view-albums' ).attr( 'target' , '_blank' );
		
	},
	upload: function ( elementIndex ){
		joms.jQuery('#Filedata-' + elementIndex).attr('disabled', false );
		
		if( joms.jQuery('#Filedata-' + elementIndex).val() == '' )
		{
			joms.jQuery( '#photoupload-' + elementIndex ).remove();
			joms.uploader.upload();

			// Test if there is a form around if it doesn't add a new form.
			if( joms.jQuery('#photoupload').next().length == 0 )
			{
				joms.uploader.addNewUpload();
			}
			else
			{
				joms.jQuery('#photoupload-container input').filter(function(){return joms.jQuery(this).parent().css('display') == 'block';}).attr('disabled',false);
			}
			joms.jQuery( '#upload-photos-button' ).show();

			joms.jQuery( '#new-upload-button' ).show();
			return;
		}

		// Revert to original path
		joms.uploader.postUrl = joms.uploader.originalPostUrl;
		
		// Check whether photo uploaded is set to be the default.
		var defaultPhoto	= (joms.jQuery('#photoupload-' + elementIndex + ' :input:checked').val() == "1" ) ? '1' : '0';
		this.postUrl = this.postUrl.replace('DEFAULT_PHOTOS', defaultPhoto);

		// Get the next upload id so it can pass back to this function again
		var nextUpload		= joms.jQuery( '#photoupload-' + elementIndex ).next().find('.elementIndex').val();
		nextUpload			= (nextUpload != '' ) ? nextUpload : 'undefined';
		this.postUrl = this.postUrl.replace('NXUP', nextUpload);

		// Hide existing form and whow a loading image so the user knows it's uploading.
		joms.jQuery('#photoupload-' + elementIndex ).children().each(function(){ 
			joms.jQuery(this).css('display','none');
		} );
		
		joms.jQuery('#photoupload-' + elementIndex ).append('<div id="photoupload-loading-' + elementIndex + '"><span class="loading" style="display:block;float: none;margin: 0px;"></span><span>' + joms.uploader.uploadText + '</span></div>');
		
		joms.jQuery.ajaxFileUpload({
				url: this.postUrl,
				secureuri:false,
				fileElementId:'Filedata-' + elementIndex,
				dataType: 'json',
				success: function (data, status){									   
					// Hide the loading class because it was added before the upload started.
					joms.jQuery( '#photoupload-loading-' + elementIndex ).remove();

					// Once upload is complete, revert the target attributes
					joms.jQuery( 'a#view-albums' ).attr( 'target' , '_self' );
					
					if(typeof(data.error) != 'undefined' && data.error == 'true' )
					{
						// Show nice red background stating error
						joms.jQuery( '#photoupload-' + elementIndex ).css('background', '#ffeded');
	
						// There was an error during the post, show the error message the user.
						joms.jQuery( '#photoupload-' + elementIndex).append( '<span class="error">' + data.msg + '</span>' );
					}
					else
					{
						// Upon success post to the site, we need to add some status.
						joms.jQuery( '#photoupload-' + elementIndex ).css('background', '#edfff3');
						joms.jQuery( '#photoupload-' + elementIndex ).append( '<span class="success">' + data.msg + '</span>');

						var info    =	joms.uploader.extractData( data.info );
						
						joms.ajax.call( 'photos,ajaxUpdateCounter', [info['albumId']] );

						//Show uploaded photos
						joms.jQuery('#community-photo-items').show();

						joms.jQuery(new Image()).attr('src', info['thumbUrl'])
								.appendTo('#community-photo-items div.container')
								.wrap('<div class="photo-item" />');
					}

					// Fadeout existing upload form
					joms.jQuery( '#photoupload-' + elementIndex).fadeOut( 4500 , function() {
						joms.jQuery( '#photoupload-' + elementIndex ).remove();
		
						// Test if there is a form around if it doesn't add a new form.
						if( joms.jQuery('#photoupload').next().length == 0 )
						{
							joms.uploader.addNewUpload();
						}
					});

					// Show the remove button
					joms.jQuery( '#photoupload-' + elementIndex + ' .remove').css('display','block');
					
					if( data.nextupload != 'undefined' )
					{
						joms.uploader.upload( data.nextupload );
						return;
					}
					else
					{
						joms.jQuery( '#upload-photos-button' ).show();	
						joms.jQuery( '#new-upload-button' ).show();
					}

				},
				error: function (data, status, e){
	// 				var names = '';
	// 				
	// 				for(var name in data)
	// 					names += name + "\n";
	// 				
	// 				alert(names);
	// 				alert(e.description);
				}
			}
		)
		return false;
	},
	extractData: function( data ){
		data = data.split('#');
		var info = [];

		info['thumbUrl'] = data[0];
		info['albumId'] = data[1];

		return info;
	}
}
</script>


	<script type="text/javascript" language="javascript">
	function submitForm()
	{
		joms.jQuery('#changeAlbum').submit();
	}
	</script>
	<form name="changeAlbum" id="changeAlbum" action="<?php echo CRoute::getURI();?>" method="POST">
	<div>
		<div><strong><?php echo JText::_( 'COM_COMMUNITY_VIDEOS_SELECT_PHOTO_ALBUM' ); ?></strong></div>
		<select name="albumid" onchange="submitForm();" class="inputbox">

		<?php if ($albumId==-1) { ?>
			<option value="-1" selected="selected"><?php echo JText::_('COM_COMMUNITY_PHOTOS_SELECT_ALBUM');?></option>
		<?php }; ?>

		<?php foreach($albums as $album) { ?>
			<option value="<?php echo $album->id;?>" <?php if($album->id==$albumId) { ?>selected="selected"<?php }; ?>><?php echo CStringHelper::truncate($this->escape($album->name), 64); ?></option>
		<?php } ?>

		</select>
		<?php
		if(!empty($albumId) && $albumId != -1 )
		{
		?>
		<span><a class="jsIcon1 icon-photos" id="view-albums" href="<?php echo $viewAlbumLink;?>" target="_self"><?php echo JText::_('COM_COMMUNITY_UPLOAD_VIEW_ALBUM');?></a></span>
		<?php
		}
		?>
	</div>
	<div></div>
	<?php echo JHTML::_( 'form.token' ); ?>
	</form>

	<div id="community-photo-items" class="photo-list-item" style="display:none">
		<div class="container"></div>
	</div>
	<br/>
	<div id="photoUploadedCounter" class="hints">
		<?php
			if(($photoUploadLimit <= 0) || ($photoUploadLimit > 0 && ($photoUploaded/$photoUploadLimit>=COMMUNITY_SHOW_LIMIT))) 
			{
			    if($photoUploadLimit >= 0 && !COwnerHelper::isCommunityAdmin() ){
				    echo JText::sprintf('COM_COMMUNITY_UPLOAD_LIMIT_STATUS', $photoUploaded, $photoUploadLimit );
			    }

			}
		?>
	</div>
	<?php
	// This section only proceeds when user selects an album
	if( !empty( $albumId ) && $albumId != -1 )
	{
	?>
	<script type="text/javascript" language="javascript">
	joms.uploader.postUrl 		= '<?php echo CRoute::_('index.php?option=com_community&view=photos&task=jsonupload&no_html=1&tmpl=component&defaultphoto=DEFAULT_PHOTOS&nextupload=NXUP&albumid=' . $albumId , false );?>';
	joms.uploader.uploadText	= '<?php echo JText::_('COM_COMMUNITY_PHOTO_UPLOADING');?>';
	joms.uploader.originalPostUrl = joms.uploader.postUrl;
	joms.jQuery(document).ready( function() {
		joms.uploader.addNewUpload();
		
	});
	</script>
	
	<div class="clr"></div>
	<div id="photoupload-container">
		<div id="photoupload" class="upload-form">
			<a class="remove" href="javascript:void(0);"></a>
			<input class="text input" type="file" onchange="joms.uploader.addNewUpload();" size="35" name="Filedata" id="Filedata" />
			<span>
				<input type="checkbox" name="default" value="1" /><?php echo JText::_('COM_COMMUNITY_PHOTOS_SET_AS_ALBUM_COVER'); ?>
			</span>
			<input type="hidden" name="elementIndex" class="elementIndex" />
		</div>
	</div>

	<div>
		<button class="button button-upload" onclick="joms.uploader.addNewUpload();" id="new-upload-button"><?php echo JText::_('COM_COMMUNITY_UPLOAD_ANOTHER_PHOTO');?></button>
		<button class="button button-upload" onclick="joms.uploader.startUpload();" id="upload-photos-button"><?php echo JText::_('COM_COMMUNITY_PHOTOS_START_UPLOAD_BUTTON');?></button>
	</div>
	<?php
		if( $uploadLimit != 0 )
		{
	?>
	<div><?php echo JText::sprintf('COM_COMMUNITY_MAXIMUM_UPLOAD_LIMIT' , $uploadLimit ); ?></div>
	<?php
		}
	}
	?>
<?php
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