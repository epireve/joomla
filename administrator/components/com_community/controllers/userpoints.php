<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Jom Social Component Controller
 */
class CommunityControllerUserPoints extends CommunityController
{
	public function __construct()
	{
		parent::__construct();
		
		$this->registerTask( 'publish' , 'savePublish' );
		$this->registerTask( 'unpublish' , 'savePublish' );
	}
	
	public function ajaxSaveRule($ruleId, $data)
	{
		$user	=& JFactory::getUser();

		if ( $user->get('guest')) {
			JError::raiseError( 403, JText::_('COM_COMMUNITY_ACCESS_FORBIDDEN') );
			return;
		}

		$response	= new JAXResponse();
		
		$row	=& JTable::getInstance( 'userpoints' , 'CommunityTable' );
		$row->load( $ruleId );		
		$row->bindAjaxPost( $data );
		
		
		$isValid = true;
		
// 		$this->rule_name		= $data['rule_name'];
// 		$this->rule_description	= $data['rule_description'];
// 		$this->rule_plugin		= $data['rule_plugin'];
// 		$this->access 			= $data['access'];
// 		$this->points			= $data['points'];
// 		$this->published		= $data['published'];		
		
		//perform validation here.
		if( empty( $row->rule_name ) )
		{
			$error		= JText::_('COM_COMMUNITY_USERPOINTS_USER_RULE_EMPTY_WARN');			
			$response->addScriptCall( 'joms.jQuery("#error-notice").html("' . $error . '");');
			$isValid	= false;
		}
		if( empty( $row->rule_description ) )
		{
			$error		= JText::_('COM_COMMUNITY_USERPOINTS_DESCRIPTION_EMPTY_WARN');			
			$response->addScriptCall( 'joms.jQuery("#error-notice").html("' . $error . '");');
			$isValid	= false;
		}
		if( empty( $row->rule_description ) )
		{
			$error		= JText::_('COM_COMMUNITY_USERPOINTS_DESCRIPTION_EMPTY_WARN');			
			$response->addScriptCall( 'joms.jQuery("#error-notice").html("' . $error . '");');
			$isValid	= false;
		}
		if( empty( $row->rule_plugin ) )
		{
			$error		= JText::_('COM_COMMUNITY_USERPOINTS_PLUGIN_EMPTY_WARN');			
			$response->addScriptCall( 'joms.jQuery("#error-notice").html("' . $error . '");');
			$isValid	= false;
		}
		
		if( $row->points == '' )
		{
			$error		= JText::_('COM_COMMUNITY_USERPOINTS_POINT_EMPTY_WARN');
			$response->addScriptCall( 'joms.jQuery("#error-notice").html("' . $error . '");');
			$isValid	= false;
		} 
		else
		{
			$regex = '/^(-?\d+)$/';
			if (! preg_match($regex, $row->points)) { 
				$error		= JText::_('COM_COMMUNITY_USERPOINTS_INTEGER_ONLY');			
				$response->addScriptCall( 'joms.jQuery("#error-notice").html("' . $error . '");');
				$isValid	= false;
			}
		}
		
		if( $isValid )
		{
			//save the changes
			$row->store();
			
			$acl   =& JFactory::getACL();
			
			$parent			= '';
			// Get the view
			$view		=& $this->getView( 'userpoints' , 'html' );
	
			if($ruleId != 0)
			{
				$name		= '<a href="javascript:void(0);" onclick="azcommunity.editRule(\'' . $row->id . '\');">' . $row->rule_name . '</a>';
				$publish	= $view->getPublish( $row , 'published' , 'userpoints,ajaxTogglePublish' );
				
				//$userlevel = $acl->get_group_name( $row->access, 'ARO' );
				
				$userlevel = '';			
				switch($row->access)
				{
					case PUBLIC_GROUP_ID : $userlevel = 'Public'; break;
					case REGISTERED_GROUP_ID : $userlevel = 'Registered'; break;
					case SPECIAL_GROUP_ID : $userlevel = 'Special'; break;
					default : $userlevel = 'Unknown'; break;
				}				

				// Set the parent id
				$parent		= $row->id;
				
				// Update the rows in the table at the page.
				//@todo: need to update the title in a way looks like Joomla initialize the tooltip on document ready
				$response->addAssign('name' . $row->id, 'innerHTML' , $name);
				$response->addAssign('description' . $row->id, 'innerHTML', $row->rule_description);
				$response->addAssign('plugin' . $row->id, 'innerHTML', $row->rule_plugin);
				$response->addAssign('access' . $row->id, 'innerHTML', $userlevel);
				$response->addAssign('points' . $row->id, 'innerHTML', $row->points);
				$response->addAssign('published' . $row->id, 'innerHTML', $publish);
				
			}
			else
			{
				$response->addScriptCall('window.location.href = "' . JURI::base() . 'index.php?option=com_community&view=userpoints";');
			}
			$response->addScriptCall('cWindowHide();');
		}		
		
		$response->sendResponse();
	}
	
	
	
	public function ajaxEditRule($ruleId)
	{		
		$user	=& JFactory::getUser();
		if ( $user->get('guest')) {
			JError::raiseError( 403, JText::_('COM_COMMUNITY_ACCESS_FORBIDDEN') );
			return;
		}
		
		// Load the JTable Object.
		$row	=& JTable::getInstance( 'userpoints' , 'CommunityTable' );
		$row->load( $ruleId );
		
		$accessObj	= new stdClass();
		$accessObj->access		= $row->access;
		$group	= JHTML::_('list.accesslevel', $accessObj);		

		
		$response	= new JAXResponse();
		ob_start();
?>
<div id="error-notice" style="color: red; font-weight:700;"></div>
<div style="clear: both;"></div>
<div id="progress-status"  style="overflow:auto; height:99%;">
<form action="#" method="post" name="editRule" id="editRule">
	<table cellspacing="0" class="admintable" border="0" width="100%">
		<tbody>
			<tr>
				<td class="key" style="width:25%;"><?php echo JText::_('COM_COMMUNITY_USERPOINTS_ACTION_STRING');?></td>
				<td>:</td>
				<td>
					<?php echo $row->action_string;?>
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('COM_COMMUNITY_USERPOINTS_RULE_DESCRIPTION');?></td>
				<td>:</td>
				<td>
					<?php echo $row->rule_description;?>
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('COM_COMMUNITY_USERPOINTS_PLUGIN');?></td>
				<td>:</td>
				<td>
					<?php echo $row->rule_plugin;?>
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('COM_COMMUNITY_USERPOINTS_USER_ACCESS');?></td>
				<td>:</td>
				<td>
					<span>
						<?php echo $group; ?>
					</span>
				</td>		
			</tr>		
			<tr>
				<td class="key"><?php echo JText::_('COM_COMMUNITY_PUBLISHED');?></td>
				<td>:</td>
				<td>
					<span><?php echo $this->_buildRadio($row->published, 'published', array('Yes', 'No'));?></span>
				</td>		
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('COM_COMMUNITY_USERPOINTS_POINTS');?></td>
				<td>:</td>
				<td>
					<input type="text" value="<?php echo $row->points;?>" name="points" size="10" />
				</td>		
			</tr>
		</tbody>
	</table>
</form>
</div>
<?php

		$contents	= ob_get_contents();
		ob_end_clean();

		$buttons	= '<input type="button" class="button" onclick="javascript:azcommunity.saveRule(\'' . $row->id . '\');return false;" value="' . JText::_('COM_COMMUNITY_SAVE') . '"/>';
		$buttons	.= '&nbsp;&nbsp;<input type="button" class="button" onclick="javascript:cWindowHide();" value="' . JText::_('COM_COMMUNITY_CANCEL') . '"/>';
		$response->addAssign( 'cWindowContent' , 'innerHTML' , $contents );		
		$response->addAssign( 'cwin_logo' , 'innerHTML' , $row->rule_name );		
		$response->addScriptCall( 'cWindowActions' , $buttons );
		//$response->addScriptCall("jQuery('#cWindowContent').css('overflow','auto');");
		return $response->sendResponse();
		
	}
	
	public function ajaxRuleScan()
	{
		$const_file	= 'jomsocial_rule.xml';
		$user	=& JFactory::getUser();		
		
		if ( $user->get('guest')) {
			JError::raiseError( 403, JText::_('COM_COMMUNITY_ACCESS_FORBIDDEN') );
			return;
		}
		
		$newRules = array();
		$pathToScan = array( 0 =>	'components',
							1 =>	'modules',
							2 =>	'plugins');
		
		
		foreach($pathToScan as $scan)
		{
			$scan_path		= JPATH_ROOT . DS .$scan;
			
			if(! JFolder::exists($scan_path))
				continue;
			
			$scan_folders	= JFolder::folders($scan_path, '.', false, true);
		
			foreach($scan_folders as $folder)
			{
				$xmlRuleFile = $folder . DS . $const_file;
				if(JFile::exists($xmlRuleFile))
				{
					$parser =& JFactory::getXMLParser('Simple');
					$parser->loadFile($xmlRuleFile);
					
					$eleCom		=& $parser->document->getElementByPath('component');
					$component 	= (empty($eleCom)) ? '' : $eleCom->data();
					
					$eleRoot =& $parser->document->getElementByPath('rules');
					
					$cnt = 0;
					if(! empty($eleRoot))
					{
						foreach($eleRoot->children() as $rule)
						{
						
						    $ele 	=& $rule->getElementByPath('name');
						    $name 	= (empty($ele)) ? '' : $ele->data();
						    
						    $ele 		=& $rule->getElementByPath('description');
						    $description = (empty($ele)) ? '' : $ele->data();
							
						    $ele 			=& $rule->getElementByPath('action_string');
						    $action_string 	= (empty($ele)) ? '' : $ele->data();
							
						    $ele 		=& $rule->getElementByPath('publish');
						    $publish 	= (empty($ele)) ? 'false' : $ele->data();
							
						    $ele 	=& $rule->getElementByPath('points');
						    $points = (empty($ele)) ? '0' : $ele->data();
							
						    $ele 			=& $rule->getElementByPath('access_level');
						    $access_level 	= (empty($ele)) ? '1' : $ele->data();
						    
						    
// 						    echo 'name : '.$name."\n";
// 						    echo 'description : '.$description."\n";
// 						    echo 'component : '.$component."\n";
// 						    echo 'action_string : '.$action_string."\n";
// 						    echo 'publish : '.$publish."\n";
// 							echo 'points : '.$points."\n";
// 						    echo 'access_level : '.$access_level."\n";
// 							echo '------------------------'."\n\n\n";
							
							$tblUserPoints	=& JTable::getInstance( 'userpoints', 'CommunityTable' );
							
							if((! empty($action_string)) && (! $tblUserPoints->isRuleExist($action_string)))
							{
								$tblUserPoints->rule_name			= $name;
								$tblUserPoints->rule_description	= $description;
								$tblUserPoints->rule_plugin			= $component;
								$tblUserPoints->action_string		= $action_string;
								$tblUserPoints->published			= ($publish == 'true') ? 1 : 0;
								$tblUserPoints->points				= $points;
								$tblUserPoints->access				= $access_level;
								if($tblUserPoints->store())
								{
									$newRules[] = $name;
								}
							}//end if
							
						}//end foreach
						
					}//end if
				}//end if
			}//end foreach
		}
		
		$response	= new JAXResponse();
		
		ob_start();
?>
<fieldset style="width: 85%; height: 85%;">
	<legend>User rule scan</legend>
	<div id="progress-status"  style="overflow:auto; height:95%;">
<?php 
	if(count($newRules) > 0){
?>
	New rules added during scan:
<?php	
		foreach($newRules as $newrule){ 
?>
			<li style="padding:2px"><?php echo $newrule; ?></li>
<?php 	
		}//end foreach
	} else { 
?>
	No new rules detected during the scan.
<?php } //end if else ?>

	</div>
</fieldset>	

<?php
		$contents	= ob_get_contents();
		ob_end_clean();
		$buttons	= '';
		
		if(count($newRules) > 0)
			$buttons	.= '<input type="button" class="button" onclick="javascript: location.reload();return false;" value="' . JText::_('COM_COMMUNITY_USERPOINTS_REFRESH') . '"/>';
			
		$buttons	.= '&nbsp;&nbsp;<input type="button" class="button" onclick="javascript:cWindowHide();" value="' . JText::_('COM_COMMUNITY_CANCEL') . '"/>';
		$response->addAssign( 'cWindowContent' , 'innerHTML' , $contents );
		$response->addScriptCall( 'cWindowActions' , $buttons );
		return $response->sendResponse();
	}
	
	public function ajaxTogglePublish( $id , $field )
	{
		return parent::ajaxTogglePublish( $id , $field , 'userpoints' );
	}
	
	public function removeRules()
	{
		$mainframe	=& JFactory::getApplication();
		$ids		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$count		= 0;
		$sysCount	= 0;
		
		$row	=& JTable::getInstance( 'userpoints' , 'CommunityTable' );
		
		foreach( $ids as $id )
		{
			$row->load( $id );
			
			if(! $row->system)
			{
				if(! $row->delete( $id ) )
				{
					// If there are any error when deleting, we just stop and redirect user with error.
					$message	= JText::sprintf('There are errors removing the selected rule: %1$s', $row->rule_name );
					$mainframe->redirect( 'index.php?option=com_community&view=userpoints' , $message);										
					exit;
				}
				else
				{
					$count++;
				}			
			}
			else
			{
				$sysCount++;
			}			
		}
		 				 
		$message	= JText::sprintf( '%1$s Rule(s) successfully removed.' , $count );
		 
		if($sysCount > 0)
		{
			if($count > 0)
			{
				$message .= JText::sprintf( ' However, %1$s Rule(s) failed to remove due to core rules are not removable.' , $sysCount );			
			}
			else{
				$message = JText::sprintf( '%1$s Rule(s) failed to remove due to core rules are not removable.' , $sysCount );			
			}			
		}	
 		$mainframe->redirect( 'index.php?option=com_community&view=userpoints' , $message );
	}
	
	/**
	 * Method to build Radio fields
	 * 
	 * @access	private
	 * @param	string
	 * 	 
	 * @return	string	HTML output
	 **/
	public function _buildRadio($status, $fieldname, $values){
		$html	= '<span>';
		
		if($status || $status == '1'){
			$html	.= '<input type="radio" name="' . $fieldname . '" value="1" checked="checked" />' . $values[0];
			$html	.= '<input type="radio" name="' . $fieldname . '" value="0" />' . $values[1];
		} else {
			$html	.= '<input type="radio" name="' . $fieldname . '" value="1" />' . $values[0];
			$html	.= '<input type="radio" name="' . $fieldname . '" value="0" checked="checked" />' . $values[1];	
		}
		$html	.= '</span>';
		
		return $html;
	}	
	
	/**
	 * Ajax functiion to handle ajax calls
	 */	 	
	public function _ajaxPerformAction( $actionId )
	{
		$objResponse	= new JAXResponse();
		$output			= '';
		
		// Require Jomsocial core lib
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );

		$language	=& JFactory::getLanguage();
		
		$language->load( 'com_community' , JPATH_ROOT );
		
		// Get the action data
		$action	=& JTable::getInstance( 'ReportsActions' , 'CommunityTable' );
		$action->load( $actionId );

		// Get the report data
		$report	=& JTable::getInstance( 'Reports' , 'CommunityTable' );
		$report->load( $action->reportid );
				
		$method		= explode( ',' , $action->action );
		$args		= explode( ',' , $action->args );

		if( is_array( $method ) && $method[0] != 'plugins' )
		{	
			$controller	= JString::strtolower( $method[0] );
			
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'controllers' . DS . 'controller.php' );
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'controllers' . DS . $controller . '.php' );
			
			$controller	= JString::ucfirst( $controller );
			$controller	= 'Community' . $controller . 'Controller';
			$controller	= new $controller();

			$output		= call_user_func_array( array( &$controller , $method[1] ) , $args );
		}
		else if( is_array( $method ) && $method[0] == 'plugins' )
		{
			// Application method calls
			$element	= JString::strtolower( $method[1] );
			
			require_once( CPluginHelper::getPluginPath('community',$element) . DS . $element . '.php' );
			
			$className	= 'plgCommunity' . JString::ucfirst( $element );

			$output		= call_user_func_array( array( $className , $method[2] ) , $args );
		}
		$objResponse->addAssign( 'cWindowContent' , 'innerHTML' , $output );

		// Delete actions
		$report->deleteChilds();
		
		// Delete the current report
		$report->delete();
		
		$objResponse->addScriptCall('joms.jQuery("#row' . $report->id . '").remove();');
		return $objResponse->sendResponse();
	}
}