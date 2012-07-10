<?php
/**
 * @category	Model
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'models' . DS . 'models.php' );

class CommunityModelMailq extends JCCModel
{
	/**
	 * take an object with the send data
	 * $recipient, $body, $subject, 	 
	 */	 	
	public function add($recipient, $subject, $body , $templateFile = '' , $params = '' , $status = 0, $email_type = '' )
	{
		$my  = CFactory::getUser();
		
		// A user should not be getting a notification email of his own action
		$bookmarkStr = explode('.',$templateFile);
		if ($my->id == $recipient && $bookmarkStr[1] != 'bookmarks' )
		{
			return $this;
		}
		
		$db	 = &$this->getDBO();
		
		
		$date =& JFactory::getDate();
		$obj  = new stdClass();
		
		$obj->recipient = $recipient;
		
		// This part does a url search in the email body for URL and automatically makes it a linked URL
		// pattern search must starts with www or protocal such as http or https
		$matchUrl = preg_match_all('/\b(([\w-]+:\/\/?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/)))/', $body, $matching);
		if ($matchUrl !== false && $matchUrl > 0)
		{
			for ($i = 0; $i < $matchUrl; $i++)
			{
				$body = str_replace($matching[0][$i], '<a href="'.$matching[0][$i].'">'.$matching[0][$i].'</a>', $body);
			}
		}
		
		$obj->body 		= $body;
		$obj->subject 	= $subject;
		$obj->template	= $templateFile;
		$obj->params	= ( is_object( $params ) && method_exists( $params , 'toString' ) ) ? $params->toString() : '';	
		$obj->created	= $date->toMySQL();
		$obj->status	= $status;
		$obj->email_type = $email_type;
		
		$db->insertObject( '#__community_mailq', $obj );
		
		return $this;
	}
	
	/**
	 * Restrive some emails from the q and delete it
	 */	 	
	public function get($limit = 100, $markAsSent = false )
	{
		$db	 = &$this->getDBO();
				
		$sql = 'SELECT * FROM '.$db->nameQuote('#__community_mailq').' WHERE '.$db->nameQuote('status').'='.$db->Quote('0').' LIMIT 0,' . $limit;

		$db->setQuery( $sql );
		$result = $db->loadObjectList();
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr() );
		}
		
		if( $markAsSent )
		{
			// lets immediately mark all as sent for now to minimise 
			// multiple email being sent at the same time
			$ids = array();
			foreach ($result as $row){
				$ids[] = $row->id;
			}

			if( !empty($ids)) {
				$ids = implode(',', $ids);
				$sql  = 'UPDATE '.$db->nameQuote('#__community_mailq').' SET '.$db->nameQuote('status').'='.$db->Quote('1').' WHERE '.$db->nameQuote('id').' IN ('. $ids.'); ';
				$db->setQuery( $sql );
				$db->query();
			}
		}
		
		return $result;
	}
	
	/**
	* Set the email status (0 = pending, 1 = sent/succesful, 2 = blocked)
	*/
	public function markEmailStatus($id, $statuscode = 1){
		$db	 = &$this->getDBO();
		
		$sql = 'SELECT * FROM '.$db->nameQuote('#__community_mailq').' WHERE '.$db->nameQuote('id').'=' . $db->Quote($id);
		$db->setQuery( $sql );
		$obj = $db->loadObject();
		
		$obj->status = $statuscode;
		$db->updateObject( '#__community_mailq', $obj, 'id' );
	}

	/**
	 * Change the status of a message
	 */	 	
	public function markSent($id)
	{
		return $this->markEmailStatus($id, 1);
	}
	
	public function purge(){
	}
	
	public function remove(){
	}
}
