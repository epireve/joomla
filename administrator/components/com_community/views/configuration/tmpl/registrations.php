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
	<legend><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS' ); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_ENABLE_TERMS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_ENABLE_TERMS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_ENABLE_TERMS' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'enableterms' , null , $this->config->get('enableterms') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_TERMS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_TERMS_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_TERMS' ); ?>
					</span>
				</td>
				<td valign="top">
					<textarea name="registrationTerms" cols="30" rows="5"><?php echo $this->config->get('registrationTerms');?></textarea>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_RECAPTCHA_ENABLE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_RECAPTCHA_ENABLE_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_RECAPTCHA_ENABLE' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'recaptcha' , null , $this->config->get('recaptcha') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_RECAPTCHA_SECURE_MODE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_RECAPTCHA_SECURE_MODE_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_RECAPTCHA_SECURE_MODE' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'recaptcha_secure' , null , $this->config->get('recaptcha_secure') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_RECAPTCHA_PUBLIC_KEY' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_RECAPTCHA_PUBLIC_KEY_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_RECAPTCHA_PUBLIC_KEY' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="recaptchapublic" value="<?php echo $this->config->get('recaptchapublic'); ?>" size="35" />
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_RECAPTCHA_PRIVATE_KEY' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_RECAPTCHA_PRIVATE_KEY_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_RECAPTCHA_PRIVATE_KEY' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="recaptchaprivate" value="<?php echo $this->config->get('recaptchaprivate'); ?>" size="35" />
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_ALLOW_PROFILE_DELETION' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_ALLOW_PROFILE_DELETION_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_ALLOW_PROFILE_DELETION' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'profile_deletion' , null , $this->config->get('profile_deletion') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_RECAPTCHA_THEME' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_RECAPTCHA_THEME_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_RECAPTCHA_THEME' ); ?>
					</span>
				</td>
				<td valign="top">
					<select name="recaptchatheme">
						<option value="red"<?php echo $this->config->get('recaptchatheme') == 'red' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_RED');?></option>
						<option value="white"<?php echo $this->config->get('recaptchatheme') == 'white' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_WHITE');?></option>
						<option value="blackglass"<?php echo $this->config->get('recaptchatheme') == 'blackglass' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_BLACKGLASS');?></option>
						<option value="clean"<?php echo $this->config->get('recaptchatheme') == 'clean' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_CLEAN');?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_RECAPTCHA_LANGUAGE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_RECAPTCHA_LANGUAGE_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_RECAPTCHA_LANGUAGE' ); ?>
					</span>
				</td>
				<td valign="top">
					<select name="recaptchalang">
						<option value="en"<?php echo $this->config->get('recaptchalang') == 'en' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_ENGLISH');?></option>
						<option value="nl"<?php echo $this->config->get('recaptchalang') == 'nl' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_DUTCH');?></option>
						<option value="fr"<?php echo $this->config->get('recaptchalang') == 'fr' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_FRENCH');?></option>
						<option value="de"<?php echo $this->config->get('recaptchalang') == 'de' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_GERMAN');?></option>
						<option value="pt"<?php echo $this->config->get('recaptchalang') == 'pt' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_PORTUGUESE');?></option>
						<option value="ru"<?php echo $this->config->get('recaptchalang') == 'ru' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_RUSSIAN');?></option>
						<option value="es"<?php echo $this->config->get('recaptchalang') == 'es' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_SPANISH');?></option>
						<option value="tr"<?php echo $this->config->get('recaptchalang') == 'tr' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_TURKISH');?></option>
						<option value="it"<?php echo $this->config->get('recaptchalang') == 'it' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_ITALIAN');?></option>
					</select>
				</td>
			</tr>

			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_ALLOWEDDOMAINS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_ALLOWEDDOMAINS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_ALLOWEDDOMAINS' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="alloweddomains" value="<?php echo $this->config->get('alloweddomains'); ?>" size="35" />
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_DENIEDDOMAINS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_DENIEDDOMAINS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_DENIEDDOMAINS' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="denieddomains" value="<?php echo $this->config->get('denieddomains'); ?>" size="35" />
				</td>
			</tr>
			
		</tbody>
	</table>
</fieldset>