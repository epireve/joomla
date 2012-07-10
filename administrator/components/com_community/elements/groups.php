<?php
/**
 * @category	Elements
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );

class JElementGroups extends JElement
{
	var	$_name = 'Groups';

	public function fetchElement($name, $value, &$node, $control_name)
	{
		$lang =& JFactory::getLanguage();
		$lang->load( 'com_community', JPATH_ROOT);
		
		$model		= CFactory::getModel( 'Groups' );
		$groups		= $model->getAllGroups();
		$fieldName	= $control_name.'['.$name.']';

	    ob_start();
		?>
		<select name="<?php echo $fieldName;?>">
			<?php foreach( $groups as $group ){ ?>
			<option value="<?php echo $group->id;?>"<?php echo $value == $group->id ? ' selected="selected"' : '';?>><?php echo $group->name;?></option>
			<?php } ?>
		</select>
		<?php
		$html   = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
}
