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
	<legend><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_CRONJOB' ); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_CRONJOB_SENDMAIL_PAGELOAD' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_CRONJOB_SENDMAIL_PAGELOAD_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_CRONJOB_SENDMAIL_PAGELOAD'); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'sendemailonpageload' , null , $this->config->get('sendemailonpageload') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_CRONJOB_ARCHIVE_LIMIT' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_CRONJOB_ARCHIVE_LIMIT_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_CRONJOB_ARCHIVE_LIMIT'); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="archive_activity_limit" value="<?php echo $this->config->get('archive_activity_limit' );?>" size="4" />
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>