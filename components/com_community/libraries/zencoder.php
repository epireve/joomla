<?php
/**
 * @package		JomSocial
 * @subpackage	Core 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
 * Zencoder class
 * TODO: multiple output files?
 */
class CZencoderOutput extends JObject
{
	public $base_url			= null;
	public $filename			= null;
	public $label				= null;
	public $notifications		= array();
	public $video_codec			= null;
	public $quality				= null;
	public $speed				= null;
	public $width				= null;
	public $height				= null;
	public $aspect_mode			= null;
	public $upscale				= null;
	public $audio_codec			= null;
	public $audio_quality		= null;
	public $thumbnails			= array();
	public $deinterlace			= null;
	public $max_frame_rate		= null;
	public $frame_rate			= null;
	public $keyframe_interval	= null;
	public $video_bitrate		= null;
	public $bitrate_cap			= null;
	public $buffer_size			= null;
	public $skip_video			= null;
	public $audio_bitrate		= null;
	public $audio_channels		= null;
	public $audio_sample_rate	= null;
	public $skip_audio			= null;
	public $autolevel			= null;
	public $deblock				= null;
	public $denoise				= null;
	public $public				= null;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function build()
	{
		$properties	= get_object_vars($this);
		
		// Clear empty values in the params array. 2 level down.
		foreach ($properties as $key => $value)
		{
			if (is_array($value))
			{
				foreach ($value as $subkey => $subvalue)
				{
					if ($subvalue===null || $subvalue==='')
					{
						unset($properties[$key][$subkey]);
					}
				}
			}
			if ($value===null || $value==='')
			{
				unset($properties[$key]);
			}
		}
		
		unset($properties['_errors']);
		
		return $properties;
	}
	
	/*
	 * Set Output Base URL
	 * A S3, FTP, or SFTP directory URL where we'll put the transcoded file.
	 */
	public function setBaseUrl($base_url)
	{
		$this->base_url = $base_url;
	}
	
	/*
	 * Set Output File Name
	 * The name of the output file. The extension is important, 
	 * and must match the output format/codecs
	 */
	public function setFilename($filename = null)
	{
		$this->filename = $filename;
	}
	
	/*
	 * Set Output Label
	 * A label for this output.
	 */
	public function setLabel($label = null)
	{
		$this->label = $label;
	}
	
	/*
	 * Set Notification URL
	 * A HTTP URL to receive the notification after a job is complete
	 */
	public function setNotificationUrl($notification_url = null)
	{
		$this->notifications[] = $notification_url;
	}
	
	/*
	 * Set Notification Email Address
	 * An email address to receive the notification after a job is complete
	 */
	public function setNotificationEmail($notification_email = null)
	{
		$this->notifications[] = $notification_email;
	}
	
	public function setNotifications($notifications=null)
	{
		$this->notifications = array_merge($this->notifications, (array)$notifications);
	}
	
	/*
	 * Set Video Codec
	 * The video codec to be used.
	 * 
	 * The video codec used in the video file can affect the ability to play 
	 * the video on certain devices. The default codec used is H.264 and should 
	 * only be changed if the playback device you are targeting requires 
	 * something different.
	 * 
	 * h264 | vp8 | theora | vp6
	 */
	public function setVideoCodec($video_codec = 'h264')
	{
		$this->video_codec = $video_codec;
	}
	
	/*
	 * Set Video Quality
	 * A target video quality. Affects bitrate and file size.
	 * 
	 * This is the desired output video quality. A higher quality setting will 
	 * mean higher bitrates and higher file sizes. A quality setting of 5 will 
	 * be nearly lossless and a setting of 1 will be quite compressed and may 
	 * not look great. Higher quality encoding is also a bit slower than lower 
	 * quality encoding. As a rule of thumb, lowering quality by a level will 
	 * reduce file size by about 40%. A quality setting of 3-4 usually looks 
	 * pretty good. Note that the actual bitrate will vary when using the 
	 * quality setting, depending on the type of video. Even at the same 
	 * quality setting, low-complexity video (like a screencast) will generally 
	 * result in lower bitrates than high-complexity video (like a movie).
	 * 
	 * 1 (Poor Quality, smaller file) | 2 | 3 | 4 | 5 (High Quality, larger file)	 	 
	 */
	public function setQuality($quality = 3)
	{
		$this->quality = intval($quality);
	}
	
	/*
	 * Set Video Speed
	 * Speed of encoding. Affects compression.
	 * 
	 * This is the desired speed of encoding. A lower speed setting will mean 
	 * slower encode times, but lower file sizes and better quality video. 
	 * A high speed setting will transcode quickly, but compression will be 
	 * less efficient and result in larger files with lower quality output.
	 * 
	 * 1 (Slow, better compression) | 2 | 3 | 4 | 5 (Fast, worse compression)	
	 */
	public function setSpeed($speed = 3)
	{
		$this->speed = intval($speed);
	}
	
	/*
	 * Set Video Width
	 * The maximum width of the output video (in pixels)
	 * 
	 * This is the maximum width of the output video specified as a positive 
	 * integer. In order for video compression to work properly the width 
	 * should be divisible by 4 (or even better 16).
	 */
	public function setWidth($width = null)
	{
		$this->width = intval($width);
	}
	
	/*
	 * Set Video Height
	 * The maximum height of the output video (in pixels).
	 * 
	 * This is the maximum height of the output video specified as a positive 
	 * integer. In order for video compression to work properly the height 
	 * should be divisible by 4 (or even better 16)
	 */
	public function setHeight($height = null)
	{
		$this->height = intval($height);
	}
	
	/*
	 * Set Aspect Mode
	 * What to do when aspect ratio of input file does not match the target 
	 * width/height aspect ratio.
	 * 
	 * preserve | crop | pad | stretch
	 */
	public function setAspectMode($aspect_mode = 'preserve')
	{
		$this->aspect_mode = $aspect_mode;
	}
	
	public function setUpscale($upscale = null)
	{
		$this->upscale = ($upscale) ? 1 : null;
	}
	
	/*
	 * Set Audio Codec
	 * The audio codec to be used
	 * 
	 * The audio codec used in the video file can affect the ability to play 
	 * the video on certain devices. The default codec used is AAC and should 
	 * only be changed if the playback device you are targeting requires 
	 * something different.
	 * 
	 * aac | mp3 | vorbis
	 */
	public function setAudioCodec($audio_codec = 'aac')
	{
		$this->audio_codec = $audio_codec;
	}
	
	/*
	 * Set Audio Quality
	 * A target audio quality. Affects bitrate and file size
	 * 
	 * 1 (Poor Quality, smaller file) | 2 | 3 | 4 | 5 (High Quality, larger file)
	 */
	public function setAudioQuality($audio_quality = 3)
	{
		$this->audio_quality = intval($audio_quality);
	}
	
	/*
	 * Set Thumbnails Number
	 * The number of thumbnails you want returned
	 * 
	 * A number of thumbnails to capture specified as a positive integer. We 
	 * will grab evenly-spaced thumbnails across the whole duration of the 
	 * file. So if you ask for 1 thumbnail, it will be near the middle of the 
	 * file. Specifying 3 thumbnails on an 8 minute video will result in 
	 * thumbnails at approximately 2, 4, and 6 minutes.
	 */
	public function setThumbnailsNumber($number = 1)
	{
		$this->thumbnails['number'] = intval($number);
	}
	
	/*
	 * Set Thumnails Size
	 * The size of the thumbnails as WIDTHxHEIGHT
	 * 
	 * A target resolution for the thumbnails, like "160×120". If no size is 
	 * provided, thumbnails will be the same size as the output video. By 
	 * default, we will preserve aspect ratio, so if the aspect ratio of this 
	 * size parameter does not match the aspect ratio of the movie, the 
	 * resulting file may not exactly match this size. If Stretch is set to 
	 * true for the corresponding output file, then this thumbnail will also be 
	 * stretched to fill the resolution.
	 */
	public function setThumbnailsSize($size)
	{
		$this->thumbnails['size'] = $size;
	}
	
	/*
	 * Set Thumbnails Base URL
	 * A S3, FTP, or SFTP URL where we'll put the thumbnails.
	 * 
	 * Currently, we only support Amazon S3 as an output location. Specify the 
	 * output URL (directory only, no filenames) starting with s3:// and we'll 
	 * send the thumbnails here. Make sure you grant write access to this 
	 * bucket to aws@zencoder.com. If you don't supply this field, we'll host 
	 * the file for you for 24 hours.
	 */
	public function setThumbnailsBaseUrl($base_url)
	{
		$this->thumbnails['base_url'] = $base_url;
	}
	
	/*
	 * Set Thumbnails Filename Prefix 
	 * A custom filename prefix.
	 * 
	 * Thumbnail files will be exported with sequential filenames. However, you 
	 * may specify a custom prefix for the thumbnail files. If no prefix is 
	 * specified we'll use frame.
	 */
	public function setThumbnailsPrefix($prefix)
	{
		$this->thumbnails['prefix'] = $prefix;
	}
	
	/*
	 * Set Public Readable
	 * Permissions to be applied to the output files and thumbnails, if using S3.
	 * Give the READ permission to all users.
	 */
	public function setThumbnailsPublic($isPublicly_Readable)
	{
		$this->thumbnails['public'] = $isPublicly_Readable;
	}
	
	/*
	 * Note that detect mode will auto-detect and deinterlace interlaced content.
	 * 
	 * detect | on | off
	 */
	public function setDeinterlace()
	{
	}
	
	/*
	 * A maximum frame rate cap (in frames per second).
	 */
	public function setMaxFrameRate()
	{
	}
	
	/*
	 * Force a specific output frame rate (in frames per second).
	 */
	public function setFrameRate()
	{
	}
	
	/*
	 * Creates a keyframe every n frames.
	 */
	public function setKeyframeInterval()
	{
	}
	
	/*
	 * A target bitrate in kbps. Not necessary if you select a Video Quality 
	 * setting, unless you want to target a specific bitrate.
	 */
	public function setVideoBitrate()
	{
	}
	
	/*
	 * A bitrate cap in kbps, used for streaming servers.
	 */
	public function setBitrateCap()
	{
	}
	
	/*
	 * The buffer size for the bitrate cap in kbps
	 */
	public function setBufferSize()
	{
	}
	
	/*
	 * The H.264 profile to be used.
	 */
	public function setH264Profile()
	{
	}
	
	/*
	 * The H.264 level to be used.
	 */
	public function setH264Level()
	{
	}
	
	/*
	 * Return an audio-only file.
	 */
	public function setSkipVideo()
	{
	}
	
	/*
	 * Total audio bitrate in kbps
	 */
	public function setAudioBitrate()
	{
	}
	
	/*
	 * The number of audio channels.
	 */
	public function setAudioChannel()
	{
	}
	
	/*
	 * The sample rate of the audio in hertz.
	 */
	public function setAudioSampleRate()
	{
	}
	
	/*
	 * Return a video-only file.
	 */
	public function setSkipAudio()
	{
	}
	
	/*
	 * Automatic brightness / contrast correction
	 */
	public function setAutolevels()
	{
	}
	
	/*
	 * Apply deblocking filter. Useful for highly compressed or blocky input videos.
	 */
	public function setDeblock()
	{
	}
	
	/*
	 * Apply denoise filter.
	 * 
	 *	weak | medium | strong | strongest
	 */
	public function setDenoise()
	{
	}
	
	/*
	 * Set Public Readable
	 * Permissions to be applied to the output files and thumbnails, if using S3.
	 * Give the READ permission to all users.
	 */
	public function setPublic($isPublicly_Readable)
	{
		$this->public = $isPublicly_Readable;
	}
	
	/*
	 * Optimize the encoder for a certain content type.
	 */
	public function setTuning()
	{
	}
	
	/*
	 * The starting point of a subclip (in timecode or number of seconds).
	 */
	public function setStartClip()
	{
	}
	
	/*
	 * The length of the subclip (in timecode or number of seconds).
	 */
	public function setClipLength()
	{
	}
	
	/*
	 * Set Watermark
	 *
	 * TODO: set multiple watermarks
	 *
	 * url: The URL of a remote image file to use as a watermark.
	 * x | y : Where to place the watermark within the video, on the x axis (left/right). 
	 * Can be a number of pixels (e.g. 100 or -20) or a percent of the video 
	 * width (e.g. “25%” or “-5%”). Use a positive number to place relative to 
	 * the left side of the video, and a negative number to place relative to 
	 * the right side of the video. Use “-0″ (as a string) to lock to the right side.
	 * width: The width of the watermark, expressed as a number of pixels 
	 * (e.g. 64) or as a percent of the video width (e.g. “10%”)
	 */
	public function setWatermark($url='', $x='-10', $y='-10', $width='', $height='')
	{
		if ($url)
		{
			$this->watermark['url'] = $url;
			$this->watermark['x'] = $x;
			$this->watermark['y'] = $y;
			$this->watermark['width'] = $width;
			$this->watermark['height'] = $height;
		}
	}
}

class CZendcoderTest extends JObject
{
	public $_requestId	= null;
	public $_label		= null;
	public $_url		= null;
	
	public $test		= null;
	public $outputs		= array();
	public $id			= null;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function load($result)
	{
		$json	= new CJSON();
		$result	= $json->decode($result);
		
		foreach($this->getProperties() as $key => $value)
		{
			if (isset( $result->$key ))
			{
				$this->$key = $result->$key;
			}
		}
		
		// TODO: cater for multi-ouputs
		$output				= $this->outputs[0];
		$this->_requestId	= $output->id;
		$this->_label		= $output->label;
		$this->_url			= $output->url;
		unset($this->outputs);
	}
	
	public function getJobId()
	{
		return $this->id;
	}
	
	public function getURL()
	{
		return $this->_url;
	}
	
	public function getRequestId()
	{
		return $this->_requestId;
	}
	
	public function getLabel()
	{
		return $this->label;
	}
}

class CZencoderJob extends JObject
{
	public $test	= null;
	public $input	= null;
	public $output	= null;
	public $api_key	= null;
	private $jobUrl	= 'https://app.zencoder.com/api/jobs';
	private $config	= null;
	
	public function __construct($api_key=null)
	{
		parent::__construct();
		
		$this->config		= CFactory::getConfig();
		$this->api_key		= $this->config->get('zencoder_api_key', '');
	}
	
	public function create($input, $output, $test=false, $region=null, $download_connections=null)
	{	
		$params	= $this->_build($input, $output, $test, $region, $download_connections);
		
		$json	= new CJSON();
		$params	= $json->encode($params);
		
		$curl	= new CZenCoderCURL();
		if (!$curl->post($this->jobUrl, $params))
		{
			$this->setError($curl->getError());
			return false;
		}
		
		$result = $curl->getResults();
		$result	= $json->decode($result);
		if (isset($result->errors))
		{
			$this->setError($result->errors[0]);
			return false;
		}
		
		return true;
	}
	
	public function getProgress($output_id)
	{
		$url	= 'https://app.zencoder.com/api/outputs/'.$output_id.'/progress?api_key='. $this->api_key;
		
		$curl	= new CZenCoderCURL();
		if (!$curl->get($url))
		{
			return $curl->getError();
		}
		
		// expected result
		// {"current_event":"Uploading","state":"finished"}
		
		$json	= new CJSON();
		$result	= $json->decode($curl->getResults());
		
		return $result;
	}
	
	public function save(&$videoObj)
	{
	}
	
	private function _build($input, $output, $test=null, $region=null, $download_connections=null)
	{
		$params	= array();
		
		if ($test)
		{
			$params['test'] = 1;
		}
		
		$params['input']	= $input;
		$params['output']	= $output;
		$params['api_key']	= $this->api_key;
		
		if ($region)
		{
			// us | europe | asia
			$params['region'] = $region;
		}
		if ($download_connections)
		{
			// 1 to 25. default 5
			$params['download_connections'] = $download_connections;
		}
		
		return $params;
	}
}

class CJSON extends JObject
{
	private $_json		= null;
	
	public function __construct()
	{
		parent::__construct();
		
		if (!function_exists('json_encode'))
		{
			$this->_getJSON();
		}
	}
	
	public function encode($value)
	{
		return $this->_json ? $this->_json->encode($value) : json_encode($value);
	}
	
	public function decode($value)
	{
		return $this->_json ? $this->_json->decode($value) : json_decode($value);
	}
	
	private function _getJSON()
	{
		if (!is_object($this->_json))
		{
			require_once( JPATH_PLUGINS . DS . 'system' . DS . 'pc_includes' . DS . 'JSON.php');
			if (!class_exists('Services_JSON'))
			{
				$this->setError('Could not find library: Services_JSON');
				return false;
			}
			$this->_json = new Services_JSON();
		}
	}
}

// instead of using the current CRemoteHelper because we need custom
// header(Content-Type) and response code
class CZenCoderCURL extends JObject
{
	private $_statusCode	= null;
	private $_results		= null;
	private $_headers		= null;
	private $_body			= null;
	
	public function execute($url)
	{
		CFactory::load('helpers', 'remote');
		$response	= CRemoteHelper::getContent($url, true);
		list($headers,$body) = explode( "\r\n\r\n" , $response , 2 );
		return $body;
	}
	
	public function post($url, $data)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Accept: application/json"));
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
// 		$cert	= JPATH_COMPONENT . DS . 'libraries' . DS . 'zencoder' . DS . 'app.zencoder.com.crt';
// 		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
// 		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
// 		curl_setopt($ch, CURLOPT_CAINFO, $cert);
		
		$this->_results		= curl_exec($ch);
		$this->_statusCode	= curl_getinfo ($ch, CURLINFO_HTTP_CODE);
		
		if (curl_errno($ch) !== 0)
		{
			$this->setError( 'CURL error: ' . curl_errno($ch) . ' ' . htmlspecialchars(curl_error($ch)) );
			return false;
		}
		
		curl_close($ch);
		
		return true;
	}
	
	public function get($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		$this->_results		= curl_exec($ch);
		$this->_statusCode	= curl_getinfo ($ch, CURLINFO_HTTP_CODE);
		
		if (curl_errno($ch) !== 0)
		{
			$this->setError( 'CURL error: ' . curl_errno($ch) . ' ' . htmlspecialchars(curl_error($ch)) );
			return false;
		}
		
		curl_close($ch);
		
		return true;
	}
	
	public function getStatusCode()
	{
		return $this->_statusCode;
	}
	
	public function getResults()
	{
		return $this->_results;
	}
}