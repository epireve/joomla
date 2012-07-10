<?php
/**
 * @package		JomSocial
 * @subpackage	Core 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class CICal
{
    /**
     * @access private
     * @var string 
     */
	private $_content	= '';

    /**
     * @access private
     * @var string 
     */
	private $_items		= array();

	/**
	 * Object construct.
	 * 
	 * @param	string	$content	The raw contents of the calendar data.	 
	 **/	
	public function __construct( $content )
	{
		$this->_content	= $content;
	}

	/**
	 * Initializes and processes the raw contents provided.
	 * 
	 * @return	Boolean		True on success and false otherwise.
	 **/
	public function init()
	{
		preg_match_all( '/BEGIN:VEVENT(.*)END:VEVENT\s/isU' , $this->_content , $matches );

		if( !empty( $matches[1] ) )
		{
			foreach($matches[0] as $raw )
			{
				$this->_items[]	= new CIcalItem( $raw );
			}
			return true;
		}
		return false;
	}
	
	/**
	 * Retrieves all the children items in the given calendar
	 * 
	 * @return	Array	An array of CICalItem objects.
	 **/
	public function getItems()
	{
		// For now, we will just return whatever that is needed. It could be
		// improvised in the future say triggering some apps?
		return $this->_items;
	}
}

class CICalItem
{
    /**
     * @access private
     * @var string 
     */
	private $_raw		= '';
	private $_title		= '';
	private $_description	= '';
	private $_location		= '';
	private $_startdate		= '';
	private $_enddate		= '';
	
	public function __construct( $raw )
	{
		// Raw codes
		$this->_raw		= $raw;
	}

	/**
	 * Retrieve the item's title
	 * 
	 * @return	string	The calendar's item title	 	 
	 **/	 	
	public function getTitle()
	{
		if( empty($this->_title ) )
		{
			// @rule: Match the title
			preg_match( '/SUMMARY:(.*)/i' , $this->_raw , $match );
			
			if( isset( $match[1] ) )
			{
				$this->_title	= JString::trim( $match[1] );
			}
		}
		return $this->_title;
	}

	/**
	 * Retrieve the item's description
	 * 
	 * @return	string	The calendar's item's description	 	 
	 **/	
	public function getDescription()
	{
		if( empty($this->_description ) )
		{
			// @rule: Match the description
			$match = array();
			//Description in multiple line and begin with a space
			preg_match( '/DESCRIPTION:((.*\n .*)*)\n/ismU' , $this->_raw , $match );
			if( isset( $match[1] ) )
			{
				$this->_description	= JString::trim( $match[1] );
			} else {
				//single line description
				unset($match);
				preg_match( '/DESCRIPTION:(.*)/i' , $this->_raw , $match );
				
				if( isset( $match[1] ) )
				{
					$this->_description	= JString::trim( $match[1] );
				}
			}
		}
		
		//strip out new line character
//		eval("\$str = \"$this->_description\";"); //Evaluate a string as PHP code
		$this->_description = str_replace('\,', ',', $this->_description);
		return str_replace("\r\n ",'', str_replace('\n',"\n", $this->_description));
	}

	/**
	 * Retrieve the item's location
	 * 
	 * @return	string	The calendar's item's location	 	 
	 **/
	public function getLocation()
	{
		if( empty( $this->_location ) )
		{
			// @rule: Match the description
			$match = array();
			//Description in multiple line and begin with a space
			preg_match( '/LOCATION:((.*\n .*)*)/' , $this->_raw , $match );
			
			//var_dump($match);exit;
			if( isset( $match[1] ) &&  !empty($match[1]))
			{
				$this->_location	= JString::trim( $match[1] );
			} else {
				//single line description
				unset($match);
				preg_match( '/LOCATION:(.*)/i' , $this->_raw , $match );

				if( isset( $match[1] ) )
				{
					$this->_location	= JString::trim( $match[1] );
				}
			}
		}
		
		return str_replace(array("\r", "\n", '\,'), array('', '', ',' ), $this->_location);
	}

	/**
	 * Retrieve the item's start date
	 * 
	 * @return	JDate	The calendar start date 	 
	 **/
	public function getStartDate()
	{
		if( empty( $this->_startdate ) )
		{
			// @rule: Match the start date
			preg_match( '/DTSTART;TZID=(.*)/i' , $this->_raw , $match );
			if( isset( $match[1] ) )
			{
				$timestamp	= JString::trim( $match[1] );
				
				preg_match( '/(.*\/.*):(.*)/i' , $timestamp , $match );
				$timezone	= $match[1];
				$startTime	= $match[2];
		
				$date	=& JFactory::getDate( $startTime );
				$this->_startdate 	= $date->toMySQL();
			}
			else
			{
				//all day event format
				preg_match( '/DTSTART;VALUE=DATE:(.*)/i' , $this->_raw , $match );
				if( isset( $match[1] ) ) {
					$startTime	= $match[1];
					

					//$date	=& JFactory::getDate( $startTime . 'T000000Z' );
					//$startTime = $startTime . 'T000000Z';
					$date	=& JFactory::getDate( $startTime );
					
					$this->_startdate 	= $date->toMySQL();
				
				} else {
					preg_match( '/DTSTART:(.*)/i' , $this->_raw , $match );
					if( isset( $match[1] ) )
					{
						$startTime	= $match[1];
				
						$date	=& JFactory::getDate( $startTime );
						$this->_startdate 	= $date->toMySQL();
					}
				}
			}
		}
		return $this->_startdate;
	}

	/**
	 * Retrieve the item's end date
	 * 
	 * @return	JDate	The calendar end date 	 
	 **/	
	public function getEndDate()
	{
		if( empty( $this->_enddate ) )
		{
			// @rule: Match the start date
			preg_match( '/DTEND;TZID=(.*)/i' , $this->_raw , $match );
			if( isset( $match[1] ) )
			{
				$timestamp	= JString::trim( $match[1] );
				
				preg_match( '/(.*\/.*):(.*)/i' , $timestamp , $match );
				$timezone	= $match[1];
				$startTime	= $match[2];
		
				$date	=& JFactory::getDate( $startTime );
				$this->_enddate	= $date->toMySQL();
			}
			else
			{
				//all day event format
				preg_match( '/DTEND;VALUE=DATE:(.*)/i' , $this->_raw , $match );
				if( isset( $match[1] ) ) {
					$endTime	= $match[1];
					$date	=& JFactory::getDate( trim($endTime) . 'T235959Z' );
					$this->_enddate 	= $date->toMySQL();
				
				} else { 
					preg_match( '/DTEND:(.*)/i' , $this->_raw , $match );

					if( isset( $match[1] ) )
					{
						$endTime	= $match[1];
						$date	=& JFactory::getDate( $endTime );
						$this->_enddate 	= $date->toMySQL();
					}
				}
			}
		}
		return $this->_enddate;
	}

        /**
	 * Retrieve the item's Summary
	 *
	 * @return	string	The calendar's item's Summary
	 **/
	public function getSummary()
	{
		if( empty( $this->_summary ) )
		{
			// @rule: Match the description
			preg_match( '/SUMMARY:(.*)/i' , $this->_raw , $match );

			if( isset( $match[1] ) )
			{
				$this->_summary	= $match[1];
			}
		}

		return str_replace("\r\n ",'', str_replace('\n',"\n", $this->_summary));
	}
}