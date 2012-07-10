<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Jom Social Table Model
 */
class CommunityTableProfiles extends JTable
{
	var $id				= null;
	var $type			= null;
	var $ordering		= null;
	var $published		= null;
	var $min			= null;
	var $max			= null;
	var $name			= null;
	var $tips			= null;
	var $visible		= null;
	var $required		= null;
	var $searchable		= null;
	var $options		= null;
	var $registration	= null;
	var $params			= null;
	
	/** UNIQUE KEY that stores the field code type **/
	var $fieldcode	= null;
	
	public function __construct(&$db)
	{
		parent::__construct('#__community_fields','id', $db);
	}
	
	/**
	 * Overrides Joomla's load method so that we can define proper values
	 * upon loading a new entry
	 * 
	 * @param	int	id	The id of the field
	 * @param	boolean isGroup	Whether the field is a group
	 * 	 
	 * @return boolean true on success
	 **/
	public function load( $id , $isGroup = false )
	{
		if( $id == 0 )
		{
			$this->id				= 0;
			$this->type				= ( $isGroup ) ? 'group' : '';
			$this->published		= true;
			$this->visible			= true;
			$this->required			= true;
			$this->name				= '';
			$this->tips				= '';
			$this->min				= 10;
			$this->max				= 100;
			$this->options			= '';
			$this->registration		= true;
		}
		else
		{
			parent::load( $id );
		}
	}

	/**
	 * Overrides Joomla's JTable delete method so that we can update the ordering
	 * upon deleting
	 * 
	 * @return boolean true on success
	 **/
	public function delete()
	{
		$db		=& $this->getDBO();
		
		$query	= "UPDATE " . $db->nameQuote( '#__community_fields' ) . ' '
				. 'SET '. $db->nameQuote('ordering') . ' = ('. $db->nameQuote('ordering') . ' -1 ) '
				. 'WHERE ' . $db->nameQuote( 'ordering' ) . '>' . $this->ordering;
				
		$db->setQuery( $query );
		$db->query();
		
		return parent::delete();
	}

	/**
	 * Overrides Joomla's JTable store method so that we can define proper values
	 * upon saving a new entry
	 * 
	 * @return boolean true on success
	 **/
	public function store( $groupOrdering = '' )
	{
		$db		=& $this->getDBO();

		// Update old field codes to empty if they exists
		if( !empty( $this->fieldcode ) )
		{
			$query	= "UPDATE " . $db->nameQuote( '#__community_fields' ) . ' '
					. 'SET ' . $db->nameQuote( 'fieldcode' ) . '=' . $db->Quote( '' ) . ' '
					. 'WHERE ' . $db->namequote( 'fieldcode' ) . '=' . $db->Quote( $this->fieldcode );
			
			$db->setQuery( $query );
			$db->query();
		}

		// For new groups, we need to get the max ordering
		if( $this->type == 'group' )
		{
			if( $this->id == 0 )
			{
				// Set the ordering
				$query	= 'SELECT MAX(' . $db->nameQuote('ordering') . ') FROM ' . $db->nameQuote('#__community_fields');
				
				$db->setQuery( $query );
				$this->ordering	= $db->loadResult() + 1;
			}
		}
		else
		{
			// If this is a new field, alway put it at the end
			if( $this->id == 0 )
			{
				
				// Get id of the next group
				// Set the ordering
				$query	= 'SELECT MIN(' . $db->nameQuote('ordering') . ') FROM ' . $db->nameQuote('#__community_fields')
						. 'WHERE ' . $db->nameQuote('ordering') . ' > ' . $db->Quote($groupOrdering)
						. ' AND '. $db->nameQuote('type') . ' = ' . $db->Quote('group');
						
				$db->setQuery( $query );
				$targetOrdering	= $db->loadResult();
				
				// If empty, we just put it at the very last item
				if( empty($targetOrdering) ){
					$query	= 'SELECT MAX(' . $db->nameQuote('ordering') . ') FROM ' . $db->nameQuote('#__community_fields');
				
					$db->setQuery( $query );
					$this->ordering	= $db->loadResult() + 1;
				}
				
				// If not empty, move everything else downwards and use the target group ordering
				else 
				{
					// Now increment the rest of the ordering.
					$query	= 'UPDATE ' . $db->nameQuote( '#__community_fields' ). ' '
							. 'SET ' . $db->nameQuote('ordering') . '=' . $db->nameQuote('ordering') . ' + 1 '
							. 'WHERE ' . $db->nameQuote('ordering') . ' >= ' . $db->Quote($targetOrdering);
							//$firstItemOrdering;
					$db->setQuery( $query );
					$db->query();
					
					$this->ordering = $targetOrdering;
				}
			}
			
			/*
			elseif (!empty( $groupOrdering ) && $this->id == 0 ) 
			{
					// Get the last ordering for this groups item
					// Now increment the rest of the ordering.
					$query	= 'UPDATE ' . $db->nameQuote( '#__community_fields' ). ' '
							. 'SET ' . $db->nameQuote('ordering') . '=' . $db->nameQuote('ordering') . ' + 1 '
							. 'WHERE ' . $db->nameQuote('ordering') . ' > ' . $db->Quote($groupOrdering);
							//$firstItemOrdering;
					$db->setQuery( $query );
					$db->query();
					$this->ordering	= $groupOrdering + 1;
			} elseif($this->id != 0 && !empty($groupOrdering) && $this->getCurrentParent() != $groupOrdering ){
					//for an existing field item
					// descrease orderring of next field items
					if($this->ordering > $groupOrdering){
						//move field item up
						$query	= 'UPDATE ' . $db->nameQuote( '#__community_fields' ). ' '
								. 'SET ' . $db->nameQuote('ordering') . '=' . $db->nameQuote('ordering') . ' + 1 '
								. 'WHERE ' . $db->nameQuote('ordering') . ' > ' . $db->Quote($groupOrdering)
								. ' AND '. $db->nameQuote('ordering') . ' < ' . $db->Quote($this->ordering);
						$db->setQuery( $query );
						$db->query();
						$this->ordering	= $groupOrdering + 1;
					} else {
						//move field item down
						$query	= 'UPDATE ' . $db->nameQuote( '#__community_fields' ). ' '
								. 'SET ' . $db->nameQuote('ordering') . '=' . $db->nameQuote('ordering') . ' - 1 '
								. 'WHERE ' . $db->nameQuote('ordering') . ' <= ' . $db->Quote($groupOrdering)
								. ' AND '. $db->nameQuote('ordering') . ' > ' . $db->Quote($this->ordering);
						$db->setQuery( $query );
						$db->query();
						$this->ordering	= $groupOrdering;
					}
			}
			 *
			 */
		}
		
 		return parent::store();
	}

	public function getCurrentParent()
	{
		$db		=& $this->getDBO();
		
		$query	= 'SELECT ' . $db->nameQuote( 'ordering' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__community_fields' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'ordering' ) . '<' . $db->Quote($this->ordering) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'group' ) . ' '
				. 'ORDER BY ' . $db->nameQuote('ordering') . ' DESC';
		$db->setQuery( $query );
		
		return $db->loadResult();
	}
	
	/**
	 * Method to retrieve parent's ID
	 **/	 	
	public function getCurrentParentId()
	{
		$db		=& $this->getDBO();
		
		$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__community_fields' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'ordering' ) . '<' . $db->Quote($this->ordering) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'group' ) . ' '
				. 'ORDER BY ' . $db->nameQuote('ordering') . ' DESC';
		$db->setQuery( $query );

		return $db->loadResult();
	}
	
	/**
	 * Tests the specific field if value exists
	 * 
	 * @param	string	
	 **/
	public function _exists( $field , $value )
	{
		$db		=& $this->getDBO();
		$query	= 'SELECT COUNT(*) FROM '
				. $db->nameQuote( '#__community_fields' )
				. 'WHERE ' . $db->nameQuote( $field ) . '=' . $db->Quote( $value );
		
		$db->setQuery( $query );

		$result	= ( $db->loadResult() > 0 ) ? true : false ;
		
		return $result;
	}

	/**
	 * Bind AJAX data into object's property
	 * 
	 * @param	array	data	The data for this field
	 **/
	public function bindAjaxPost( $data )
	{
		// @todo: Need to check if all fields are valid!
		$this->name			= $data['name'];
		$this->tips			= isset($data['tips']) ? $data['tips'] : '';
		$this->type			= isset($data['type']) ? $data['type'] : '';
		$this->published 	= isset($data['published']) ? $data['published'] : '';
		$this->min			= isset($data['min']) ? $data['min'] : '';
		$this->max			= isset($data['max']) ? $data['max'] : '';
		$this->visible		= isset($data['visible']) ? $data['visible'] : '';
		$this->required		= isset($data['required']) ? $data['required'] : '';
		$this->options		= isset($data['options']) ? $data['options'] : '';
		$this->fieldcode 	= isset($data['fieldcode']) ? $data['fieldcode'] : '';
		$this->registration = isset($data['registration']) ? $data['registration'] : '';
	}

}