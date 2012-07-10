<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/


// Dont allow direct linking
defined('_JEXEC') or die( 'Direct Access to this location is not allowed.' );

define( '_AEC_MI_NAME_JOMSOCIALJSPT',		'JomSocial-JSPT' );
define( '_AEC_MI_DESC_JOMSOCIALJSPT',		'Choose the default profile type for a user.' );
define( '_MI_MI_JOMSOCIALJSPT_SET_PROFILETYPE_NAME',				'Set JSPT Profiletype' );
define( '_MI_MI_JOMSOCIALJSPT_SET_PROFILETYPE_DESC',				'Choose Yes if you want this MI to set the profiletype when it is called.' );
define( '_MI_MI_JOMSOCIALJSPT_PROFILETYPE_NAME',					'Select Profile Type Name' );
define( '_MI_MI_JOMSOCIALJSPT_PROFILETYPE_DESC',					'The Profile type name that you want the user to be in.' );
define( '_MI_MI_JOMSOCIALJSPT_SET_PROFILETYPE_AFTER_EXP_NAME',			'Set Expiration profiletype.' );
define( '_MI_MI_JOMSOCIALJSPT_PROFILETYPE_AFTER_EXP_NAME',			'Expiration profiletype' );
define( '_MI_MI_JOMSOCIALJSPT_SET_PROFILETYPE_AFTER_EXP_DESC',			'Choose Yes if you want this MI to set the profile type when the calling payment plan expires.' );
define( '_MI_MI_JOMSOCIALJSPT_PROFILETYPE_AFTER_EXP_DESC',			'The Profile type name that you want the user to be in when plan expires.' );

class mi_jomsocialjspt
{
	function Info()
	{
		$info = array();
		$info['name'] = _AEC_MI_NAME_JOMSOCIALJSPT;
		$info['desc'] = _AEC_MI_DESC_JOMSOCIALJSPT;

		return $info;
	}

	function detect_application()
	{
		if(!is_dir( JPATH_ROOT. DS . 'components'. DS .'com_community' ))
			return false;
			
		if(!is_dir( JPATH_ROOT. DS . 'components'. DS .'com_xipt' ))
			return false;

		//require_once ( JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'includes.php');
		return true;
	}

	function Settings()
	{
		//require_once ( JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'includes.php');
		require_once ( JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'api.xipt.php');
		
		$database	=& JFactory::getDBO();
        $settings = array();
		$settings['profiletype']				= array( 'list' );
		$settings['profiletype_after_exp'] 		= array( 'list' );

		$filter = array ('published'=>1);
		
		//new concept
		$profiletypes = XiptAPI::getProfiletypeInfo(0, $filter);
	 	
		//old concept
		//$profiletypes = XiptLibProfiletypes::getProfiletypeArray($filter);

		$spt = array();
		$spte = array();

		$ptype = array();
		foreach($profiletypes as $profiletype ) {
			$ptype[] = JHTML::_('select.option', $profiletype->id, $profiletype->name );
			if ( !empty( $this->settings['profiletype'] ) ){
				if ( in_array( $profiletype->id, $this->settings['profiletype'] ) ) {
					$spt[] = JHTML::_('select.option', $profiletype->id, $profiletype->name );
				}
			}

			if ( !empty( $this->settings['profiletype_after_exp'] ) ) {
				if ( in_array( $profiletype->id, $this->settings['profiletype_after_exp'] ) ) {
					$spte[] = JHTML::_('select.option', $profiletype->id, $profiletype->name );
				}
			}
		}

		$settings['lists']['profiletype']			= JHTML::_('select.genericlist', $ptype, 'profiletype[]', 'size="4"' , 'value', 'text', $spt );
		$settings['lists']['profiletype_after_exp'] 	= JHTML::_('select.genericlist', $ptype, 'profiletype_after_exp[]', 'size="4"', 'value', 'text', $spte );

		return $settings;
	}

	function action( $request )
	{
		if ( !empty( $this->settings['profiletype'] ) ) {
				$this->setUserProfiletype( $request->metaUser->userid, $this->settings['profiletype'][0] );
		}

		return true;
	}

	function expiration_action( $request )
	{
		if ( !empty( $this->settings['profiletype_after_exp'] ) ) {
				$this->setUserProfiletype( $request->metaUser->userid, $this->settings['profiletype_after_exp'][0] );
		}

		return true;
	}


	function setUserProfiletype($userId,$pId)
	{
		if($this->detect_application()==false)
			return;
			
		//IMP : if MI are attached but subscription_message is set to false
		// then dont apply any action 
		
		//old
		//$subscription_message =  XiptFactory::getSettings('subscription_message');
		//new
		require_once ( JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'api.xipt.php');
		$integrate_with = XiptAPI::getGlobalConfig('integrate_with');	
		if($integrate_with == 'aec')
			XiptAPI::setUserProfiletype($userId, $pId, 'ALL');

		return;
	}

	function saveparams( $request )
	{
		//save all data in xipt_aec table
		$db =& JFactory::getDBO();

		$planid = $this->id;
		$mi_jspthandler = new jomsocialjspt_restriction( $db );

		$id = $mi_jspthandler->getIDbyPlanId( $planid );

		$mi_id = $id ? $id : 0;
		$mi_jspthandler->load( $mi_id );

		$mi_jspthandler->planid = $planid;
		$mi_jspthandler->profiletype = $request['profiletype'][0];

		$mi_jspthandler->check();
		$mi_jspthandler->store();

		return $request;
	}

}


class jomsocialjspt_restriction extends JTable {
	/** @var int Primary key */
	var $id						= null;
	/** @var int */
	var $planid		 			= null;
	/** @var int contain micro-integration id  */
	var $profiletype 			= null;
	/** @var int */

	function jomsocialjspt_restriction( &$db ) {
		parent::__construct( '#__xipt_aec', 'id', $db );
	}

	function getIDbyPlanId( $planid ) {
		$db = &JFactory::getDBO();

		$query = 'SELECT '.$db->nameQuote('id')
			. ' FROM '.$db->nameQuote('#__xipt_aec')
			. ' WHERE '.$db->nameQuote('planid').'=' .$db->Quote($planid);

		$db->setQuery( $query );
		return $db->loadResult();
	}
}