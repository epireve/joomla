<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
require_once (COMMUNITY_COM_PATH.DS.'libraries'.DS.'fields'.DS.'profilefield.php');
class CFieldsEmail extends CProfileField
{
	/**
	 * Method to format the specified value for text type
	 **/	 	
	public function getFieldData( $field )
	{
		$value = $field['value'];
		
		if( empty( $value ) )
			return $value;
		
		CFactory::load( 'helpers' , 'linkgenerator' );
		
		return CLinkGeneratorHelper::getEmailURL($value);
	}
	
	public function getFieldHTML( $field , $required )
	{
		// If maximum is not set, we define it to a default
		$field->max	= empty( $field->max ) ? 200 : $field->max;
		
		//get the value in param
		$params	= new CParameter($field->params);
		$style 				= $this->getStyle()?'':' style="' .$this->getStyle() . '" ';
		
		$class	= ($field->required == 1) ? ' required' : '';
		$class	.= $params->get('min_char') != '' && $params->get('max_char') != '' ? ' minmax_'.$params->get('min_char').'_'.$params->get('max_char') : '';
		$class	.= !empty( $field->tips ) ? ' jomNameTips tipRight' : '';
		CFactory::load( 'helpers' , 'string' );
		ob_start();
?>
	<input class="inputbox validate-profile-email<?php echo $class;?>" title="<?php echo CStringHelper::escape( JText::_( $field->tips ) );?>" type="text" value="<?php echo $field->value;?>" id="field<?php echo $field->id;?>" name="field<?php echo $field->id;?>" maxlength="<?php echo $field->max;?>" size="40" <?php echo $style;?>	 />
	<span id="errfield<?php echo $field->id;?>msg" style="display:none;">&nbsp;</span>
<?php
		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}
	
	public function isValid( $value , $required )
	{
		CFactory::load( 'helpers' , 'validate' );
		
		$isValid	= CValidateHelper::email( $value );

		if( !empty($value) && !$isValid )
		{
			return false;
		}
		//validate string length
		if(!$this->validLength($value)){
			return false;
		}		
		//validate allowed domain
		if(isset($this->params)){
			$allowed = $this->params->get('allowed');
			if($allowed){
				$delimiter = ';';
				$allowed_list = explode($delimiter,$allowed);
				$valid = false;
				if(count($allowed_list) > 0 ){
					foreach($allowed_list as $domain){
						if(CValidateHelper::domain( $value, $domain))
						{
							$valid = true;
						}
					}
				}
				if(!$valid){
					return false;
				}
			}
		}
		//validate backlist domain
		if(isset($this->params)){
			$blacklist = $this->params->get('blacklist');
			if($blacklist){
				$delimiter = ';';
				$blacklists = explode($delimiter,$blacklist);
				if(count($blacklists) > 0 ){
					foreach($blacklists as $domain){
						if(CValidateHelper::domain( $value, $domain))
						{
							return false;
						}
					}
				}
			}
		}		
		return true; 
	}
}