<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
require_once (COMMUNITY_COM_PATH.DS.'libraries'.DS.'fields'.DS.'profilefield.php');
class CFieldsCountry extends CProfileField
{
	/**
	 * Method to format the specified value for text type
	 **/	 	
	public function getFieldData( $field )
	{
		$value = $field['value'];
		if( empty( $value ) )
			return $value;
		
		return $value;
	}
	
	public function getFieldHTML( $field , $required )
	{
		// If maximum is not set, we define it to a default
		$field->max	= empty( $field->max ) ? 200 : $field->max;

		$class	= ($field->required == 1) ? ' required' : '';
		
		// @since 2.4 detect language and call current language country list
		if (!defined('COUNTRY_LANG_AVAILABLE')) {
		    define('COUNTRY_LANG_AVAILABLE', 1);
		}

		$lang =& JFactory::getLanguage();
		$locale = $lang->getLocale();
		$countryCode = $locale[2];
		$countryLangExtension = "";

		$countryListLanguage =   explode(',', trim(COUNTRY_LIST_LANGUAGE) );
		if(in_array($countryCode,$countryListLanguage)==COUNTRY_LANG_AVAILABLE){
		    $countryLangExtension = "_".$countryCode;
		}
		jimport( 'joomla.filesystem.file' );
		$file	= JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'fields' . DS . 'countries'.$countryLangExtension.'.xml';
	
		if( JFile::exists( $file ) )
		{
                        CFactory::load( 'helpers' , 'string' );
                        
			$contents	= JFile::read( $file );
			$parser		=& JFactory::getXMLParser('Simple');
			$parser->loadFile( $file );
			$document	=& $parser->document;
	
			$element		=& $document->getElementByPath( 'countries' );
			$countries		= $element->children();

			$tooltips		= !empty( $field->tips ) ? ' title="' .  CStringHelper::escape( JText::_( $field->tips ) ) . '"' : '';
			ob_start();
?>
			<select id="field<?php echo $field->id;?>" name="field<?php echo $field->id;?>" class="<?php echo !empty( $field->tips ) ? 'jomNameTips tipRight ' : '';?>select validate-country<?php echo $class;?> inputbox"<?php echo $tooltips;?>>
				<option value=""<?php echo empty($field->value) ? ' selected="selected"' : '';?>><?php echo JText::_('COM_COMMUNITY_SELECT_A_COUNTRY');?></option>
			<?php
			foreach($countries as $country )
			{
				$name	= $country->getElementByPath('name')->data();
				
			?>
				<option value="<?php echo $name;?>"<?php echo ($field->value == $name) ? ' selected="selected"' : '';?>><?php echo JText::_($name);?></option>
			<?php			
			}
			?>
			</select>
			<span id="errfield<?php echo $field->id;?>msg" style="display:none;">&nbsp;</span>
<?php
			$html	= ob_get_contents();
			ob_end_clean();
		}
		else
		{
			$html	= JText::_('Countries list not found');
		}

		return $html;
	}
	
	public function isValid( $value , $required )
	{
		if( $value === 'selectcountry' && $required )
			return false;
			
		return true;
	}

}