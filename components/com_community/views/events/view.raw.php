<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

class CommunityViewEvents extends CommunityView
{

	public function export( $event )
	{
		CFactory::load( 'helpers' , 'event' );
		$handler	= CEventHelper::getHandler( $event );
		
		if( !$handler->showExport() )
		{
			echo JText::_('COM_COMMUNITY_ACCESS_FORBIDDEN');
			return;
		}
		
		header('Content-type: text/Calendar');
		header('Content-Disposition: attachment; filename="calendar.ics"');
		
		$creator	= CFactory::getUser($event->creator);
		$offset		= $creator->getUtcOffset();
				
		$date = new JDate($event->startdate);
		$dtstart = $date->toFormat('%Y%m%dT%H%M%S');
		
		$date = new JDate($event->enddate);
		$dtend = $date->toFormat('%Y%m%dT%H%M%S');
		
		$url	= $handler->getFormattedLink( 'index.php?option=com_community&view=events&task=viewevent&eventid=' . $event->id , false , true );
		
		$tmpl	= new CTemplate();
		$tmpl->set( 'dtstart'	, $dtstart );
		$tmpl->set( 'dtend'		, $dtend );
		$tmpl->set( 'url'		, $url );
		$tmpl->set( 'event'			, $event );
		$raw	= $tmpl->fetch( 'events.ical' );
		unset( $tmpl );

		echo $raw;
		exit;
	}
	
	public function __construct()
	{
		$this->my	= CFactory::getUser();
		$this->model	= CFactory::getModel( 'events' );
	}
    
	public function display()
	{
		//         header('Content-type: application/json');
		$jsonObj    = new StdClass;

		$jsonObj->allEvents = $this->_getAllEvents();

		// Output the JSON data.
		echo json_encode( $jsonObj );
		exit;
	}
    
	private function _getAllEvents()
	{
		$mainframe= JFactory::getApplication();

		$rows     = $this->model->getEvents();
		$items    = array();

		foreach($rows as $row){

		$item               = new stdClass();

		$table              =& JTable::getInstance( 'Event' , 'CTable' );
		$table->bind($row);
		$table->thumbnail	= $table->getThumbAvatar();
		$table->avatar	= $table->getAvatar();
		$author             = CFactory::getUser($table->creator);

		$item->id           = $row->id;
		$item->created      = $row->created;
		$item->creator      = CStringHelper::escape($author->getDisplayname());
		$item->title        = $row->title;
		$item->description  = CStringHelper::escape($row->description);
		$item->location     = CStringHelper::escape($row->location);
		$tiem->startdate    = $row->startdate;
		$item->enddate      = $row->enddate;
		$item->thumbnail    = $table->thumbnail;
		$tiem->avatar       = $table->avatar;
		$item->ticket       = $row->ticket;
		$item->invited      = $row->invitedcount;
		$item->confirmed    = $row->confirmedcount;
		$item->declined     = $row->declinedcount;
		$item->maybe        = $row->maybecount;
		$item->latitude     = $row->latitude;
		$item->longitude    = $row->longitude;
		$items[]            = $item;
	    }

	    return $items;
	}

}
