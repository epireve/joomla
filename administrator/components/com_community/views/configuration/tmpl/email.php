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
	<legend><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EMAIL' ); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EMAIL_HTML_EMAILS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_EMAIL_HTML_EMAILS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EMAIL_HTML_EMAILS' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'htmlemail' , null , $this->config->get('htmlemail') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EMAIL_COPYRIGHT' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_EMAIL_COPYRIGHT'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EMAIL_COPYRIGHT' ); ?>
					</span>
				</td>
				<td valign="top">
					<textarea name="copyrightemail" cols="30" rows="5"><?php echo $this->config->get('copyrightemail');?></textarea>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>