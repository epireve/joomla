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
	<legend><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DAYLIGHT_SAVING' ); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DAYLIGHT_SAVING' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_DAYLIGHT_SAVING_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DAYLIGHT_SAVING' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo $this->lists['dstOffset']; ?>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>