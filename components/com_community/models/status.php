<?php
/**
 * @category	Model
 * @package		JomSocial
 * @subpackage	News feed
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'models' . DS . 'models.php' );

/**
 *
 */ 
class CommunityModelStatus extends JCCModel
{
	/**
	 * Update the user status
	 * 
	 * @param	int		user id
	 * @param	string	the message. Should be < 140 char (controller check)	 	 	 
	 */ 	 	
	public function update($id, $status, $access=0){
		$db	= &$this->getDBO();
		$my	= CFactory::getUser();
		// @todo: posted_on should be constructed to make sure we take into account
		// of Joomla server offset
		
		// Current user and update id should always be the same
		CError::assert( $my->id, $id, 'eq', __FILE__ , __LINE__ );
		
		// Trigger onStatusUpdate
		require_once( COMMUNITY_COM_PATH.DS.'libraries' . DS . 'apps.php' );
	
		$appsLib	=& CAppPlugins::getInstance();
		$appsLib->loadApplications();
		
		$args 	= array();
		$args[]	= $my->id;			// userid
		$args[]	= $my->getStatus();	// old status
		$args[]	= $status;			// new status
		$appsLib->triggerEvent( 'onProfileStatusUpdate' , $args );
		
		$today	=& JFactory::getDate();
		$data	= new stdClass();
		$data->userid		= $id;
		$data->status		= $status; 		
		$data->posted_on    = $today->toMySQL();
		$data->status_access= $access;
				
		$db->updateObject( '#__community_users' , $data , 'userid' );
		
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		
		return $this;
	}
	
	/**
	 * Get the user status
	 * 
	 * @param int	userid
	 * 
	 * @todo: this should return the status object. Use Jtable for this	 	 	 	 
	 */	 	
	public function get($id, $limit=1){
		$db	= &$this->getDBO();
		$config		= CFactory::getConfig();
		CFactory::load('helpers', 'owner');
		//enforce user's status privacy
		$andWhere = array();
		$andWhere[] = $db->nameQuote('userid').'='. $db->Quote($id);
		if ($config->get('respectactivityprivacy')){
			$my	= CFactory::getUser();
			if($my->id == 0){
				// for guest, it is enough to just test access <= 0
				$andWhere[] = '('.$db->nameQuote('status_access').' <= 10)';
				
			}elseif( ! COwnerHelper::isCommunityAdmin($my->id) )
			{
				$orWherePrivacy = array();
				$orWherePrivacy[] = '(' . $db->nameQuote('status_access') .' = 0) ';
				$orWherePrivacy[] = '(' . $db->nameQuote('status_access') .' = 10) ';
				$orWherePrivacy[] = '((' . $db->nameQuote('status_access') .' = 20) AND ( '.$db->Quote($my->id) .' != 0)) ' ;
				if($my->id != 0)
				{
					$orWherePrivacy[] = '((' . $db->nameQuote('status_access') .' = ' . $db->Quote(40).') AND (' . $db->Quote($id) .' = ' . $db->Quote($my->id).')) ' ;
					$orWherePrivacy[] = '((' . $db->nameQuote('status_access') .' = ' . $db->Quote(30).') AND ((' . $db->Quote($my->id) .'IN (SELECT c.' . $db->nameQuote('connect_to')
							.' FROM ' . $db->nameQuote('#__community_connection') .' as c'
							.' WHERE c.' . $db->nameQuote('connect_from') .' = ' . $db->Quote($id)
							.' AND c.' . $db->nameQuote('status') .' = ' . $db->Quote(1) .' ) ) OR (' . $db->Quote($id) .' = ' . $db->Quote($my->id).') )) ';
				}
				$OrPrivacy = implode(' OR ', $orWherePrivacy);
				$andWhere[] = "(".$OrPrivacy.")";
			}
		}
		$whereAnd = implode(' AND ', $andWhere);
		$sql = 'SELECT * from '.$db->nameQuote('#__community_users')
			.' WHERE '. $whereAnd
			.' ORDER BY '.$db->nameQuote('posted_on').' DESC LIMIT '.$limit;
		
		$db->setQuery($sql);
		$result = $db->loadObjectList();
		
		// Return the first row
		if(!empty($result)){
			$result= $result[0];
		} else {
			$result = new stdClass();
			$result->status = '';
		}
		
		
		return $result;
	}
	
}
