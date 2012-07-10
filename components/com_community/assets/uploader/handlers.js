/* **********************
   Event Handlers
   These are my custom event handlers to make my
   web application behave the way I went when SWFUpload
   completes different tasks.  These aren't part of the SWFUpload
   package.  They are part of my application.  Without these none
   of the actions SWFUpload makes will show up in my application.
   ********************** */

var pendingText			= '';
var filesExceededText	= '';
var uploadExceededText	= '';
var fileTooBigText		= '';
var unhandledErrorText	= '';
var uploadingText		= '';
var completeText		= '';
var uploadErrorText		= '';
var uploadFailedText	= '';
var zeroByteFileText	= '';
var invalidFileText		= '';
var serverErrorText		= '';
var securityErrorText	= '';
var failedValidationText	= '';
var uploadCancelledText	= '';
var uploadStoppedText	= '';
var fileUploadedText	= '';
var filesUploadedText	= '';

function fileQueued(file)
{
	try
	{
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setStatus( pendingText );
		progress.toggleCancel(true, this);

	}
	catch (ex)
	{
		this.debug(ex);
	}

}

function fileQueueError(file, errorCode, message) {
	try {
		if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED)
		{
			alert(filesExceededText + (message === 0 ? uploadExceededText : "You may select " + (message > 1 ? "up to " + message + " files." : "one file.")));
			return;
		}

		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);

		switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			progress.setStatus(fileTooBigText);
			this.debug("Error Code: File too big, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			progress.setStatus( zeroByteFileText );
			this.debug("Error Code: Zero byte file, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
			progress.setStatus( invalidFileText );
			this.debug("Error Code: Invalid File Type, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		default:
			if (file !== null) {
				progress.setStatus(unhandledErrorText);
			}
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}

function fileDialogComplete(numFilesSelected, numFilesQueued) {
	try {

		if (numFilesSelected > 0) {
			document.getElementById(this.customSettings.cancelButtonId).disabled = false;

			// @BUG: swfupload lacks an event handler that executes
			//       each time a upload queue gets executed. A handler
			//       in between fileDialogComplete & uploadStart/uploadResizedStart.
			//       
			//       If the first file in the group of selected files is >1MB,
			//       the rest of the files gets resized as well.

			// var file = this.getFile(0);
			// var maxFileSize = 1000000;

			// if (file.size > maxFileSize)
			// {
			// 	this.startResizedUpload(file.ID, 1600, 1200, SWFUpload.RESIZE_ENCODING.JPEG, 60, false);
			// } else {
				this.startUpload();
			// }
		}
	} catch (ex)  {
        this.debug(ex);
	}
}

function uploadStart(file) {
	try
	{
		/* I don't want to do any file validation or anything,  I'll just update the UI and
		return true to indicate that the upload should start.
		It's important to update the UI here because in Linux no uploadProgress events are called. The best
		we can do is say we are uploading.
		 */
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setStatus(uploadingText);
		progress.toggleCancel(true, this);
		
		// Change view album link target attributes to _blank when uploading is in progress.
		joms.jQuery( 'a#view-albums' ).attr( 'target' , '_blank' );
	}
	catch (ex)
	{
	}
	
	return true;
}

function uploadProgress(file, bytesLoaded, bytesTotal) {
	try {
		var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);

		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setProgress(percent);
		progress.setStatus(uploadingText);
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadSuccess(file, data) {
	var info = extractData(data);
	
	if(info['albumId']=='-1'){
		joms.jQuery('#photoUploadedCounter').html(info['thumbUrl']);
	} else {
		uploadingAlbumId = info['albumId'];
		//joms.ajax.call( 'photos,ajaxUpdateCounter', [info['albumId']] );
		
		//Show uploaded photos
		joms.jQuery('#community-photo-items').show();

		joms.jQuery(new Image()).attr('src', joms.jQuery.trim(info['thumbUrl']))
						   .appendTo('#community-photo-items div.container')
						   .wrap('<div class="photo-item" />');
	}
	
	// Once upload is complete, revert the target attributes
	joms.jQuery( 'a#view-albums' ).attr( 'target' , '_self' );
	
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setComplete();
		progress.setStatus( completeText );
		progress.toggleCancel(false);

	} catch (ex) {
		this.debug(ex);
	}
}

function uploadError(file, errorCode, message) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);

		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
			progress.setStatus(uploadErrorText + message);
			this.debug("Error Code: HTTP Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			progress.setStatus( uploadFailedText );
			this.debug("Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			progress.setStatus(serverErrorText);
			this.debug("Error Code: IO Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			progress.setStatus( securityErrorText );
			this.debug("Error Code: Security Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			progress.setStatus( uploadExceededText );
			this.debug("Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
			progress.setStatus( failedValidationText );
			this.debug("Error Code: File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			// If there aren't any files left (they were all cancelled) disable the cancel button
			if (this.getStats().files_queued === 0) {
				document.getElementById(this.customSettings.cancelButtonId).disabled = true;
			}
			progress.setStatus( uploadCancelledText );
			progress.setCancelled();
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			progress.setStatus( uploadStoppedText );
			break;
		default:
			progress.setStatus("Unhandled Error: " + errorCode);
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}

function uploadComplete(file) {
	if (this.getStats().files_queued === 0) {
		document.getElementById(this.customSettings.cancelButtonId).disabled = true;
	}
}

// This event comes from the Queue Plugin
function queueComplete(numFilesUploaded)
{
	var status = document.getElementById("divStatus");
	status.innerHTML = numFilesUploaded + " " + (numFilesUploaded === 1 ? fileUploadedText : filesUploadedText);
	
	joms.ajax.call( 'photos,ajaxUpdateCounter', [uploadingAlbumId] );
}

function extractData(data){
	data = data.split('#');
	var info = [];

	info['thumbUrl'] = data[0];
	info['albumId'] = (data[1] == undefined) ? '' : data[1];

	return info;
}

