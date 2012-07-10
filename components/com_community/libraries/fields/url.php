<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
require_once (COMMUNITY_COM_PATH.DS.'libraries'.DS.'fields'.DS.'profilefield.php');
class CFieldsUrl extends CProfileField
{
	/**
	 * Method to format the specified value for text type
	 **/	 	
	public function getFieldData( $field )
	{
		$value = $field['value'];
		
		if( empty( $value ) )
			return $value;
		
		return '<a rel="nofollow" href="' . $value . '" target="_blank">' . $value . '</a>';
	}
	
	public function getFieldHTML( $field , $required )
	{
		// If maximum is not set, we define it to a default
		$field->max	= empty( $field->max ) ? 200 : $field->max;

		$class	= ($field->required == 1) ? ' required' : '';
		$class	.= !empty( $field->tips ) ? ' jomNameTips tipRight' : '';
		$scheme	= '';
		$host	= '';
		$style 				= $this->getStyle()?'':' style="' .$this->getStyle() . '" ';
		if(! empty($field->value))
		{
			//value passed could be something like http://,www.example.com due to processing done at com_community/views/register/view.html.php .
			//Let's correct the format bfore passing to parse_url()
			$field->value = implode('', explode(',', $field->value));
			if (strlen(str_replace(array('http://', 'https://'), '', $field->value)) != 0) 
			{		
				$url	= parse_url($field->value);
			}
			$scheme	= isset( $url[ 'scheme' ] ) ? $url['scheme'] : 'http://';
			$host	= isset( $url[ 'host' ] ) ? $url['host'] : '';
			$path	= isset( $url[ 'path'] ) ? $url['path'] : '';
			$query	= isset( $url[ 'query'] ) ? '?' . $url['query'] : '';
			$fragment = isset( $url['fragment'] ) ? '#' . $url['fragment'] : '' ;
			$field->value	= $host . $path . $query . $fragment;
		}
		CFactory::load( 'helpers' , 'string' );
		ob_start();
?>
	<select name="field<?php echo $field->id;?>[]">
		<option value="http://"<?php echo ($scheme == 'http') ? ' selected="selected"' : '';?>><?php echo JText::_('http://');?></option>
		<option value="https://"<?php echo ($scheme == 'https') ? ' selected="selected"' : '';?>><?php echo JText::_('https://');?></option>
	</select>
	<input title="<?php echo CStringHelper::escape( $field->tips );?>" type="text" value="<?php echo $field->value;?>" id="field<?php echo $field->id;?>" name="field<?php echo $field->id;?>[]" maxlength="<?php echo $field->max;?>" size="40" class="jomNameTips tipRight inputbox validate-profile-url<?php echo $class;?>"<?php echo $style; ?> />
	<span id="errfield<?php echo $field->id;?>msg" style="display:none;">&nbsp;</span>
<?php
		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}
	
	public function isValid( $value , $required )
	{
		CFactory::load( 'helpers' , 'validate' );
		
		$isValid	= CValidateHelper::url( $value );
		
		$url		= parse_url( $value );
		$host		= isset($url['host']) ? $url['host'] : '';

		//validate url format
		if( !$isValid && $required )
			return false;
		else if( !empty($host) && !$isValid )
			return false; 

		//validate length
		if(!$this->validLength($value)){
			return false;
		}
		return true;
	}
	
	public function formatdata( $value )
	{
		if( empty( $value[0] ) || empty( $value[1] ) )
		{
			$value = '';
		}
		else
		{

			$scheme	= $value[ 0 ];
			$url	= $value[ 1 ];
			$value	= $scheme . $url;			
		}
		return $value;
	}
}
