<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' );

class XiptLibImage
{
	private $width;
	private $height;
	
	private $text;
	private $textcolor;
	private $background; 
    private $imgpath;
	private	$fontSize;
	private	$fontName;
	private $imgname;
	private $params;
	private $_debugMode;
	
	// initalize arguments
	function __construct($params, $debugMode = false)
	{
		$this->params		= 	$params;
		$this->width		= 	$params->get('xiWidth','160');
		$this->height		= 	$params->get('xiHeight','40');
        //  if type of watermark is image then set image path and image name
		if($params->get('typeofwatermark','0')=='1'){
	     $this->imgpath  	=   $_FILES['watermarkparams']['tmp_name']['xiImage'];
		 $this->imgname		=   $_FILES['watermarkparams']['name']['xiImage'];
		}
		$this->fontSize	= 	$params->get('xiFontSize','26');
		$this->fontName		=   JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'assets'.DS.'fonts'.DS.$params->get('xiFontName','monofont').'.ttf';
		$this->text			= 	$params->get('xiText','Profiletype');
		$this->textcolor	= 	$params->get('xiTextColor','FFFFFF');
		$this->background	= 	$params->get('xiBackgroundColor','000000');
		$this->_debugMode	=	$debugMode;
				
	}
	
	
	/*return a valid filename ( with extension ) to store image */
	function genImage($path,$filename) 
	{
		XiptError::assert(!empty($path), XiptText::_("PATH_IS_EMPTY"), XiptError::ERROR);
		XiptError::assert(!empty($filename), XiptText::_("FILE_IS_EMPTY"), XiptError::ERROR);
			
		//here check if folder exist or not. if not then create it.
		if(JFolder::exists($path)==false && JFolder::create($path)===false){
			XiptError::raiseError(__CLASS__.'.'.__LINE__,sprintf(XiptText::_("FOLDER_DOES_NOT_EXIST"), $path));
			return false;
		}
			
		$filename 	 = $filename.".png";
		$filepath	 = $path."/".$filename;
		
		// init image
		// Create a new image instance
		$img 			= 	ImageCreateTrueColor($this->width, $this->height);
		
	    if($img==false){
            XiptError::raiseError(__CLASS__.'.'.__LINE__,'IMAGE_NOT_GENERATED_RE-TRY');
			return false;
        }
        
		$Cbackground	=	$this->_getColor($img, $this->background);
		$Ctextcolor		=	$this->_getColor($img, $this->textcolor);
		
		// fill background color
		imagefilledrectangle($img,	0,	0,	$this->width,	$this->height,	$Cbackground);
		
		// print string
		$textbox = imagettfbbox($this->fontSize, 0, $this->fontName, $this->text);
		$x 		 = ($this->width - $textbox[4])/2;
		$y 		 = ($this->height - $textbox[5])/2; 
		
		imagettftext($img, $this->fontSize, 0, $x, $y, $Ctextcolor, $this->fontName, $this->text);
		
		if($img==false){
            XiptError::raiseError(__CLASS__.'.'.__LINE__,'IMAGE_NOT_GENERATED_RE-TRY');
			return false;
        }
        
		$result	=	 imagepng($img,$filepath);
		
		//fix for permissions
		chmod($filepath, 0744);
		
		imagedestroy($img);
		
		// if file creation is successfull return filename , else false
		return $result ? $filename :  false;
	}
	
	function _getColor($img , $hexcode)
	{
		assert($img);
		list($r, $g, $b)	= $this->_html2rgb($hexcode);
		return imagecolorallocate($img, $r,$g,$b);
	}
	
	// convert color into RGB
	function _html2rgb($color)
	{
	    if ($color[0] == '#')
	        $color = substr($color, 1);

	    if (strlen($color) == 6)
	        list($r, $g, $b) = array($color[0].$color[1],
	                                 $color[2].$color[3],
	                                 $color[4].$color[5]);
	    elseif (strlen($color) == 3)
	        list($r, $g, $b) = array($color[0].$color[0],
	        						 $color[1].$color[1], 
	        						 $color[2].$color[2]);
	    else
	        return false;

	    $r = hexdec($r); 
	    $g = hexdec($g); 
	    $b = hexdec($b);
	    
	    return array($r, $g, $b);
	}
	
	//create Watermark from an image
	function createImageWatermark($path,$filename)
	{
		XiptError::assert(!empty($path), XiptText::_("PATH_IS_EMPTY"), XiptError::ERROR);
		XiptError::assert(!empty($filename), XiptText::_("FILE_IS_EMPTY"), XiptError::ERROR);
			
		//here check if folder exist or not. if not then create it.
		if(JFolder::exists($path)==false && JFolder::create($path)===false)
           {
			XiptError::raiseError(__CLASS__.'.'.__LINE__,sprintf(XiptText::_("FOLDER_DOES_NOT_EXIST"), $path));
			return false;
		   }
			
		$filename 	 = $filename.".png";
		$filepath	 = $path."/".$filename;
		//create an image identifier from path
		$img = $this->load($this->imgname);
		
	
	    if($img==false){
			return false;
	      }
		$img_width  =  imagesx($img);
		$img_height =  imagesy($img);
        //create a blank transparent image with the 
        // height and width of the watermark
		$new_image=$this->imageCreateTransparent($this->width, $this->height);
	    // resize $img to watermark height and width
        //copy it to $new_image
        imagecopyresampled($new_image, $img, 0, 0, 0, 0,$this->width, $this->height,$img_width, $img_height);
		// create an png image 
        $result	=imagepng($new_image,$filepath);
	    chmod($filepath, 0744);
	    imagedestroy($new_image);
		
		// if file creation is successfull return filename , else false
		return $result ? $filename :  false;        
	}
	
     // create image identifier according to the extension
	 function load($imgname) 
	 {
	 	if(!$imgname)
			return false;

 		$filename1 = stripslashes($this->imgname);
		//XiTODO:: Use php fun rather than manually
	   	$extension = $this->getExtension($filename1);
	  	$extension = strtolower($extension);
	 	
		if($extension != "png"){
		     XiptError::raiseWarning('XIPT_THUMB_WAR','EXTENSION NOT SUPPORTED');
             }
		else{
		     $uploadedfile =  $this->imgpath;
		   	  $src = imagecreatefrompng($uploadedfile);	
		     }
		 return $src;
       }
	
     //get extension of a file
     function getExtension($str)
     {
	   $i = strrpos($str,".");
       if(!$i) { return false; } 
       $l = strlen($str) - $i;
       $ext = substr($str,$i+1,$l);
       return $ext;
     }
 
    //create a transparent blank image
     function imageCreateTransparent($x, $y) 
     {
      $imageOut 		 = imagecreate($x, $y);
      $colourtransparent = imagecolorallocate($imageOut, 0, 0, 0);
      imagecolortransparent($imageOut, $colourtransparent);
      return $imageOut;
      }
}
