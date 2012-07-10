<?php
/**
 * @category	Helper
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

require_once (COMMUNITY_COM_PATH.DS.'models'.DS.'videos.php');

/**
 * Class to manipulate data from Youku
 * 	 	
 * @access	public  	 
 */
class CTableVideoYouku extends CVideoProvider
{
	var $xmlContent = null;
	var $url 		= '';
	var $videoId	= '';
	private $infoArr = '';
	private $htmlurl="http://v.youku.com/v_show/{id}.html";
	private $jsonurl="http://v.youku.com/player/getPlayList/VideoIDS/{id}/timezone/+08/version/5/source/out?password=&ran={rand}&n=";
	
	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		$videoId = explode("/", $this->videoId);
		return $this->url;
	}
	
	/*
	 * Return true if successfully connect to remote video provider
	 * and the video is valid
	 */	 
	public function isValid()
	{
		// Connect and get the remote video
        if ( !parent::isValid())
		{
			return false;
		}
		return true;
	}	
	
	/**
	 * Extract youku video id from the video url submitted by the user
	 * 	 	
	 * @access	public
	 * @param	video url
	 * @return videoid	 
	 */
	public function getId()
	{
		$this->get_data($this->url);
		return $this->id;
	}
	
	private function getIdArr($url){
		CFactory::load('helpers' , 'videos');
		$content=CVideosHelper::getVideoInfo($url);
		$swf = '';
		$urlid = '';
		if(preg_match_all("/<embed\s*src=\"(.*)\"/siU",$content,$flashurl,2)){
			$swf=$flashurl[0][1];
			$urlid=preg_replace("/\/.*$/si","",preg_replace("/^.*sid\//si","",$swf));
		}else{
			//return error
		}
		
		return array("swf"=>$swf,"id"=>$urlid);
	}
	
	public function get_data($url){
		CFactory::load('helpers' , 'videos');
		
		$jsonurl=str_ireplace("{rand}",rand(100,999),$this->jsonurl);
		$idandswf=$this->getIdArr($url);
		$jsonurl=str_ireplace("{id}",$idandswf['id'],$jsonurl);
		$dat=CVideosHelper::getVideoInfo($jsonurl);
		$jsonobj=json_decode($dat);
		$this->id = $idandswf['id'];
		$this->swf=$idandswf['swf'];
		$this->thumbnail=$jsonobj->data[0]->logo;
		$this->duration=$jsonobj->data[0]->seconds;
		$this->title=$jsonobj->data[0]->title;
		$this->description = 'test';
	}
	
	/**
	 * Return the video provider's name
	 * 
	 */
	public function getType()
	{
		return 'youku';
	}
	
	public function getTitle()
	{	
		return $this->title;
	}
	
	/**
	 * Get video's description from videoid
	 * 
	 * @access 	public
	 * @param 	videoid
	 * @return desctiption
	 */
	public function getDescription()
	{
		return '';
	}
	
	/**
	 * Get video duration 
	 * 
	 * @return $duration seconds
	 */
	public function getDuration()
	{	
		return $this->duration;
	}
	
	/**
	 * Get video's thumbnail URL from videoid
	 * 
	 * @access 	public
	 * @param 	videoid
	 * @return url
	 */
	public function getThumbnail()
	{
		return $this->thumbnail;
	}
	
	/**
	 * 
	 * 
	 * @return $embedvideo specific embeded code to play the video
	 */
	public function getViewHTML( $videoId, $videoWidth, $videoHeight )
	{
		if (!$videoId)
		{
			$videoId	= $this->videoId;
		}
		
		return '<embed src="http://player.youku.com/player.php/sid/'.$videoId.'/v.swf" width="'.$videoWidth.'" height="'.$videoHeight.'" wmode="transparent" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowFullScreen="true" wmode="transparent"> </embed>';
	}
}
