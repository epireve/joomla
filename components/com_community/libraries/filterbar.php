<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * to use CHTML::sort($array)  
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );

class CFilterBar
{
	public function getHTML( $url , $sortItems = array() , $defaultSort = '' , $filterItems = array() , $defaultFilter = '' )
	{
		$cleanURL	= $url;
		$uri		= &JFactory::getURI();
		$queries	= JRequest::get('GET');

		// If there is Itemid in the querystring, we need to unset it so that CRoute
		// will generate it's correct Itemid
		if( isset( $queries['Itemid'] ) )
		{
			unset( $queries['Itemid'] );
		} 	
		
		// Force link to start with first page
		if( isset($queries['limitstart']) )
		{
			unset($queries['limitstart']);
		}  
		
		if( isset($queries['start']) )
		{
			unset($queries['start']);
		}
		
		$selectedSort	= JRequest::getVar( 'sort', $defaultSort , 'GET' );
		$selectedFilter = JRequest::getVar( 'filter', $defaultFilter, 'GET' );
		
		$tmpl		= new CTemplate();
		$tmpl->set( 'queries'			, $queries );
		$tmpl->set( 'selectedSort' 		,  $selectedSort );
		$tmpl->set( 'selectedFilter' 	, $selectedFilter );
		$tmpl->set( 'sortItems' 		, $sortItems );
		$tmpl->set( 'uri'				, $uri );
		$tmpl->set( 'filterItems'		, $filterItems );
		
		return $tmpl->fetch( 'filterbar.html' );
	}
}