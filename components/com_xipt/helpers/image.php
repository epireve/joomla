<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptHelperImage
{
	function rotateImage($img, &$watermarkSize, $rotation) 
	{
	  
	  $width  = imagesx($img);
	  $height = imagesy($img);
	  switch($rotation) {
	    case 90: $newimg  = @imagecreatetruecolor($height , $width );break;
	    case 180: $newimg = @imagecreatetruecolor($width , $height );break;
	    case 270: $newimg = @imagecreatetruecolor($height , $width );break;
	    case 0: return $img;break;
	    case 360: return $img;break;
	  }
	  if($newimg) {
	    for($i = 0;$i < $width ; $i++) {
	      for($j = 0;$j < $height ; $j++) {
	        $reference = imagecolorat($img,$i,$j);
	        switch($rotation) {
	          case 90: if(!@imagesetpixel($newimg, ($height - 1) - $j, $i, $reference )){return false;}break;
	          case 180: if(!@imagesetpixel($newimg, $i, ($height - 1) - $j, $reference )){return false;}break;
	          case 270: if(!@imagesetpixel($newimg, $j, $width - $i, $reference )){return false;}break;
	        }
	      }
	    } 
		$watermarkSize[0]   = imagesx($newimg);
		  $watermarkSize[1] = imagesy($newimg);
		 return $newimg;
	  }
	  return false;
	}
	
	/* watermarksize array contain width at 0th index and height at 1st index in array 
	 * imagesize array contain width at 0th index and height at 1st index in array
	 * xy contain x pos at 0th index and y pos at 1st index
	 * */
	function setPosotion($imagesize,&$watermarkSize,&$watermarkImage,$position,$xy)
	{ 
		/*reference of image is always top-left corener */
		switch($position) {
			case 'tl': 
					$xy[0] = 0;
					$xy[1] = 0;
					break;
			case 'tr': 
					$xy[0] = $imagesize[0] - $watermarkSize[0];
					$xy[1] = 0;
					break;
			case 'bl': 
					$xy[0] = 0;
					$xy[1] = $imagesize[1] - $watermarkSize[1];
					break;
			case 'br': 
					$xy[0] = $imagesize[0] - $watermarkSize[0];
					$xy[1] = $imagesize[1] - $watermarkSize[1];
					break;
			/*now we have to rotate image */
			case 'lt': 
					$watermarkImage=self::rotateImage($watermarkImage,$watermarkSize,90*3);
					$xy[0] = 0;
					$xy[1] = 0;
					break;
			case 'lb': 
					$watermarkImage=self::rotateImage($watermarkImage,$watermarkSize,90*3);
					$xy[0] = 0;
					$xy[1] = $imagesize[1] - $watermarkSize[1];
					break;
			case 'rt': 
					$watermarkImage=self::rotateImage($watermarkImage,$watermarkSize,90);
					$xy[0] = $imagesize[0] - $watermarkSize[0];
					$xy[1] = 0;
					break;
			case 'rb': 
					$watermarkImage=self::rotateImage($watermarkImage,$watermarkSize,90);
					$xy[0] = $imagesize[0] - $watermarkSize[0];
					$xy[1] = $imagesize[1] - $watermarkSize[1];
					break;
			case 'lta': 
					$xy[0] = $imagesize[0] - $watermarkSize[0];
					$xy[1] = $imagesize[1] - $watermarkSize[1];
					break;
			case 'rta': 
					$xy[0] = $imagesize[0] - $watermarkSize[0];
					$xy[1] = $imagesize[1] - $watermarkSize[1];
					break;
			case 'lba': 
					$xy[0] = $imagesize[0] - $watermarkSize[0];
					$xy[1] = $imagesize[1] - $watermarkSize[1];
					break;
			case 'rba': 
					$xy[0] = $imagesize[0] - $watermarkSize[0];
					$xy[1] = $imagesize[1] - $watermarkSize[1];
					break;
		}
		
		return;
	}
	
    function getImageType($imagePath)
	{
		$extension	= JFile::getExt($imagePath);	
		switch($extension)
		{
			case 'png':
				$type	= 'image/png';
				break;
			case 'gif':
				$type	= 'image/gif';
				break;
			case 'jpg':
			case 'jpeg':
			default :
				$type	= 'image/jpg';
		}
		return $type;
	}
	
	//When we do not modify original image path, then we should not call it by reference.
	function addWatermarkOnAvatar($userid, $originalImage, $waterMark, $what)
	{		
		//Original Image in machine formate
		$originalImage	= XiptHelperUtils::getRealPath($originalImage);
		
		//IMP : do not modify original image.
		$image = JPATH_ROOT. DS. $originalImage;
		
		// Load image helper library as it is needed.
		require_once JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'helpers'.DS.'image.php';
		//ini_set('gd.jpeg_ignore_warning', 1);
		
		$ptype 			 = XiptLibProfiletypes::getUserData($userid,'PROFILETYPE');
		$watermarkParams = XiptLibProfiletypes::getParams($ptype,'watermarkparams');
		
		if($what == 'thumb')
			$waterMark = self::getThumbAvatarFromFull($waterMark);
				
		$waterMark = JPATH_ROOT. DS. $waterMark;
		
		$type  = self::getImageType($image);
		$wType = self::getImageType($waterMark);

		if($wType == 'image/jpg')
		{
			JFactory::getApplication()->enqueueMessage("Watermark must be PNG or GIF image, no watermark applied");
			return false;
		}

		$imageInfo	= getimagesize($image);
		
		if($imageInfo == false)
		{
			JFactory::getApplication()->enqueueMessage("Unable to open through getimage the file $image");
			return false;
		}
		
		$imageWidth   = $imageInfo[0];//imagesx( $image );	
		$imageHeight  = $imageInfo[1];// imagesy( $image );


		if($what == 'avatar'){
			$watermarkWidth  = WATERMARK_HEIGHT;
			$watermarkHeight = WATERMARK_WIDTH;
		}
		
		if($what == 'thumb'){
			$watermarkWidth  = $watermarkParams->get('xiThumbWidth',0);
			$watermarkHeight = $watermarkParams->get('xiThumbHeight',0);
			
			//XITODO : here we need to trick as per the JomSocial
			// we need to modify the code when things changes, currently 
			// the image informationfor thumbs does not come correctly
			$imageWidth  = AVATAR_WIDTH_THUMB;
			$imageHeight = AVATAR_HEIGHT_THUMB;
		}
		
		if(!JFile::exists($image) || !JFile::exists($waterMark))
			return false;
		
		
		// if warter marking is not enable for profile type then return
		
				
		/*First copy user old avatar b'coz we don't want to overwrite watermark */
		$avatarFileName = JFile::getName($originalImage);
		
		if(JFile::exists(USER_AVATAR_BACKUP.DS.$avatarFileName))
			JFile::copy(USER_AVATAR_BACKUP.DS.$avatarFileName,JPATH_ROOT.DS.$originalImage);

		// if watermarking is not enable for profile type then return
		if($watermarkParams->get('enableWaterMark',0) == false)
			return;
			
		$newimagepath = self::showWatermarkOverImage($image,$waterMark,'tmp',$watermarkParams->get('xiWatermarkPosition','br'));
				
		/*copy user original avatar at one place to remove destroy */
		//here check if folder exist or not. if not then create it.
		$avatarPath = USER_AVATAR_BACKUP;
		if(JFolder::exists($avatarPath)==false)
			JFolder::create($avatarPath);
		
		JFile::copy(JPATH_ROOT.DS.$originalImage,$avatarPath.DS.JFile::getName(JPATH_ROOT.DS.$originalImage));
		JFile::move(JPATH_ROOT.DS.$newimagepath,JPATH_ROOT.DS.$originalImage);
		return;
	}
	
	
	function showWatermarkOverImage( $imagePath, $watermarkPath ,$newImageName="tmp",$position='bl' )
	{
		XiptError::assert(JFile::exists($imagePath) && JFile::exists($watermarkPath)
			, XiptText::_("FILE $imagePath AND $watermarkPath DOES NOT EXIST"), XiptError::ERROR);
		
		//original image
		$destinationType = self::getImageType($imagePath);
		
		$watermarkType   = self::getImageType($watermarkPath);
		// Load image helper library as it is needed.
		require_once JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'helpers'.DS.'image.php';
		$watermarkImage	 = cImageOpen( $watermarkPath , $watermarkType);
		
		
		/*if(JFolder::exists(PROFILETYPE_AVATAR_STORAGE_REFERENCE_PATH)==false)
			JFolder::create(PROFILETYPE_AVATAR_STORAGE_REFERENCE_PATH);
			
		JFile::copy($imagePath,$newImagePath);*/
		$imageImage	 = cImageOpen( $imagePath , $destinationType);
		
		/*calculate watermark height and width from watermark image */
		$watermarkWidth		= imagesx( $watermarkImage );
		$watermarkHeight	= imagesy( $watermarkImage );
		
		/*get original image size */
		$size = getimagesize($imagePath);  
		
		$dest_x = 0;//$size[0] - $watermarkWidth - 5;  
		$dest_y = 0;//$size[1] - $watermarkHeight - 5;
		
		$xy = array();
		$xy[0] = &$dest_x;
		$xy[1] = &$dest_y;
		
		$watermarkSize=array();
		$watermarkSize[0] = $watermarkWidth;
		$watermarkSize[1] = $watermarkHeight;
		self::setPosotion($size,$watermarkSize,$watermarkImage,$position,$xy);
				
		imagecopymerge($imageImage , $watermarkImage, $dest_x, $dest_y, 0, 0, $watermarkSize[0], $watermarkSize[1], 100);
		
		/*first copy the image to tmp location , b'coz we don't want to destroy original image */
		$newImagePath = PROFILETYPE_AVATAR_STORAGE_PATH.DS.$newImageName.'.'.JFile::getExt($imagePath);
		$newImageRefPath = PROFILETYPE_AVATAR_STORAGE_REFERENCE_PATH.DS.$newImageName.'.'.JFile::getExt($imagePath);
		
		imagesavealpha($imageImage, true);
		
		ob_start();
		// Test if type is png
		if( $destinationType == 'image/png' || $destinationType == 'image/x-png' )
			imagepng($imageImage);			
		elseif ( $destinationType == 'image/gif')
			imagegif($imageImage);
		else{
			// We default to use jpeg
			imagejpeg($imageImage, null, 100);		
		}

		$output = ob_get_contents();
		ob_end_clean();
		JFile::write( $newImagePath , $output );
		
		// Free any memory from the existing image resources
		imagedestroy( $imageImage );
		imagedestroy( $watermarkImage );
		
		return $output ? $newImageRefPath : false;
	}
	
	function getThumbAvatarFromFull($avatar)
	{
		if(empty($avatar)){
			return '';
		}
		$ext   = JFile::getExt($avatar);
		$thumb = JFile::stripExt($avatar).'_thumb.'.$ext;
		return $thumb;
	}
	
	function getWatermark($userid)
	{
		$ptype		   = XiptLibProfiletypes::getUserData($userid,'PROFILETYPE');
		$watermarkInfo = XiptLibProfiletypes::getProfiletypeData($ptype,'watermark');
		if(!$watermarkInfo)
			return false;
		
		return $watermarkInfo;
	}
}
