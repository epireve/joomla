<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class CProfileField
{
	var $fieldId = null;
	var $params = null;
	public function __construct($fieldId=null){
		if ($fieldId!==null) {
			$this->load($fieldId);
		}
	}
	public function load($fieldId){
		if ($fieldId!==null) {
			$this->fieldId = $fieldId;
			$db		=& JFactory::getDBO();
			$query	= 'SELECT * FROM '.$db->nameQuote('#__community_fields')
					. ' WHERE '.$db->nameQuote('id').'='.$db->quote($this->fieldId);
			$db->setQuery($query);
			if($db->getErrorNum()) {
				JError::raiseError( 500, $db->stderr());
			}
			$field	= $db->loadObject();
			$this->params	= new CParameter($field->params);
		}		
	}
	public function validLength( $value )
	{
		if(isset($this->params)){
			$max_char = $this->params->get('max_char');
			$min_char = $this->params->get('min_char');
			$len = strlen($value);
			if($min_char && $len < $min_char ){
				return false;
			}
			if($max_char && $len > $max_char ){
				return false;
			}
		}
		return true;
	}
	public function getStyle(){
		if(isset($this->params)){
			$style = $this->params->get('style');
			return $style;
		}
		return '';
	}
}
