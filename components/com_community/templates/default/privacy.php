<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 */
defined('_JEXEC') or die();
?>
<div class="<?php echo $classAttribute;?>">
	<select name="<?php echo $nameAttribute;?>" class="js_PrivacySelect js_PriDefault">
		<?php if( isset( $access[ 'public'] ) && $access['public'] === true ){ ?>
		<option class="js_PriOption js_Pri-0" value="0"<?php echo $selectedAccess == 0 ? ' selected="selected"' : '';?>><?php echo JText::_('COM_COMMUNITY_PRIVACY_PUBLIC');?></option>
		<?php } ?>
		
		<?php if( isset( $access[ 'members'] ) && $access['members'] === true ){ ?>
		<option class="js_PriOption js_Pri-20" value="20"<?php echo $selectedAccess == 20 ? ' selected="selected"' : '';?>><?php echo JText::_('COM_COMMUNITY_PRIVACY_SITE_MEMBERS');?></option>
		<?php } ?>
		
		<?php if( isset( $access[ 'friends'] ) && $access['friends'] === true ){ ?>
		<option class="js_PriOption js_Pri-30" value="30"<?php echo $selectedAccess == 30 ? ' selected="selected"' : '';?>><?php echo JText::_('COM_COMMUNITY_PRIVACY_FRIENDS');?></option>
		<?php } ?>
		
		<?php if( isset( $access[ 'self'] ) && $access['self'] === true ){ ?>
		<option class="js_PriOption js_Pri-40" value="40"<?php echo $selectedAccess == 40 ? ' selected="selected"' : '';?>><?php echo JText::_('COM_COMMUNITY_PRIVACY_ME');?></option>
		<?php } ?>
	</select>
	<div class="clr"></div>
</div>
