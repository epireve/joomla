<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.utilities.utility');

class CAvatar {
	var $_path		= '';
	var $_relative	= '';
	var $_allowed	= array(
							'image/gif' 	=> 'gif',
							'image/pjpeg'	=> 'jpg',
							'image/jpeg'	=> 'jpg',
							'image/png'		=> 'png'
							);

	var $_image		= null;
	var $_filename	= '';
	var $_width		= '';
	var $_height	= '';
	var $_tmp		= '';
	var $_error		= '';
	var $_size		= '';
	var $_type		= '';
	
	public function CAvatar( $path = null , $forceCreate = false)
	{
		// If path is not defined, we assume that it is the default path used
		if( is_null($path) )
		{
			$this->_path	= JPATH_BASE . DS . 'images' . DS . 'avatar';
		}
		else
		{
			$this->_path	= $path;
		}
		
		// If path doesn't exists try to create them.
		if($forceCreate)
		{
			if(!JFile::exists($this->_path))
			{
				JFolder::create($this->_path);
				JFile::copy( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'index.html' , $this->_path . DS . 'index.html' );
			}
		}
	}

	/**
	 * Draws an for the specific image based on the image type
	 * 	 	
	 * @access	public
	 * @param	int	Type of the image (thumbnail = 0 ), (medium = 1), (large = 2)	 
	 * @param	string	The file name of the thumbnail	 
	 * @param	Array	An array that stores the uploaded image details
	 * @param	int	The width of the thumbnail
	 * @param	int	Height of the thumbnail	 	 
	 * @returns boolean	True on success and false otherwise
	 */	
	public function draw( $filename , $image , $width , $thumbnail = false, $maxWidth = false)
	{
		$filename		= JString::substr( $filename , 0 , 16 ) . '.jpg';
		$this->_type	= $image['type'];
		
		JFile::upload( $image['tmp_name'] , $this->_path . DS . $filename );
		
		list($currentWidth , $currentHeight) = getimagesize( $this->_path . DS . $filename );

		$height	= ($currentHeight / $currentWidth) * $width;

		$src_x	= 0;
		$src_y	= 0;

		$object		= new stdClass();

		// If there is maximum width specified, then we resize it
		if( $maxWidth == false || (($maxWidth != false) && ($currentWidth > $maxWidth)) )
		{
			$content	= $this->_create($this->_path . DS . $filename , $src_x , $src_y , $width , $height , $currentWidth , $currentHeight );

			if(JFile::write( $this->_path . DS . $filename , $content ))
			{
				$object->image	= $filename;
			}
		}
		else
		{
			// We assume image is below or has the same width or the caller doesnt want to resize.
			$object->image	= $filename;
		}

		
		if($thumbnail)
		{
			
			// Get new dimensions
			list($currentWidth, $currentHeight) = getimagesize( $this->_path . DS . $filename );
			
			// Find the correct x/y offset and source width/height. Crop the image
			// suqrely, at the centre
			if($currentWidth == $currentHeight)
			{
				$src_x = 0;
				$src_y = 0;
			}
			else if($currentWidth > $currentHeight)
			{
				$src_x			= ( $currentWidth - $currentHeight ) / 2 ;
				$src_y 			= 0;
				$currentWidth	= $currentHeight;
			}
			else
			{
				$src_x			= 0;
				$src_y			= ( $currentHeight - $currentWidth ) / 2 ;
				$currentHeight	= $currentWidth;
			}
			
			// Prepend 'thumb' before the file name
			$thumbFile	= JString::substr( 'thumb_' . $filename , 0 , 16 ) . '.jpg';
			$content	= $this->_create( $this->_path . DS . $filename , $src_x , $src_y , 64 , 64 , $currentWidth , $currentHeight);

			if(JFile::write( $this->_path . DS . $thumbFile , $content ))
			{
				$object->thumbnail	= $thumbFile;
			}
		}

		return $object;
	}
	
	public function _create( $file , $sourceX , $sourceY , $width , $height , $currentWidth , $currentHeight)
	{
		// Set output quality
		$config		= CFactory::getConfig();
		$imgQuality	= $config->get('output_image_quality');
		$pngQuality = ($imgQuality - 100) / 11.111111;
		$pngQuality = round(abs($pngQuality));
		
		// Create new image resource
		$image_p	= imagecreatetruecolor( $width , $height );
		$background	= imagecolorallocate( $image_p , 0 , 0 , 0 );
		
		if( isset($this->_allowed[$this->_type]) && ($this->_allowed[$this->_type] == 'gif' || $this->_allowed[$this->_type] == 'png' ) )
		{
			// Make new image to be transparent
			ImageColorTransparent( $image_p , $background );

			// Turn off alpha blending to keep the alpha channel
			imagealphablending( $image_p , false );
		}
		
		$image		= $this->_openImage( $file );
		
		imagecopyresampled( $image_p , $image, 0, 0, $sourceX, $sourceY, $width , $height , $currentWidth , $currentHeight );

		// Output
		ob_start();
		
		// Test if type is png
		if( $this->_type == 'image/png' )
		{
			imagepng($image_p , null, $pngQuality);
		}
		elseif ( $this->_type == 'image/gif')
		{
			imagegif( $image_p );
		}
		else
		{
			// We default to use jpeg
			imagejpeg($image_p, null, $imgQuality);
		}
		
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	/**
	 * Open image file
	 */	 	
	public function _openImage ($file)
	{
		# JPEG:
		$im = @imagecreatefromjpeg($file);
		if ($im !== false) { return $im; }
		
		# GIF:
		$im = @imagecreatefromgif($file);
		if ($im !== false) { return $im; }
		
		# PNG:
		$im = @imagecreatefrompng($file);
		if ($im !== false) { return $im; }
		
		# GD File:
		$im = @imagecreatefromgd($file);
		if ($im !== false) { return $im; }
		
		# GD2 File:
		$im = @imagecreatefromgd2($file);
		if ($im !== false) { return $im; }
		
		# WBMP:
		$im = @imagecreatefromwbmp($file);
		if ($im !== false) { return $im; }
		
		# XBM:
		$im = @imagecreatefromxbm($file);
		if ($im !== false) { return $im; }
		
		# XPM:
		$im = @imagecreatefromxpm($file);
		if ($im !== false) { return $im; }
		
		# Try and load from string:
		$im = @imagecreatefromstring(file_get_contents($file));
		if ($im !== false) { return $im; }
	}

	public function debug()
	{
		echo '<pre>';
		print_r($this);
		echo '</pre>';
	}
}

/**
 * Maintain classname compatibility with JomSocial 1.6 below
 */ 
class CAvatarLibrary extends CAvatar
{}