<?php
/**
 * @category	Model
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'models' . DS . 'models.php' );

class CommunityModelToolbar extends JCCModel
{
	/**
	 * Retrieve menu items in JomSocial's toolbar
	 * 
	 * @access	public
	 * @param
	 * 
	 * @return	Array	An array of #__menu objects.
	 **/
	public function getItems()
	{
		$config	=   CFactory::getConfig();
		$db	=   JFactory::getDBO();
		$menus	=   array();

		// For menu access
		$my		= CFactory::getUser();
				
		//joomla 1.6		
		$menutitlecol = ( !C_JOOMLA_15 ) ? 'title' : 'name' ;		

		$query	= 'SELECT a.'.$db->nameQuote('id').', a.'.$db->nameQuote('link').', a.' . $menutitlecol . ' as name, a.'.$db->nameQuote(TABLE_MENU_PARENTID).', false as script '
				. ' FROM ' . $db->nameQuote( '#__menu' ) . ' AS a '
				. ' LEFT JOIN ' . $db->nameQuote( '#__menu' ) . ' AS b '
				. ' ON b.'.$db->nameQuote('id').'=a.'.$db->nameQuote(TABLE_MENU_PARENTID)
				. ' AND b.' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 ) . ' '
				. ' WHERE a.' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 ) . ' '
				. ' AND a.' . $db->nameQuote( 'menutype' ) . '=' . $db->Quote( $config->get( 'toolbar_menutype') );		

		if( $my->id == 0 )
		{
			$query	.= ' AND a.' . $db->nameQuote( 'access' ) . '=' . $db->Quote( 0 );
		}
		
		CFactory::load( 'helpers' , 'owner' );

		if( $my->id > 0 && !COwnerHelper::isCommunityAdmin() )
		{
//			$query	.= ' AND a.' . $db->nameQuote( 'access' ) . '>=' . $db->Quote( 0 ) . ' AND a.' . $db->nameQuote( 'access' ) . '<' . $db->Quote( 2 );
			//we haven't supported access level setting for toolbar menu yet.
			$query	.= ' AND a.' . $db->nameQuote( 'access' ) . '>=' . $db->Quote( 0 ) ;
		}
		
		if( COwnerHelper::isCommunityAdmin() )
		{
			$query	.= ' AND a.' . $db->nameQuote( 'access' ) . '>=' . $db->Quote( 0 );
		}

		$ordering_field = TABLE_MENU_ORDERING_FIELD;
		$query	.= ' ORDER BY a.'.$db->nameQuote($ordering_field);
				
		$db->setQuery( $query );
		
		$result	= $db->loadObjectList();
		
		// remove disabled apps base on &view=value in result's link
		$this->cleanMenus($result);

		//avoid multiple count execution
		$parentColumn	= TABLE_MENU_PARENTID;
		$menus			= array();
		
		foreach($result as $i => $row){
			//get top main links on toolbar

                        //add Itemid if not our components and dont add item id for external link
                        $row->link = CString::str_ireplace( 'https://' , 'http://' , $row->link );
                        if(strpos($row->link,'com_community') == false && strpos($row->link,'http://') === false){
                            $row->link .="&Itemid=".$row->id;
                        }

			if( $row->$parentColumn == MENU_PARENT_ID )
			{
				$obj				= new stdClass();
				$obj->item			= $row;
				$obj->item->script	= false;
				$obj->childs		= null;

				$menus[ $row->id ]	= $obj;
			}
		}

		// Retrieve child menus from the original result.
		// Since we reduce the number of sql queries, we need to use php to split the menu's out
		// accordingly.
		foreach($result as $i => $row){
			if( $row->$parentColumn != MENU_PARENT_ID && isset( $menus[ $row->$parentColumn]) )
			{
				if( !is_array( $menus[ $row->$parentColumn ]->childs ) )
				{
					$menus[ $row->$parentColumn ]->childs = array();
				}
				$menus[ $row->$parentColumn ]->childs[]	= $row;
			}
		}
		return $menus;
	}
	
	/**
	 * Retrieves the current active menu.
	 * 
	 * @param	int	$menuId	The current menu id.
	 * 
	 * @return	int	Active menu id.	 	 	 	 
	 **/	 	
	public function getActiveId( $link )
	{
		$db		= JFactory::getDBO();
		$config	= CFactory::getConfig();
		$query	= 'SELECT `id`,'.$db->nameQuote(TABLE_MENU_PARENTID).' FROM ' . $db->nameQuote( '#__menu' ) . ' WHERE '
				. $db->nameQuote( 'menutype' ) . '=' . $db->Quote( $config->get( 'toolbar_menutype' ) ) . ' '
				. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 ) . ' '
				. 'AND ' . $db->nameQuote( 'link' ) . ' LIKE ' . $db->Quote( '%' . $link . '%' );

		$db->setQuery( $query );
		$result	= $db->loadObject();
		
		if( !$result )
		{
			return 0;
		}
		$parent_id = TABLE_MENU_PARENTID;
		return ($result->$parent_id == 0 || (!C_JOOMLA_15 && $result->$parent_id == 1) ) ? $result->id : $result->$parent_id;
		//return $result->$parent_id == 0 ? $result->id : $result->$parent_id;
	}
	
	private function cleanMenus(&$menus)
	{
		// Load the apps state from config
		$config	= CFactory::getConfig();
		$apps	= array(
					// The major core apps
					'groups' => $config->get('enablegroups'), 
					'photos' => $config->get('enablephotos'), 
					'videos' => $config->get('enablevideos'), 
					'events' => $config->get('enableevents')
					);
		$subapp = array('linkVideo' => $config->get('enableprofilevideo') );
		
		$exception	= array();
		
		for ($i=0; $i<count($menus); $i++)
		{
			$menu	= $menus[$i];
			if (is_object($menu) && isset($menu->link))
			{
				// find the view from link
				
				preg_match('/&view=(\w+)/', $menu->link, $matches);
				
				if ($matches && isset($matches[1]))
				{
					foreach ($apps as $app => $enable)
					{
						if ($app == $matches[1] && !$enable)
						{
							$exception[]	= $i;
						}
					}
					unset($matches);
				}
				
				preg_match('/&task=(\w+)/', $menu->link, $matches);
				if ($matches && isset($matches[1])){
					foreach ($subapp as $app => $enable){
						if($app == $matches[1] && !$enable){
							$exception[]	= $i;
						}
					}
				}
			}
			unset($menu);
		}

		// Remove the disabled menu items
		if ($exception)
		{
			foreach ($exception as $i)
			{
				unset($menus[$i]);
			}
		}
	}
	
}
