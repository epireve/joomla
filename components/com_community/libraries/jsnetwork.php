<?php
/**
 * @package		JomSocial
 * @subpackage	Core 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class JSNetworkLibrary
{
	
	public function submitToJomsocial()
	{
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_community' . DS . 'models' . DS . 'network.php');
		$model		= new CommunityModelNetwork();
		$network	=& $model->getJSNInfo();

		// to run or not to run?
		if( empty($network['network_enable']) ) return;
		if( $network['network_cron_freq'] )
		{
			$time_diff = time() - $network['network_cron_last_run'];
			$cron_freq = $network['network_cron_freq'] * 60 * 60; // 1 hour
			
			if( $time_diff < $cron_freq ) return;
		}
		
		// prepare data
		foreach ($network as $key => $value)
		{
			$token = JUtility::getToken();
			$keys = array( 'network_site_name', 'network_description', 'network_keywords', 
			'network_language', 'network_member_count', 'network_group_count', 
			'network_site_url', 'network_join_url', 'network_logo_url' ); 
			
			if( in_array($key, $keys) ) {
				$key	= CString::str_ireplace('network_', '', $key);
				$input_filtered[$key] = $value;
			}
		}
		if(!defined('SERVICES_JSON_SLICE')) {
			include_once( AZRUL_SYSTEM_PATH . '/pc_includes/JSON.php');
		}
		$json = new Services_JSON();
		$json_output = $json->encode( $input_filtered );
		
		// post data
		$post_data	= array();
		$post_data['jsonText'] = $json_output;
		$config		= CFactory::getConfig();
		$post_url	= $config->get('jsnetwork_path');
		
		CFactory::load('helpers', 'remote');
		$test	= CRemoteHelper::post($post_url, $post_data);
		
		// save
		$network['network_cron_last_run']	= time();
		$token	= JUtility::getToken();
		$network[$token]	= 1;
		// set post data
		foreach ($network as $key => $value)
		{
			JRequest::setVar( $key, $value, 'POST' );
		}
		$model->save();
		
	}
	
}
