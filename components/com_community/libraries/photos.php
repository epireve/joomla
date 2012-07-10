<?php
/**
 * @category	Library
 * @package		JomSocial
 * @subpackage	Photos 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');
CFactory::load('helpers', 'image');

define('C_ASPECT_LANDSCAPE_RATIO', 1.3125);

class CPhotos
{	
	
	const ASPECT_PORTRAIT 	= 1;
	const ASPECT_LANDSCAPE 	= 2;
	const ASPECT_SQUARE		= 0;
	
	const ASPECT_LANDSCAPE_RATIO 	= 1.31;
	const ASPECT_PORTRAIT_RATIO 	= 0.76;
	const ASPECT_SQUARE_RATIO 		= 1.00;
	
	const ACTIVITY_SUMMARY_ITEM_COUNT = 5;
	
	static public function getActivityContentHTML($act)
	{
		// Ok, the activity could be an upload OR a wall comment. In the future, the content should
		// indicate which is which
		CFactory::load('models', 'photos');
		
		$html 	 = '';
		$param 	 = new CParameter( $act->params );
		$action  = $param->get('action' , false);
		$photoid = $param->get('photoid' , 0);
		$url	 = $param->get( 'url' , false );
		
		CFactory::load( 'helpers' , 'albums' );
		
		if( $action == 'wall' )
		{
			// unfortunately, wall post can also have 'photo' as its $act->apps. If the photo id is availble
			// for (newer activity stream, inside the param), we will show the photo snippet as well. Otherwise
			// just print out the wall content

			// Version 1.6 onwards, $params will contain photoid information
			// older version would have #photoid in the $title, since we link it to the photo
			
			$photoid = $param->get('photoid', false);

			if( $photoid )
			{
				$photo = JTable::getInstance( 'Photo' , 'CTable' );
				$photo->load( $act->cid );
				$helper	= new CAlbumsHelper( $photo->albumid );
				
				if( $helper->showActivity() )
				{
					$tmpl	= new CTemplate();
					return $tmpl->set( 'url' , $url )
								->set( 'photo'	, $photo )
								->set( 'param'	, $param )
								->set( 'act'	, $act )
								->fetch( 'activity.photos.wall' );
				}
			}
			return '';
		}
		elseif ( $action == 'upload' && $photoid > 0)
		{
			$photoModel = CFactory::getModel('photos');
			$album		= JTable::getInstance( 'Album' , 'CTable' );
			$album->load( $act->cid );

			$albumsHelper	= new CAlbumsHelper( $album );

			if( $albumsHelper->isPublic() )
			{
				// If content has link to image, we could assume it is "upload photo" action
				// since old content add this automatically.
				// The $act->cid will be the album id, Retrive the recent photos uploaded
				
				// If $act->activities has data, that means this is an aggregated content
				// display some of them
				
	
				if(empty($act->activities))
				{
					$acts[] = $act;
				}
				else
				{
					$acts	= $act->activities;
				}
	
				$tmpl	= new CTemplate();
				return $tmpl->set( 'album'	, $album )
							->set( 'acts'	, $acts )
							->set( 'stream', $param->get('stream' , false))
							->fetch( 'activity.photos.upload' );
			}
		}
		
		return $html;
	}
	
	/**
	 * Return the given photo aspect ratio
	 */	 	
	public function getPhotoAspectRatio($srcPath)
	{
		/*
		$size = CImageHelper::getSize($srcPath);
		$ratio = ($size->width / $size->height);
		
		if( $ratio >  CPhotos::ASPECT_LANDSCAPE_RATIO)
			return CPhotos::ASPECT_LANDSCAPE;
		
		if( $ratio <  CPhotos::ASPECT_PORTRAIT_RATIO)
			return CPhotos::ASPECT_PORTRAIT;
		*/
		// Only allow square thumbnails for now
		return CPhotos::ASPECT_SQUARE;
		
	}
	
	/**
	 * Generate photo thumbnail
	 */	 	
	public function generateThumbnail($srcPath, $destPath, $destType)
	{
		$aspect = CPhotos::getPhotoAspectRatio($srcPath);
		list( $currentWidth , $currentHeight )	= getimagesize( $srcPath );
				
		$origWidth 	= $currentWidth;
		$origHeight = $currentHeight;
		$destWidth  = COMMUNITY_PHOTO_THUMBNAIL_SIZE;
		$destHeight = COMMUNITY_PHOTO_THUMBNAIL_SIZE;
		$sourceX = 0;
		$sourceY = 0;
		
		switch($aspect){
			/*
			case CPhotos::ASPECT_PORTRAIT:
				$currentHeight	= $currentWidth / CPhotos::ASPECT_PORTRAIT_RATIO;
				
				$sourceY		= intval( ( $origHeight - $currentHeight ) / 2 );
				$sourceX		= 0;
				
				$destHeight = 84;
				break;
				
			case CPhotos::ASPECT_LANDSCAPE:
				$currentWidth		= $currentHeight * CPhotos::ASPECT_LANDSCAPE_RATIO;
				$sourceX			= intval( ( $origWidth - $currentWidth ) / 2 );
				$sourceY 			= 0;
				
				$destWidth = 84;
				break;
			*/
			default:
				
		}
		
		//CImageHelper::resize( $srcPath , $destPath , $destType , $destWidth , $destHeight , $sourceX , $sourceY , $currentWidth , $currentHeight);
		CImageHelper::createThumb( $srcPath , $destPath , $destType , $destWidth , $destHeight );
		
	}
	
	/**
	 * Given the original source path
	 */	 	
	public function getStorePath($srcPath)
	{
	}
	
	public function saveAvatarFromURL($url)
	{
		$tmpPath    = JPATH_ROOT . DS . 'images';
		$my	    = CFactory::getUser();
		
		// Need to extract the non-https version since it will cause
		// certificate issue
		$avatarUrl  =	str_replace('https://', 'http://', $url);
		CFactory::load('helpers', 'remote');
		$source	    =	CRemoteHelper::getContent($url, true);
		JFile::write( $tmpPath , $source );

		// @todo: configurable width?
		$imageMaxWidth	= 160;
		
		// Get a hash for the file name.
		$fileName	= JUtility::getHash( $my->getDisplayName() . time() );
		$hashFileName	= JString::substr( $fileName , 0 , 24 );
		
		$extension  = JString::substr( $url , JString::strrpos( $url , '.' ) );

		$type	= 'image/jpg';

		if( $extension == '.png' )
		{
			$type	= 'image/png';
		}

		if( $extension == '.gif' )
		{
			$type	= 'image/gif';
		}

		//@todo: configurable path for avatar storage?
		$config		    = CFactory::getConfig();
		$storage	    = JPATH_ROOT . DS . $config->getString('imagefolder') . DS . 'avatar';
		$storageImage	    = $storage . DS . $hashFileName . $extension;
		$storageThumbnail   = $storage . DS . 'thumb_' . $hashFileName . $extension;
		$image		    = $config->getString('imagefolder') . '/avatar/' . $hashFileName . $extension;
		$thumbnail	    = $config->getString('imagefolder') . '/avatar/' . 'thumb_' . $hashFileName . $extension;

		$userModel	    = CFactory::getModel( 'user' );
		
		// Only resize when the width exceeds the max.
		CImageHelper::resizeProportional( $tmpPath , $storageImage , $type , $imageMaxWidth );
		CImageHelper::createThumb( $tmpPath , $storageThumbnail , $type );
		
		$removeOldImage = false;

		$userModel->setImage( $my->id , $image , 'avatar' , $removeOldImage );
		$userModel->setImage( $my->id , $thumbnail , 'thumb' , $removeOldImage );

		// Update the user object so that the profile picture gets updated.
		$my->set( '_avatar' , $image );
		$my->set( '_thumb'  , $thumbnail );
		
		return true;
	}

}