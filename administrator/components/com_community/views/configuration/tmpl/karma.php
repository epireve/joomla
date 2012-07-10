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
<fieldset class="adminform" id="com_karmaPoints">
	<legend><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_KARMA' ); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="20" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_KARMA_ENABLE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_KARMA_ENABLE_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_KARMA_ENABLE' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'enablekarma' , null ,  $this->config->get('enablekarma') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_KARMA_DEFAULT_POINTS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_KARMA_DEFAULT_POINTS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_KARMA_DEFAULT_POINTS' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="defaultpoint" value="<?php echo $this->config->get('defaultpoint');?>" size="40" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_KARMA_SMALLER_THAN' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_KARMA_POINTS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_KARMA_SMALLER_THAN' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo $this->getKarmaHTML( 'point0' , $this->config->get('point0'), true );?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_KARMA_GREATER_THAN' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_KARMA_POINTS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_KARMA_GREATER_THAN' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo $this->getKarmaHTML( 'point1' , $this->config->get('point1') , false, 'point0');?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_KARMA_GREATER_THAN' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_KARMA_POINTS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_KARMA_GREATER_THAN' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo $this->getKarmaHTML( 'point2' , $this->config->get('point2') );?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_KARMA_GREATER_THAN' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_KARMA_POINTS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_KARMA_GREATER_THAN' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo $this->getKarmaHTML( 'point3' , $this->config->get('point3') );?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_KARMA_GREATER_THAN' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_KARMA_POINTS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_KARMA_GREATER_THAN' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo $this->getKarmaHTML( 'point4' , $this->config->get('point4') );?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_KARMA_GREATER_THAN' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_KARMA_POINTS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_KARMA_GREATER_THAN' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo $this->getKarmaHTML( 'point5' , $this->config->get('point5') );?>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>