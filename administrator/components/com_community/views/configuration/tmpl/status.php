<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<fieldset class="adminform">
	<legend><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_USER_STATUS' ); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_USER_STATUS_CHARACTER_LIMIT' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_USER_STATUS_CHARACTER_LIMIT_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_USER_STATUS_CHARACTER_LIMIT'); ?>
					</span>
				</td>
				<td valign="top">
					<div><input type="text" name="statusmaxchar" value="<?php echo $this->config->get('statusmaxchar'); ?>" size="5" /><?php echo JText::_('COM_COMMUNITY_CHARACTERS');?></div>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>