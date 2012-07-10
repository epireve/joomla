<?php
/**
 * @package		JomSocial
 * @subpackage	Core 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (AZRUL_SYSTEM_PATH.DS.'pc_includes'.DS.'JSON.php');

class CMapping
{
	const GEOCODE_URL = 'http://maps.google.com/maps/api/geocode/json?';
	const STATICMAP_URL = 'http://maps.google.com/maps/api/staticmap?';
	
	/**
	 * Restrive the data from cache table instead	 
	 */	 	
	private function _getCachedAddressData($address)
	{
		$location	=& JTable::getInstance( 'LocationCache' , 'CTable' );
		$location->load( $address );
		$location->store();
		return $location->data;
	}
	
	/**
	 * Fetch google map data refere to
	 * http://code.google.com/apis/maps/documentation/geocoding/#Geocoding	 
	 */	 	
	public function getAddressData($address)
	{
		CFactory::load('helpers', 'remote');
		
		
		$url = CMapping::GEOCODE_URL . 'address='.urlencode($address) .'&sensor=false';
		$content = CRemoteHelper::getContent($url);
		
		$status = null;	
		if(!empty($content))
		{
			$json = new Services_JSON();
			$status = $json->decode($content);
		}

		return $status;
	}
	
	/**
	 * Given a string address, we will try to validate and see if google think 
	 * it is allright
	 */	 	
	public function validateAddress($address)
	{
		
		CFactory::load('helpers', 'remote');
		$content = CMapping::_getCachedAddressData($address);
		$isValid = false;
		
		if(!empty($content)){
			$json = new Services_JSON();
			$status = $json->decode($content);
			// @todo: catch $content error
			$isValid = $status->status == 'OK';
		}
		return $isValid;
	}
	
	/**
	 * Return the code that will generate static map
	 * if width is '0', then it will draw at 100% width	 
	 */	 	
	public function drawStaticMap($address, $width, $height)
	{
		$elementid = 'map-'. md5($address);
		$elementid = substr($elementid, 0, 16);
		
		$data = CMapping::getAddressData($address);
		
		
		
		$url = CMapping::STATICMAP_URL . 'center=';
		$url .= urlencode($address).'&zoom=14&maptype=roadmap&sensor=false';
		
		if($data->status == 'OK'){
			$lat = $data->results[0]->geometry->location->lat;
			$long = $data->results[0]->geometry->location->lng;
			$url .= '&markers=color:blue|'.$lat.','.$long;
		}
		
		$html = '';
		if( !empty($width)){
			// No need for fancy javascript if the exact size is known
			$html = '<img src="' . $url .'&size='.  $width .'x'. $height.'" id="'.$elementid.'"/>';
			
		}else {
			// If we don't know the height, need some fancy javascript to calculate the width 
			// and fetch the image via javascript
			$html  = '<div style="width:100%;height:'.$height.'px" id="'.$elementid.'-wrap">
						<img src="" id="'.$elementid.'"/>
					  </div>';
			$html .= '<script type="text/javascript">'
					.'joms.jQuery(document).ready( function() {';
					
			if($width != 0)
				$html .= 'var width 	= \'' . $width . '\';';
			else
				$html .= 'var width 	= joms.jQuery(\'#'.$elementid.'-wrap\').width();';
	
	
			$html .= 'var height 	=' . $height . ';'
					.'var url		= \'' . $url .'&size=\' + width + \'x'.$height.'\';'
					.'joms.jQuery(\'#'.$elementid.'\').attr(\'src\', url);'
					.'});'
					.'</script>';
		}
		return $html;
	}
	
	/**
	 * Draw the triple zoomer map
	 * @param type $address
	 * @param type $width
	 * @param type $height 
	 */
	static public function drawZoomableMap($address, $width, $height){
		
		$tmpl = new CTemplate();
		$tmpl->set('address', $address)
				->set('width', $width)
				->set('height', $height);
		
		return $tmpl->fetch('map.zoom');
	}
	
	/**
	 * Draw google map on the target element
	 * It will add marker at the address	 
	 */	 	
	public function drawMap($targetId, $address, $showMarker = true, $title = '', $info = '')
	{
		// need to attach the google map js once. It doesn't really need
		// to be attached in the <head> section
		static $mapScriptLoaded = false;
		$html = '';
		if(!$mapScriptLoaded)
		{
			$html = '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>';
			$mapScriptLoaded = true;
		}
		CFactory::load( 'helpers' , 'string' );

		$html  .= '<script type="text/javascript">'
  				.'joms.jQuery(document).ready( function() {'
  				.'joms.maps.initialize(\''.$targetId.'\', \''. addslashes( $address ) .'\', \''. addslashes( $title ).'\', \''.$info.'\');'
		  		.'});'
  				.'</script>';
  		return $html;
	}
	
	/**
	 * Add marker point to the given address
	 */	 	 	
	public function addMarker($targetId, $address, $title ='', $info = '' )
	{
		$html = '';
		$data = CMapping::getAddressData($address);
		if($data){
			if($data->status == 'OK')
			{
				$lat = $data->results[0]->geometry->location->lat;
				$lng = $data->results[0]->geometry->location->lng;
				
				$html  = '<script type="text/javascript">'
  				.'joms.jQuery(document).ready( function() {'
  				.'joms.maps.addMarker(\''.$targetId.'\', '.$lat.', '.$lng.', \''.$title.'\', \''.$info.'\');'
		  		.'});'
  				.'</script>'; 
			}
  		}
  		return $html;
	}
        /**
         * Get the Formated address from google
         */
        public function getFormatedAdd($address)
        {
            $data = CMapping::getAddressData($address);

            return $data->results[0]->formatted_address;
        }
}